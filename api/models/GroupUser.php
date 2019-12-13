<?php

namespace api\models;

use api\controllers\RongCloud;
use Yii;
use yii\base\ErrorException;
use yii\helpers\Json;
use api\models\User;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "iec_group_user".
 *
 * @property int $id
 * @property int $groupid 群ID
 * @property string $userid 用户userid
 * @property string $nickname 用户昵称
 * @property int $permission 权限
 * @property int $msg 消息免打扰
 * @property int $status 状态
 * @property int $created_at
 */
class GroupUser extends \yii\db\ActiveRecord
{
    const IS_BAN_ACTIVE = 10;
    const IS_BAN_DELETED = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_group_user';
    }

    /**
     * 写入后事件
     *
     * @param [type] $insert
     * @param [type] $changedAttributes
     * @return void
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

    }

    /**
     * 删除事件
     *
     * @return void
     */
    public function afterDelete()
    {
        parent::afterDelete();

        Group::updateGroupNums($this->groupid);
    }

    /**
     * 模型行为
     * @return [type] [description]
     */
    public function behaviors()
    {
        return [
            //创建时间
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }

    /**s
     * 场景事物支持
     * @return [type] [description]
     */
    public function transactions()
    {
        return [
            'insert' => self::OP_INSERT | self::OP_UPDATE | self::OP_DELETE,
            'update' => self::OP_INSERT | self::OP_UPDATE | self::OP_DELETE,
        ];
    }

    /**
     * 场景
     *
     * @return void
     */
    public function scenarios()
    {
        return [
            'insert' => ['groupid', 'userid', 'nickname'],
            'update' => ['groupid', 'nickname'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['groupid', 'userid'], 'required'],
            ['groupid', 'exist', 'targetClass' => '\api\models\Group', 'message' => '没有该群ID'],
            ['userid', 'exist', 'targetClass' => '\api\models\User', 'message' => '没有该用户'],
            ['userid', function ($attribute, $params) {
                if (!$this->hasErrors()) {
                    if (self::find()->where(['groupid' => $this->groupid, 'userid' => $this->userid])->one()) {
                        $this->addError($attribute, '已在群里');
                    }
                }
            }],
            ['nickname', 'default', 'value' => ''],
            ['nickname', 'string', 'min' => 1, 'max' => 20],
            [['userid', 'groupid'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'groupid' => 'Groupid',
            'userid' => 'Userid',
            'nickname' => 'Nickname',
            'permission' => 'Permission',
            'msg' => 'Msg',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }

    /**
     * 添加群成员
     *
     * @param [type] $groupID
     * @param array $users
     * @return void
     */
    public static function addUser($groupID, array $users, $create = false)
    {
        $usersArr = [];
        $groupModel = Group::findOne(['groupid' => $groupID]);
        $currenNum = count($users) + $groupModel->nums;

        if($groupModel->max_nums < $currenNum){
            throw new ErrorException('群成员已满');
        }

        foreach ($users as $user) {

            $model = static::find()->where(['groupid' => $groupID, 'userid' => $user])->one();
            if($model){
                $model->scenario = 'update';
                $model->status = 1;
            }else{
                $model = new self();
                $model->scenario = 'insert';

                $model->setAttributes([
                    'groupid' => $groupID,
                    'userid' => $user,
                ]);
            }

            if ($user == Yii::$app->user->identity->userid && $create) {
                $model->permission = 20;
            } else {
                $usersArr[] = $user;
            }
            if (false === $model->save()) {
                throw new ErrorException($model->getErrorSummary(false)[0]);
            }

            //  融云发送群消息
            $userModel = User::findOne(['userid' => $user]);
            RongCloud::getInstance()->sendGroupInfoNtfMessage(Yii::$app->user->identity->userid, $groupID, Json::encode(['message' => '欢迎' . $userModel->nickname . '加入群聊']));
        }
        //  融云添加用户到群
        self::cloudGroupCreateUser($usersArr, $groupModel->groupid, $groupModel->name);

        Group::updateGroupNums($groupID);
        return true;
    }

    //  加入指定群组
    public static function cloudGroupCreateUser($usersArr, $groupid, $groupName)
    {
        $rs = RongCloud::getInstance()->addGroupUser($usersArr, $groupid, $groupName);

        if ($rs['code'] != 200) {
            throw new ErrorException('添加群成员失败');
        }
    }

    //  退出群组方法
    public static function cloudGroupDeleteUser($usersArr, $groupid)
    {
        $rs = RongCloud::getInstance()->deleteGroupUser($usersArr, $groupid);
        if ($rs['code'] != 200) {
            throw new \yii\web\HttpException(500, '删除群成员失败');
        }
    }

    //  发送群操作
    public static function cloudGroupOperating($userid, $groupid, $content)
    {
        $rs = RongCloud::getInstance()->sendGroupMessage($userid, $groupid, $content);
    }


    /**
     * 关联用户
     * TODO
     * @return void
     */
    public function getUser()
    {
        return $this->hasOne(\api\models\User::className(), ['userid' => 'userid'])->select('username,userid,nickname,iecid,headimgurl')->with(['friend']);
    }

    public function getGroup()
    {
        return $this->hasOne(\api\models\Group::className(), ['groupid' => 'groupid']);
    }


    public function getUserLight(){
        return $this->hasOne(User::className(), ['userid' => 'userid']);
    }

    /**
     * 权限ID
     *
     * @return void
     */
    public static function permissionArray()
    {
        return [
            1 => '普通成员',
            10 => '管理员',
            20 => '群主',
        ];
    }
}
