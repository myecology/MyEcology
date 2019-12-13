<?php
namespace api\modules\v1\controllers;

use api\controllers\APIFormat;
use api\models\Group;
use api\models\GroupUser;
use api\controllers\RongCloud;
use Yii;
use yii\helpers\Json;
use yii\data\Pagination;

/**
 * 群组成员
 */
class GroupUserController extends BaseController
{
    /**
     * 群成员
     *
     * @param [type] $groupid
     * @return void
     */
    public function actionIndex($groupid)
    {
        $type = (int) Yii::$app->request->get('type', 0);
        switch ($type) {
            case 1:
                //  群主
                $where = ['groupid' => $groupid, 'status' => 1, 'permission' => 20];
                break;
            case 2:
                //  管理员
                $where = ['groupid' => $groupid, 'status' => 1, 'permission' => 10];
                break;
            case 3:
                //  群主+管理员
                $where = ['AND', ['=', 'groupid', $groupid], ['=', 'status', 1], ['>=', 'permission', 10]];
                break;
            case 4:
                //  群成员
                $where = ['groupid' => $groupid, 'status' => 1, 'permission' => 1];
                break;
            default:
                //  群主 + 管理员 + 群员
                $where = ['groupid' => $groupid, 'status' => 1];
                break;
        }

        $data = GroupUser::find()->with(['user'])->where($where);
        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'pageSizeLimit' => [1, 3000],
            'totalCount' => $data->count(),
        ]);
        $pagination->validatePage = false;

        $data = $data->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy('permission desc')
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
     *  拉取朋友
     *
     * @return void
     */
    public function actionAdd()
    {
        $groupID = Yii::$app->request->post('groupid');
        $userid = Yii::$app->request->post('userid');

        //  
        $selfGroupInfo = GroupUser::find()->where(['groupid' => $groupID, 'userid' => Yii::$app->user->identity->userid, 'status' => 1])->one();
        if(!$selfGroupInfo){
            return APIFormat::error(4051, '查不到此群');
        }
        $groupModel = Group::findOne(['groupid' => $groupID]);
        //  判断是否是禁止拉人入群
        if($groupModel->is_pull == 0){
            if(!$this->isPermission($groupID)){
                return APIFormat::error(4051, '禁止拉取人');
            }
        }

        if ($groupID && $userid) {
            GroupUser::addUser($groupID, (array) $userid);
            return APIFormat::success(true);
        }
        return APIFormat::error(4051, '添加参数不正确');
    }

    /**
     * 申请添加群
     *
     * @return void
     */
    public function actionRequestAdd()
    {
        $model = new GroupUser();
        $model->scenario = 'insert';
        $model->userid = Yii::$app->user->identity->userid;
        $model->status = 0;
        $model->setAttributes(Yii::$app->request->post());

        if (false === $model->save()) {
            return APIFormat::error(4053, $model->errors);
        }
        return APIFormat::success(true);
    }

    /**
     * 申请列表
     *
     * @return void
     */
    public function actionRequestList()
    {
        $groupID = Yii::$app->request->post('groupid');

        if ($groupID && $this->isPermission($groupID)) {

            $data = GroupUser::find()->with(['user'])->where(['groupid' => $groupid, 'status' => 0]);
            $pagination = new Pagination([
                'defaultPageSize' => 10,
                'pageSizeLimit' => [1, 100],
                'totalCount' => $data->count(),
            ]);

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
        return APIFormat::error(4054);
    }

    /**
     * 群加入
     *
     * @return void
     */
    public function actionJoin()
    {
        $groupID = Yii::$app->request->post('groupid');
        $msg = null;
        try {
            if ($groupID) {
                $groupModel = Group::findOne(['groupid' => $groupID]);

                //  判断是否是禁止拉人入群
                if($groupModel->is_pull == 0){
                    if(!$this->isPermission($groupID)){
                        return APIFormat::error(4051, '禁止入群');
                    }
                }
                GroupUser::addUser($groupID, (array) [Yii::$app->user->identity->userid]);
                return APIFormat::success(true);
            }
        } catch (\Throwable $th) {
            $msg = $th->getMessage();
        }
        return APIFormat::error(4051, $msg);
    }

    /**
     * 同意加入群
     *
     * @return void
     */
    public function actionAgree()
    {
        $groupID = Yii::$app->request->post('groupid');
        $userid = Yii::$app->request->post('userid');

        if ($groupID && $userid && $this->isPermission($groupID)) {
            $model = GroupUser::find()
                ->where(['groupid' => $groupID, 'userid' => $userid, 'status' => 0])
                ->one();

            if ($model) {
                $model->scenario = 'update';
                $model->status = 1;
                if (false !== $model->save()) {
                    return APIFormat::success(true);
                }
            }
        }

        return APIFormat::error(4055);
    }


    /**
     * 更新群成员信息
     *
     * @return void
     */
    public function actionUpdate()
    {
        $groupID = Yii::$app->request->post('groupid');
        $nickname = Yii::$app->request->post('nickname');

        if($groupID && $nickname){
            $model = GroupUser::find()->where(['groupid' => $groupID, 'userid' => Yii::$app->user->identity->userid, 'status' => 1])->one();
            if($model){
                $model->nickname = $nickname;
                $model->scenario = 'update';

                if(false != $model->save()){

                    $content = [
                        'groupId' => $groupID,
                        'extra' => Yii::$app->user->identity->userid,
                        'nickname' => $nickname,
                    ];
                    $users = GroupUser::find()->select("userid")->where(['groupid' => $groupID, 'status' => 1])->column();
                    RongCloud::getInstance()->sendPrivateGrpReNameMessage(Yii::$app->user->identity->userid, $users, Json::encode($content));

                    return APIFormat::success(true);
                }
            }
        }
        return APIFormat::error(4061);
    }


    /**
     * 设置管理员
     *
     * @return void
     */
    public function actionSetAdmin()
    {
        $groupID = Yii::$app->request->post('groupid');
        $userid = (array) Yii::$app->request->post('userid');

        $adminCount = GroupUser::find()->where(['groupid' => $groupID, 'permission' => 10])->count();
        $count = $adminCount + count($userid);

        if ($count <= 3 && $groupID && $userid && $this->isPermission($groupID, 20)) {

            $data = GroupUser::find()
                ->where(['AND', ['=', 'groupid', $groupID], ['in', 'userid', $userid], ['=', 'status', 1]])
                ->all();
            if ($data) {
                foreach ($data as $model) {
                    $model->scenario = 'update';
                    $model->permission = 10;
                    if (false === $model->save()) {
                        return APIFormat::error(4056, '设置管理员失败');
                    }
                }
                return APIFormat::success(true);
            }
        }
        return APIFormat::error(4056);
    }

    /**
     * 取消管理员权限
     *
     * @return void
     */
    public function actionDropAdmin()
    {
        $groupID = Yii::$app->request->post('groupid');
        $userid = Yii::$app->request->post('userid');

        if ($groupID && $userid && $this->isPermission($groupID, 20)) {
            $data = GroupUser::find()
                ->where(['AND', ['=', 'groupid', $groupID], ['in', 'userid', (array) $userid], ['=', 'status', 1]])
                ->all();
            if ($data) {
                foreach ($data as $model) {
                    $model->scenario = 'update';
                    $model->permission = 1;
                    if (false === $model->save()) {
                        return APIFormat::error(4059, '去考管理员失败');
                    }
                }
                return APIFormat::success(true);
            }
        }
        return APIFormat::error(4059);
    }

    /**
     * 踢出群成员
     *
     * @return voidå
     */
    public function actionOut()
    {
        $groupID = Yii::$app->request->post('groupid');
        $userid = (array) Yii::$app->request->post('userid');

        if ($groupID && $userid && $this->isPermission($groupID)) {
            $groupModel = Group::findOne(['groupid' => $groupID]);
            $data = GroupUser::find()
                ->where(['AND', ['=', 'groupid', $groupID], ['in', 'userid', $userid], ['=', 'status', 1]])
                ->all();

            if ($data) {
                /**
                 * @var $model GroupUser
                 */
                foreach ($data as $model) {
                    if($model->permission == 20){
                        continue;
                    }

                    $model->status = -1;
                    $model->permission = 1;
                    if (false == $model->save(false)) {
                        return APIFormat::error(4057, '踢出成员失败');
                    }
                    $groupModel->nums -= 1;
                    //  群发送已被踢出群
                    // $content = [
                    //     'operatorUserId' => $groupModel->createid,
                    //     'operation' => 'Kicked',
                    //     'data' => [
                    //         'operatorNickname' => $model->user->nickname,
                    //         'targetGroupName' => $model->group->name,
                    //     ],
                    //     'message' => $model->user->nickname . '已被移出此群',
                    // ];
                    // GroupUser::cloudGroupOperating($groupModel->createid, $groupID, Json::encode($content));
                }

                // //  发送群系统消息
                // $content = [
                //     'operatorUserId' => $groupModel->createid,
                //     'operation' => 'myKickYou',
                //     'data' => [
                //         'operatorNickname' => $groupModel->name,
                //         'targetGroupName' => $groupModel->groupid,
                //     ],
                //     'message' => $groupModel->groupid,
                //     'extra' => $groupModel->groupid,
                // ];

                // //  发送群消息
                // RongCloud::getInstance()->sendPrivateGrpMessage($groupModel->createid, $userid, Json::encode($content));
                $groupModel->save(false);
                //  删除群成员
                GroupUser::cloudGroupDeleteUser($userid, $groupID);
                return APIFormat::success(true);
            }
        }
        return APIFormat::error(4057);
    }

    /**
     * 退出群
     *
     * @return void
     */
    public function actionDelete()
    {
        $groupID = Yii::$app->request->post('groupid');
        /**
         * @var GroupUser $model
         */
        $model = GroupUser::find()
            ->where(['groupid' => $groupID, 'userid' => Yii::$app->user->identity->userid, 'status' => 1])
            ->one();

        if ($model) {
            $groupModel = Group::findOne(['groupid' => $groupID]);
            $model->status = -1;
            $model->permission = 1;
            if (false !== $model->save(false)) {
                $groupModel->nums -= 1;
                $groupModel->save(false);
                GroupUser::cloudGroupDeleteUser([$model->userid], $groupID);
                $content = [
                    'operatorUserId' => $groupModel->createid,
                    'operation' => 'Quit',
                    'data' => [
                        'operatorNickname' => Yii::$app->user->identity->nickname,
                        'targetGroupName' => $model->group->name,
                    ],
                    'message' => Yii::$app->user->identity->nickname . '退出群聊',
                ];
                GroupUser::cloudGroupOperating($model->userid, $groupID, Json::encode($content));
                return APIFormat::success(true);
            }
        }
        return APIFormat::error(4052);
    }

    /**
     * 转让群主
     *
     * @return void
     */
    public function actionTransfer()
    {
        $groupID = Yii::$app->request->post('groupid');
        $userid = Yii::$app->request->post('userid');

        if ($groupID && $userid && $this->isPermission($groupID, 20)) {

            $transaction = Yii::$app->db->beginTransaction();
            try {

                $inUser = GroupUser::find()->where(['groupid' => $groupID, 'userid' => Yii::$app->user->identity->userid, 'permission' => 20, 'status' => 1])->one();
                $toUser = GroupUser::find()->where(['groupid' => $groupID, 'userid' => $userid, 'status' => 1])->one();
                $group = Group::find()->where(['groupid' => $groupID])->one();

                $inUser->scenario = 'update';
                $toUser->scenario = 'update';
                $group->scenario = 'update';
                $inUser->permission = 1;
                $toUser->permission = 20;
                $group->createid = $userid;

                if (false !== $inUser->save() && false !== $toUser->save() && $group->save()) {
                    $transaction->commit();
                    return APIFormat::success(true);
                }

            } catch (\Exception $e) {
                $transaction->rollBack();
                return APIFormat::error(4060);
            }
        }
        return APIFormat::error(4060);
    }

    /**
     * 开启禁言/关闭禁言
     *
     * @return void
     */
    public function actionBan()
    {
        $groupID = Yii::$app->request->post('groupid');
        $userid = Yii::$app->request->post('userid');
        $minute = Yii::$app->request->post('minute', 0);
        $minute = 1;
        $msg = null;

        if($groupID && $userid && $this->isPermission($groupID)){

            $groupUser = GroupUser::find()->where(['groupid' => $groupID, 'userid' => $userid, 'status' => 1])->one();
            if($groupUser){
                if($groupUser->is_ban == GroupUser::IS_BAN_ACTIVE){
                    $groupUser->is_ban = GroupUser::IS_BAN_DELETED;
                    $msg = '取消禁言失败';
                }else{
                    $groupUser->is_ban = GroupUser::IS_BAN_ACTIVE;
                    $msg = '添加禁言失败';
                }

                if(false !== $groupUser->save(false)){
                    if($groupUser->is_ban == GroupUser::IS_BAN_ACTIVE){
                        RongCloud::getInstance()->addGroupGagUser($userid, $groupID, $minute);
                    }else{
                        RongCloud::getInstance()->rollBackGroupGagUser($userid, $groupID);
                    }
                    return APIFormat::success($groupUser->is_ban);
                }
            }
        }
        return APIFormat::error(4065, $msg);
    }

    /**
     * 群的权限判断
     *
     * @return boolean
     */
    private function isPermission($groupid, $type = 10)
    {
        $permission = GroupUser::find()
            ->where(['AND', ['=', 'groupid', $groupid], ['>=', 'permission', $type], ['=', 'userid', Yii::$app->user->identity->userid]])
            ->one();

        return $permission;
    }
}
