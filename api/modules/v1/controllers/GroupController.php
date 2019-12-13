<?php
namespace api\modules\v1\controllers;

use api\controllers\APIFormat;
use api\controllers\RongCloud;
use api\models\Group;
use api\models\GroupUser;
use Yii;
use yii\helpers\Json;
use yii\data\Pagination;

/**
 * 创建群
 */
class GroupController extends BaseController
{
    /**
     * 热门群聊列表
     *
     * @return void
     */
    public function actionIndex()
    {
        $data = Group::find()->with(['isExist'])->where(['status' => 1, 'is_hot_show' => 1]);

        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'pageSizeLimit' => [1, 100],
            'totalCount' => $data->count(),
        ]);
        $pagination->validatePage = false;

        $data = $data->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['nums' => SORT_DESC, 'created_at' => SORT_DESC])
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
     * 属于自己的群组
     *
     * @return void
     */
    public function actionSelf()
    {
        $groups = GroupUser::find()
            ->select("groupid")
            ->where(['userid' => Yii::$app->user->identity->userid, 'status' => 1]);

        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'pageSizeLimit' => [1, 100],
            'totalCount' => $groups->count(),
        ]);
        $pagination->validatePage = false;

        $result = $groups->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $data = [];
        foreach ($result as $group) {
            $data[] = Group::findOne(['groupid' => $group->groupid])->attributes;
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
     * 获取群信息
     *
     * @param [type] $groupid
     * @return void
     */
    public function actionInfo($groupid)
    {
        $data = Group::find()->with(['isExist'])->where(['groupid' => $groupid])->asArray()->one();
        if ($data) {
            $data['nums'] = GroupUser::find()->where(['groupid'=>$groupid,'status'=>1])->count();
        }
        return APIFormat::success($data);
    }

    /**
     * 检查是否在群里
     *
     * @param [type] $id
     * @return void
     */
    public function actionIsGroup()
    {
        $groupID = Yii::$app->request->post('groupid', 0);
        $group = GroupUser::find()
            ->where(['groupid' => $groupID, 'userid' => Yii::$app->user->identity->userid, 'status' => 1])
            ->one();
        $data = $group ? true : false;
        return APIFormat::success($data);
    }

    /**
     * 创建群
     *
     * @return void
     */
    public function actionAdd()
    {
        //  创建群
        $model = new Group();
        $model->scenario = 'insert';
        $model->setAttributes(Yii::$app->request->post());
        if (false !== $model->save()) {
            $groupInfo = Group::findOne($model->id);
            return APIFormat::success($groupInfo->attributes);
        }
        return APIFormat::error(4050, $model->errors);
    }

    /**
     * 更新群信息
     *
     * @return void
     */
    public function actionUpdate()
    {
        $groupid = Yii::$app->request->post('groupid');
        $msg = null;

        $model = Group::findOne(['groupid' => $groupid]);
        if ($model && $this->isPermission($groupid)) {
            $model->scenario = 'update';
            $model->setAttributes(Yii::$app->request->post());
            if (false !== $model->save()) {
                return APIFormat::success($model->attributes);
            }
            $msg = $model->errors;
        }
        return APIFormat::error(4058, $msg);
    }

    //  self update
    public function actionSelfUpdate()
    {
        $groupid = Yii::$app->request->post('groupid');
        $msg = null;

        $model = Group::findOne(['groupid' => $groupid]);
        if ($model) {
            $model->scenario = 'self';
            $model->setAttributes(Yii::$app->request->post());
            if (false !== $model->save()) {
                return APIFormat::success($model->attributes);
            }
            $msg = $model->errors;
        }
        return APIFormat::error(4064, $msg);
    }

    /**
     * 解散群
     *
     * @param [type] $id
     * @return void
     */
    public function actionDelete()
    {
        if (Yii::$app->request->isPost && ($groupid = Yii::$app->request->post('groupid'))) {
            $model = Group::findOne(['groupid' => $groupid]);

            if ($model && $model->createid == Yii::$app->user->identity->userid) {
                $model->status = 0;
                $model->scenario = 'update';
                if (false !== $model->save()) {
                    //  删除用户
                    GroupUser::deleteAll(['groupid' => $model->groupid]);
                    return APIFormat::success(true);
                }
            }
        }
        return APIFormat::error(4051);
    }

    /**
     * 禁言群
     *
     * @return void
     */
    public function actionBanAdd()
    {
        $groupid = Yii::$app->request->post('groupid');

        $msg = null;
        if ($groupid && $this->isPermission($groupid)) {
            $group = Group::find()->where(['groupid' => $groupid])->one();
            $group->scenario = 'update';
            $group->is_ban = 1;
            if ($group->banAdd() && false !== $group->save()) {

                //  管理员开启了全员禁言
                $content = [
                    'message' => '管理员开启了全员禁言',
                ];
                RongCloud::getInstance()->sendGroupInfoNtfMessage(Yii::$app->user->identity->userid, $groupid, Json::encode($content));

                return APIFormat::success($group->attributes);
            }
            $msg = $group->errors;
        }

        return APIFormat::error(4062, $msg);
    }

    /**
     * 取消禁言群
     *
     * @return void
     */
    public function actionBanRollback()
    {
        $groupid = Yii::$app->request->post('groupid');

        $msg = null;
        if ($groupid && $this->isPermission($groupid)) {
            $group = Group::find()->where(['groupid' => $groupid])->one();
            $group->scenario = 'update';
            $group->is_ban = 0;
            if ($group->banRollback() && false !== $group->save()) {

                //  管理员关闭了全员禁言
                $content = [
                    'message' => '管理员关闭了全员禁言',
                ];
                RongCloud::getInstance()->sendGroupInfoNtfMessage(Yii::$app->user->identity->userid, $groupid, Json::encode($content));

                return APIFormat::success($group->attributes);
            }
            $msg = $group->errors;
        }

        return APIFormat::error(4063, $msg);
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
