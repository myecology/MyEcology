<?php
namespace api\modules\v1\controllers;

use api\controllers\APIFormat;
use api\controllers\RongCloud;
use api\models\User;
use api\models\UserFriend;
use Yii;
use yii\data\Pagination;
use yii\helpers\Json;

/**
 * 用户操作
 */
class FriendController extends BaseController
{
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];
    /**
     * 好友列表/申请列表
     *
     * @param [type] $type
     * @return void
     */
    public function actionIndex($type)
    {
        switch ($type) {
            case -1:
                $data = UserFriend::find()
                    ->with(['inUser'])
                    ->where(['to_userid' => Yii::$app->user->identity->userid, 'status' => 0]);
                break;
            default:
                $data = UserFriend::find()
                    ->with(['toUser'])
                    ->where([
                        'AND',
                        ['=', 'in_userid', Yii::$app->user->identity->userid],
                        ['>', 'status', 0],
                    ]);
                break;
        }

        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'pageSizeLimit' => [1, 100],
            'totalCount' => $data->count(),
        ]);
        $pagination->validatePage = false;

        $data = $data->offset($pagination->offset)
            ->limit($pagination->limit)
            ->asArray()
            ->orderBy('id desc')
            ->all();

        $_meta = [
            'totalCount' => $pagination->totalCount,
            'pageCount' => $pagination->pageCount,
            'currentPage' => $pagination->getPage() + 1,
            'perPage' => $pagination->getPageSize(),
        ];

        return APIFormat::success(['items' => $data, '_meta' => $_meta]);
    }

    /**
     * 匹配手机通讯录
     *
     * @return void
     */
    public function actionMatch()
    {
        //  所有好友
        $phone = (array) Yii::$app->request->post('phone');
        $nickname = (array) Yii::$app->request->post('nickname');
        $data = [];

        if (is_array($phone)) {
            //  所有朋友
            $friends = UserFriend::find()
                ->with(['toUser'])
                ->where([
                    'AND',
                    ['=', 'in_userid', Yii::$app->user->identity->userid],
                    ['>=', 'status', 0],
                ])->asArray()
                ->indexBy('toUser.username')
                ->all();

            foreach ($phone as $key => $val) {

                $user = User::findByUsername($val);

                //  下标不存在退出
                if(!isset($nickname[$key])){
                    break;
                }

                if (isset($friends[$val])) {
                    $data[] = [
                        'phone' => $val,
                        'phone_nickname' => $nickname[$key],
                        'userid' => $friends[$val]['toUser']['userid'],
                        'iec_nickname' => $friends[$val]['toUser']['nickname'],
                        'remarke' => $friends[$val]['remark'],
                        'headimgurl' => $friends[$val]['toUser']['headimgurl'],
                        'status' => $friends[$val]['status'] == 0 ? 20 : 30,
                    ];
                } else {
                    if ($user) {
                        $data[] = [
                            'phone' => $val,
                            'phone_nickname' => $nickname[$key],
                            'userid' => $user->userid,
                            'iec_nickname' => $user->nickname,
                            'remarke' => '',
                            'headimgurl' => $user->headimgurl,
                            'status' => 10,
                        ];
                    } else {
                        $data[] = [
                            'phone' => $val,
                            'phone_nickname' => $nickname[$key],
                            'userid' => '',
                            'iec_nickname' => '',
                            'remarke' => '',
                            'headimgurl' => '',
                            'status' => 0,
                        ];
                    }
                }
            }
        }

        return APIFormat::success($data);
    }

    /**
     * 添加好友
     *
     * @return void
     */
    public function actionAdd()
    {
        $transaction =Yii::$app->db->beginTransaction();
        try {
            if ($toUser = Yii::$app->request->post('to_userid')) {

                //  如果数据库已经存在 不在保存数据库
                $model = UserFriend::find()->where(['in_userid' => Yii::$app->user->identity->userid, 'to_userid' => $toUser])->one();
                if ($model && $model->status == 1) {
                    return APIFormat::error(4010, '已经是好友了');
                }

                if (!$model) {
                    $model = new UserFriend();
                    $model->scenario = 'insert';
                    $model->in_userid = Yii::$app->user->identity->userid;
                    $model->to_userid = $toUser;
                } else {
                    $model->scenario = 'update';
                }

                //  是否开启了好友验证
                $isFriend = User::find()->where(['userid' => $toUser, 'status' => user::STATUS_ACTIVE, 'friend' => 1])->one();
                if ($isFriend) {
                    //  直接添加好友
                    $model->status = 1;
                    $type = 1;
                } else {
                    //  需要验证
                    $model->status = 0;
                    $type = 0;
                }

                if (false === $model->save()) {
                    return APIFormat::error(4010, $model->errors);
                }

                if ($model->status == 0) {
                    $content = [
                        "operation" => "Request",
                        "sourceUserId" => $model->in_userid,
                        "targetUserId" => $model->to_userid,
                    ];
                    RongCloud::getInstance()->sendPrivateMessage($model->in_userid, $model->to_userid, Json::encode($content));
                } else {
                    $model->completed();
                }

                $transaction->commit();
                return APIFormat::success([
                    'type' => $type,
                    'userid' => $model->toUser->userid,
                    'initials' => $model->toUser->initials,
                    'username' => $model->toUser->username,
                    'iecid' => $model->toUser->iecid,
                    'nickname' => $model->toUser->nickname,
                    'headimgurl' => $model->toUser->headimgurl,
                    'created_at' => $model->updated_at,
                ]);
            }
            return APIFormat::error(4010, 'Not Found to_userid');

        } catch (ErrorException $e) {
            $transaction->rollBack();
            return APIFormat::error(4010, $e->getMessage());
        }
    }

    /**
     * 确认好友
     *
     * @return void
     */
    public function actionCompleted()
    {
        if ($id = Yii::$app->request->post('id')) {
            $model = UserFriend::find()->where(['id' => $id, 'status' => 0])->one();

            if ($model) {
                $model->scenario = 'update';
                // $lastStatus = $model->status;
                $model->status = 1;

                if (false !== $model->save()) {

                    //  发送融云推送系统消息
                    // $content = [
                    //     "operation" => "AcceptResponse",
                    //     "sourceUserId" => $model->in_userid,
                    //     "targetUserId" => $model->to_userid,
                    // ];

                    //  如果是
                    // if ($lastStatus == -1) {
                    //     RongCloud::getInstance()->removeBlacklist($model->in_userid, $model->to_userid);
                    //     RongCloud::getInstance()->removeBlacklist($model->to_userid, $model->in_userid);
                    // }

                    // $content = [
                    //     'message' => '对方已同意你的好友请求，现在可以开始聊天了。',
                    // ];
                    // $content2 = [
                    //     'message' => '你已添加了对方，现在可以开始聊天了。',
                    // ];
                    // RongCloud::getInstance()->sendInfoMessage($model->in_userid, $model->to_userid, Json::encode($content2));
                    // RongCloud::getInstance()->sendInfoMessage($model->to_userid, $model->in_userid, Json::encode($content));
                    // RongCloud::getInstance()->removeBlacklist($model->in_userid, $model->to_userid);
                    // RongCloud::getInstance()->removeBlacklist($model->to_userid, $model->in_userid);

                    //  查询是否黑名单列表
                    // $inUsers = RongCloud::getInstance()->queryBlacklist($model->in_userid);
                    // $toUsers = RongCloud::getInstance()->queryBlacklist($model->to_userid);
                    // if(in_array($model->to_userid, $inUsers)){
                    //     RongCloud::getInstance()->removeBlacklist($model->in_userid, $model->to_userid);
                    // }
                    // if(in_array($model->in_userid, $toUsers)){
                    //     RongCloud::getInstance()->removeBlacklist($model->to_userid, $model->in_userid);
                    // }

                    $model->completed();
                    return APIFormat::success([
                        'userid' => $model->toUser->userid,
                        'initials' => $model->toUser->initials,
                        'username' => $model->toUser->username,
                        'iecid' => $model->toUser->iecid,
                        'nickname' => $model->toUser->nickname,
                        'headimgurl' => $model->toUser->headimgurl,
                        'updated_at' => $model->updated_at,
                    ]);
                }
                return APIFormat::error(4011, $model->errors);
            }
        }
        return APIFormat::error(4011, 'Not Found');
    }

    /**
     * 一键添加好友
     *
     * @return void
     */
    public function actionCompleteds()
    {
        $friendIDS = Yii::$app->request->post('ids');
        if(!$friendIDS || !is_array($friendIDS)){
            return APIFormat::error(4022, '参数错误');
        }

        foreach($friendIDS as $id){
            $model = UserFriend::find()->where(['id' => $id, 'status' => 0])->one();

            if($model){
                $model->scenario = 'update';
                $model->status = 1;

                if (false === $model->save()) {
                    return APIFormat::error(4022);
                }
                $model->completed();
            }
        }
        return APIFormat::success(true);
    }

    /**
     * 修改备注
     *
     * @return void
     */
    public function actionUpdate()
    {
        $userid = Yii::$app->request->post('userid');
        $remark = Yii::$app->request->post('remark');

        $msg = null;
        if ($userid && $remark) {
            $model = UserFriend::find()->where(['in_userid' => Yii::$app->user->identity->userid, 'to_userid' => $userid, 'status' => 1])->one();
            if ($model) {
                $model->scenario = 'update';
                $model->remark = $remark;
                if (false !== $model->save()) {
                    return APIFormat::success([
                        'status' => $model->status,
                        'remark' => $model->remark,
                        'updated_at' => $model->updated_at,
                    ]);
                }
                $msg = $model->errors;
            }
        }
        return APIFormat::error(4012, $msg);
    }

    /**
     * 删除朋友
     *
     * @return void
     */
    public function actionDelete()
    {
        $userid = Yii::$app->request->post('userid');
        $model = UserFriend::find()->where(['in_userid' => Yii::$app->user->identity->userid, 'to_userid' => $userid])->one();

        $msg = null;
        if ($model) {

            //  如果自己被对方删除，那么直接删除对象
            if($model->status == 2){
                if(false !== $model->delete()){
                    return APIFormat::success([
                        'status' => -1,
                        'updated_at' => time(),
                    ]);
                }else{
                    return APIFormat::error(4013, '删除好友失败');
                }
            }   


            $model->scenario = 'update';
            $model->status = -1;

            $toModel = UserFriend::find()->where(['in_userid' => $userid, 'to_userid' => Yii::$app->user->identity->userid])->one();
            $toModel->scenario = 'update';
            $toModel->status = 2;

            if (false !== $model->save() && false !== $toModel->save()) {

                //  删除进入黑名单
                RongCloud::getInstance()->addBlacklist($model->in_userid, $model->to_userid);
                RongCloud::getInstance()->addBlacklist($model->to_userid, $model->in_userid);

                return APIFormat::success([
                    'status' => $model->status,
                    'updated_at' => $model->updated_at,
                ]);
                $msg = $model->errors;
            }
        }

        return APIFormat::error(4013, $msg);
    }

}
