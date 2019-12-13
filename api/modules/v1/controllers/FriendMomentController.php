<?php
namespace api\modules\v1\controllers;

use api\controllers\APIFormat;
use api\models\FriendMoment;
use api\models\FriendMomentLike;
use api\models\FriendMomentMessage;
use api\models\FriendMomentReply;
use api\models\FriendMomentUser;
use api\models\UserFriend;
use Yii;
use yii\data\Pagination;
use yii\web\UploadedFile;

/**
 * 朋友圈
 */
class FriendMomentController extends BaseController
{

    /**
     * 单个朋友圈消息信息
     *
     * @param [type] $id
     * @return void
     */
    public function actionOne($id)
    {
        $friendUserArray = UserFriend::find()->select("to_userid")->where(['in_userid' => Yii::$app->user->identity->userid, 'status' => 1])->column();
        $friendUserArray[] = Yii::$app->user->identity->userid;

        $data = [];
        $moment = FriendMoment::find()->with(['user', 'image', 'momentLike', 'isMomentLike', 'momentReply'])->where(['id' => $id, 'status' => 10])->asArray()->one();
        if ($moment) {

            //  处理点赞数据
            $momentLike = [];
            foreach ($moment['momentLike'] as $like) {
                if (in_array($like['userid'], $friendUserArray)) {
                    $momentLike[] = $like;
                }
            }

            //  处理回复数据
            $momentReply = [];
            foreach ($moment['momentReply'] as $reply) {
                if (in_array($reply['in_userid'], $friendUserArray)) {
                    $momentReply[] = $reply;
                }
            }
            $moment['momentLike'] = $momentLike;
            $moment['momentReply'] = $momentReply;

            $data[] = $moment;
        }

        return APIFormat::success($data);
    }

