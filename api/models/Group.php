<?php

namespace api\models;

use api\controllers\RongCloud;
use api\models\GroupUser;
use Yii;
use yii\helpers\Json;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\imagine\Image;

/**
 * This is the model class for table "iec_group".
 *
 * @property int $id
 * @property string $groupid 群ID
 * @property string $name 群名称
 * @property string $groupimgurl 群头像
 * @property int $sort 排名
 * @property int $hot 热度
 * @property int $nums 群人数
 * @property string $description 个性说明
 * @property int $status
 * @property int $is_verify
 * @property int $is_hot_show
 * @property int $is_pull
 * @property int $created_at
 * @property int $updated_at
 */
class Group extends ActiveRecord
{
    public $users;

    const IS_VERIFY_NO = -1;                    //  拒绝加群
    const IS_VERIFY_ALL = 0;                    //  直接加群
    const IS_VERIFY_YES = 10;                   //  验证加群

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_group';
    }

    /**
     * 模型行为
     * @return [type] [description]
     */
    public function behaviors()
    {
        return [
            //  code
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'groupid',
                ],
                'value' => function ($event) {
                    return $this->generateGroupid();
                },
            ],
            //  创建者userid
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'createid',
                ],
                'value' => function ($event) {
                    return Yii::$app->user->identity->userid;
                },
            ],
            //创建时间
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => ['updated_at'],
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
            'insert' => ['name', 'users', 'groupimgurl', 'description'],
            'update' => ['name', 'groupimgurl', 'description', 'is_verify', 'is_hot_show', 'is_pull'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            ['users', 'required', 'on' => ['insert']],
            [['is_verify', 'is_hot_show', 'is_pull'], 'in', 'range' => [0,1], 'on' => ['update']],
            ['users', function ($attribute, $params) {
                if (!$this->hasErrors()) {
                    $users = (array) $this->users;
                    if (count($users) < 2) {
                        $this->addError($attribute, '成员必须3个人');
                    }
                }
            }, 'on' => ['insert']],
            ['users', 'filter', 'filter' => function ($value) {
                $users = (array) $this->users;
                if (!in_array(Yii::$app->user->identity->userid, $users)) {
                    $users[] = Yii::$app->user->identity->userid;
                }
                return $users;
            }, 'on' => ['insert']],
            ['groupimgurl', 'filter', 'filter' => function ($value) {
                if (empty($this->groupimgurl)) {
                    $users = (array) $this->users;
                    if (!in_array(Yii::$app->user->identity->userid, $users)) {
                        $users[] = Yii::$app->user->identity->userid;
                    }

                    $imgBase = Yii::getAlias('@images/web/images/default/group_background.png');
                    $basePath = 'uploads/group/' . $this->generateString();
                    $groupImagePath = Yii::getAlias('@images/web/' . $basePath);
                    if (!file_exists($groupImagePath)) {
                        FileHelper::createDirectory($groupImagePath);
                    }

                    $groupImage = $groupImagePath . '/' . '_group.png';
                    $pos = [
                        [120, 10],
                        [12, 205],
                        [210, 205],
                    ];

                    foreach ($users as $key => $val) {
                        if($key > 2){
                            break;
                        }

                        $user = User::find()->select('headimgurl')->where(['userid' => $val])->one();
                        $imageInfo = preg_split('/\./', $user->headimgurl);
                        $extension = end($imageInfo);
                        $path = $groupImagePath . '/' . 'group.' . $extension;
                        Image::thumbnail($user->headimgurl, 180, 180)->save($path);

                        Image::watermark($imgBase, $path, $pos[$key])->save($groupImage);
                        $imgBase = $groupImage;
                    }
                    $this->groupimgurl = Yii::$app->params['imagesUrl'] . '/' . $basePath . '/_group.png';
                }
                return $this->groupimgurl;
            }, 'on' => ['insert']],
            ['name', 'string', 'min' => 1, 'max' => 20],
            ['groupimgurl', 'default', 'value' => Yii::$app->params['imagesUrl'] . '/' . 'images/default/default_headimgurl.png'],
            ['description', 'default', 'value' => ''],
        ];
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

        if ($insert) {
            //  创建群拉取会员
            $this->cloudCreateGroup();
            GroupUser::addUser($this->groupid, $this->users, true);
            self::updateGroupNums($this->groupid);
        } else {
            if ($this->status == 1 && isset($changedAttributes['name'])) {
                $this->cloudUpdateGroup();
            } elseif ($this->status == 0 && isset($changedAttributes['status'])) {
                $this->cloudDismissGroup();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'groupid' => 'Groupid',
            'createid' => 'Createid',
            'name' => 'Name',
            'groupimgurl' => 'Groupimgurl',
            'sort' => 'Sort',
            'hot' => 'Hot',
            'nums' => 'Nums',
            'description' => 'Description',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 更新群人数
     *
     * @param [type] $groupID
     * @return void
     */
    public static function updateGroupNums($groupID)
    {
        $group = self::findOne(['groupid' => $groupID]);
        $group->scenario = 'update';
        $group->nums = GroupUser::find()->where(['groupid' => $groupID, 'status' => 1])->count();
        if (false === $group->save()) {
            throw new \yii\web\HttpException(500, '更新群人数失败');
        }
    }

    //  融云创建群
    public function cloudCreateGroup()
    {
        $rs = RongCloud::getInstance()->createGroup([Yii::$app->user->identity->userid], $this->groupid, $this->name);
        if ($rs['code'] != 200) {
            throw new \yii\web\HttpException(500, '创建群失败');
        }
    }

    //  融云刷新群
    public function cloudUpdateGroup()
    {
        $rs = RongCloud::getInstance()->updateGroup($this->groupid, $this->name);

        //  发送群系统消息
        $content = [
            'operatorUserId' => Yii::$app->user->identity->userid,
            'operation' => 'Rename',
            'data' => [
                'operatorNickname' => Yii::$app->user->identity->nickname,
                'targetGroupName' => $this->name,
            ],
            'message' => '修改本群名为' . $this->name,
            'extra' => $this->groupid,
        ];
        //  发送群消息
        RongCloud::getInstance()->sendGroupMessage(Yii::$app->user->identity->userid, $this->groupid, Json::encode($content));
        if ($rs['code'] != 200) {
            throw new \yii\web\HttpException(500, '刷新群失败');
        }
    }

    //  解散群
    public function cloudDismissGroup()
    {
        $rs = RongCloud::getInstance()->dismissGroup($this->createid, $this->groupid);
        if ($rs['code'] != 200) {
            throw new \yii\web\HttpException(500, '刷新群失败');
        }
    }

    //  添加群禁言
    public function banAdd()
    {
        $rs = RongCloud::getInstance()->banGroupAdd($this->groupid);
        if ($rs['code'] != 200) {
            return false;
        }

        //  获取群主 + 群管理员
        $groupUser = GroupUser::find()->select("userid")->where([
            'AND',
            ['=', 'groupid', $this->groupid],
            ['>=', 'permission', 10],
        ])->column();
        // var_dump(RongCloud::getInstance()->banGroupWhitelistQuery($this->groupid));die;
        RongCloud::getInstance()->banGroupWhitelistAdd($groupUser, $this->groupid);

        return true;
    }

    //  接触群禁言
    public function banRollback()
    {
        $rs = RongCloud::getInstance()->banGroupRollback($this->groupid);
        if ($rs['code'] != 200) {
            return false;
        }

        //  获取白名单列表
        $result = RongCloud::getInstance()->banGroupWhitelistQuery($this->groupid);
        $result =RongCloud::getInstance()->banGroupWhitelistRollback($result['userIds'], $this->groupid);
        return true;
    }

    /**
     * 生成groupID
     *
     * @return void
     */
    public function generateGroupid()
    {
        $length = 16;
        $arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        $str = '';
        $arr_len = count($arr);
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $arr_len - 1);
            $str .= $arr[$rand];
        }
        return $str;
    }

    /**
     * 判断是否在群里
     *
     * @return void
     */
    public function getIsExist()
    {
        return $this->hasOne(GroupUser::className(), ['groupid' => 'groupid'])
                    ->select('status,groupid,nickname,created_at,permission')
                    ->andWhere(['userid' => Yii::$app->user->identity->userid])
                    ->andWhere(['status' => 1]);
    }

    /**
     * 生成string
     *
     * @return void
     */
    protected function generateString()
    {
        $length = 16;
        $arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        $str = '';
        $arr_len = count($arr);
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $arr_len - 1);
            $str .= $arr[$rand];
        }
        return $str;
    }

    /**
     * @param $groupid
     * @return Group|null
     */
    public static function findByGroupId($groupid)
    {
        return static::findOne([
            'groupid' => $groupid,
        ]);
    }

    public function getGroupUsers()
    {
        return $this->hasMany(GroupUser::className(), ['groupid' => 'groupid'])
            ->with(['userLight']);
    }
}