    /**
     * 朋友圈列表
     *
     * @return void
     */
    public function actionIndex()
    {
        $moments = FriendMomentUser::find()->where(['userid' => Yii::$app->user->identity->userid]);
        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'pageSizeLimit' => [1, 100],
            'totalCount' => $moments->count(),
        ]);
        $pagination->validatePage = false;

        $moments = $moments->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        //  获取当前人物朋友数组
        $friendUserArray = UserFriend::find()->select("to_userid")->where(['in_userid' => Yii::$app->user->identity->userid, 'status' => 1])->column();
        $friendUserArray[] = Yii::$app->user->identity->userid;
        $data = [];
        foreach ($moments as $moment) {
            $moment = FriendMoment::find()->with(['user', 'image', 'momentLike', 'isMomentLike', 'momentReply'])->where(['id' => $moment['moment_id'], 'status' => 10])->asArray()->one();
            if ($moment) {

                //  处理点赞数据
                $momentLike = [];
                foreach ($moment['momentLike'] as $like) {
                    if (in_array($like['userid'], $friendUserArray)) {
                        $momentLike[] = $like;
                    }
                }

                //  处理回复数据
                $momentReply = [];
                foreach ($moment['momentReply'] as $reply) {
                    if (in_array($reply['in_userid'], $friendUserArray)) {
                        $momentReply[] = $reply;
                    }
                }
                $moment['momentLike'] = $momentLike;
                $moment['momentReply'] = $momentReply;

                $data[] = $moment;
            }
        }

        $_meta = [
            'totalCount' => $pagination->totalCount,
            'pageCount' => $pagination->pageCount,
            'currentPage' => $pagination->getPage() + 1,
            'perPage' => $pagination->getPageSize(),
        ];

        return APIFormat::success(['items' => $data, '_meta' => $_meta]);
    }

    /**
     * 热门朋友圈
     *
     * @return void
     */
    public function actionHot()
    {
        $endtime = time();
        $statime = $endtime - 86400 * 2;
        $data = FriendMoment::find()->with(['user', 'image', 'isMomentLike'])->where(['between', 'created_at', $statime, $endtime]);

        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'pageSizeLimit' => [1, 100],
            'totalCount' => $data->count(),
        ]);
        $pagination->validatePage = false;

        $data = $data->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['hot' => SORT_DESC, 'created_at' => SORT_DESC])
            ->asArray()
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
     * 点赞朋友圈
     *
     * @return void
     */
    public function actionLike()
    {
        $momentID = Yii::$app->request->post('momentid', 0);
        $type = Yii::$app->request->post('type', 0);
        $msg = null;
        if (FriendMoment::findOne($momentID) && $type) {

            $model = FriendMomentLike::find()
                ->where([
                    'momentid' => $momentID,
                    'userid' => Yii::$app->user->identity->userid,
                    'type' => $type,
                ])->one();

            if ($model) {
                //  更新点赞的状态

                if ($model->type == 1) {
                    $model->status = $model->status === 10 ? 0 : 10;
                    if (false !== $model->save()) {
                        return APIFormat::success([
                            "type" => $model->type,
                            "amount" => $model->amount,
                            "status" => $model->status,
                        ]);
                    }
                }
                return APIFormat::error(4030, $msg);
            } else {
                //  点赞 AND 币赞

                $model = new FriendMomentLike();
                if ($type == 1) {
                    $model->scenario = 'default';
                    $model->status = 10;
                    $model->amount = 0;
                } else {
                    $model->scenario = 'currency';
                    $model->status = 10;
                }

                $model->setAttributes(Yii::$app->request->post());

                if (false !== $model->save()) {
                    FriendMoment::sendMomentMessage($momentID, $model, 10);
                    return APIFormat::success([
                        "type" => $model->type,
                        "amount" => $model->amount,
                        "status" => $model->status,
                    ]);
                }

                $msg = $model->errors;
            }
        }
        return APIFormat::error(4030, $msg);
    }

    /**
     * 朋友圈回复
     *
     * @return void
     */
    public function actionReply()
    {
        $momentID = Yii::$app->request->post('momentid', 0);
        $msg = null;

        if (FriendMoment::findOne($momentID)) {
            $model = new FriendMomentReply();
            $model->setAttributes(Yii::$app->request->post());

            if (false !== $model->save()) {
                FriendMoment::sendMomentMessage($momentID, $model, 20);
                return APIFormat::success(['id'=>$model->id]);
            }
            $msg = $model->errors;
        }
        return APIFormat::error(4040, $msg);
    }

    /**
     * 朋友圈回复删除
     *
     * @return void
     */
    public function actionDeleteReply()
    {
        $replyID = Yii::$app->request->post('id', 0);
        $msg = null;

        $model = FriendMomentReply::findOne($replyID);
        if ($model) {
            $model->status = 0;
            if (false !== $model->save()) {
                return APIFormat::success(true);
            }
            $msg = $model->errors;
        }
        return APIFormat::error(4041, $msg);
    }

    /**
     * 添加朋友圈
     *
     * @return void
     */
    public function actionAdd()
    {
        try{
            $msg = null;
            if ($type = Yii::$app->request->post('type')) {
                $model = new FriendMoment();
                switch ($type) {
                    case 1:
                        $model->scenario = 'Text';
                        break;
                    case 2:
                        $model->scenario = 'LinkText';
                        break;
                    case 3:
                        $model->scenario = 'ImgText';
                        $model->images = UploadedFile::getInstancesByName('images');
                        break;
                }
                $model->setAttributes(Yii::$app->request->post());
                if (false !== $model->save()) {
                    return APIFormat::success(true);
                }
                $msg = $model->errors;
            }
            return APIFormat::error(4020, $msg);
        }catch (\Exception $exception){
            return APIFormat::error($exception->getCode(), $exception->getMessage());
        }
    }

    /**
     * 删除朋友圈
     *
     * @return void
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->post('id', 0);
        $model = FriendMoment::find()->where(['id' => $id, 'userid' => Yii::$app->user->identity->userid])->one();

        if ($model) {
            $model->scenario = 'delete';
            $model->status = 0;
            if (false !== $model->save()) {
                return APIFormat::success(true);
            }
        }

        return APIFormat::error(4021);
    }

    /**
     * 朋友圈消息列表
     *
     * @param [type] $type
     * @return void
     */
    public function actionNewMessage($type)
    {
        switch ($type) {
            //  最新消息
            case 10:
                $dateTime = Yii::$app->request->get('datetime');
                $data = FriendMomentMessage::find()->with(['image', 'inUser', 'toUser'])->where(['AND',
                    ['=', 'userid', Yii::$app->user->identity->userid],
                    ['>=', 'created_at', $dateTime],
                ]);
                break;
            case 20:
                //  显示更多
                $id = Yii::$app->request->get('id');
                $data = FriendMomentMessage::find()->with(['image', 'inUser', 'toUser'])->where(['AND',
                    ['=', 'userid', Yii::$app->user->identity->userid],
                    ['<', 'id', $id],
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
            ->orderBy(['created_at' => SORT_DESC])
            ->asArray()
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
     * 删除朋友圈消息
     *
     * @return void
     */
    public function actionClearMessage()
    {
        FriendMomentMessage::deleteAll(['userid' => Yii::$app->user->identity->userid]);
        return APIFormat::success(true);
    }


    /**
     * 返回朋友圈最后一个ID
     *
     * @return void
     */
    public function actionLast()
    {
        // $lastOne = Yii::$app->redis->zrevrange(Yii::$app->user->identity->userid, 0, 0);
        // $momentID = isset($lastOne[0]) ? $lastOne[0] : 0;
        $model = FriendMomentUser::find()->where(['userid' => Yii::$app->user->identity->userid])->orderBy('id desc')->one();

        $momentID = $model ? $model['moment_id'] : 0;
        return APIFormat::success($momentID);
    }
}
