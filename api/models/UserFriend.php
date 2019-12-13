<?php

namespace api\models;

use Yii;
use \yii\db\ActiveRecord;
use yii\helpers\Json;
use api\controllers\RongCloud;
use yii\behaviors\TimestampBehavior;
use yii\base\ErrorException;


/**
 * This is the model class for table "iec_user_friend".
 *
 * @property int $id
 * @property string $in_userid 用户userid
 * @property string $to_userid 好友userid
 * @property int $status
 * @property string $remark 备注
 * @property int $created_at
 * @property int $updated_at
 */
class UserFriend extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_user_friend';
    }

    /**s
     * 场景事物支持
     * @return [type] [description]
     */
    public function transactions()
    {
        return [
            'default' => self::OP_INSERT | self::OP_UPDATE | self::OP_DELETE,
        ];
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
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
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
    }

    /**
     * 场景
     *
     * @return void
     */
    public function scenarios()
    {
        return [
            'insert' => ['to_userid', 'in_userid'],
            'update' => ['to_userid', 'in_userid'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['to_userid', 'in_userid'], 'required'],
            ['to_userid', 'exist', 'targetClass' => '\api\models\User', 'targetAttribute' => 'userid', 'message' => '找不到该用户', 'on' => ['insert']],
            // ['to_userid', 'compare', 'compareValue' => Yii::$app->user->identity->userid, 'operator' => '!=', 'message' => '不能添加自己', 'on' => ['insert']],
            ['to_userid', function($attribute, $params){
                if (!$this->hasErrors()) {
                    if($this->to_userid == $this->in_userid){
                        $this->addError($attribute, '不能添加相同对象');
                    }
                }
            }],
            ['remark', 'string', 'min' => 1, 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'in_userid' => 'In Userid',
            'to_userid' => '好友',
            'status' => 'Status',
            'remark' => 'Remark',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 确认好友
     *
     * @return void
     */
    public function completed()
    {
        $model = self::find()->where(['in_userid' => $this->to_userid, 'to_userid' => $this->in_userid])->one();
        if($model){
            $model->scenario = 'update';
            $model->status = 1;
        }else{
            $model = new self();
            $model->scenario = 'insert';
            $model->setAttributes([
                'in_userid' => $this->to_userid,
                'to_userid' => $this->in_userid,
            ]);
            $model->status = 1;
        }
        if(false === $model->save()){
            throw new \ErrorException('添加好友失败', 5013);
        }

        //  移除黑名单
        RongCloud::getInstance()->removeBlacklist($this->in_userid, $this->to_userid);
        RongCloud::getInstance()->removeBlacklist($this->to_userid, $this->in_userid);
        //  发送消息
        $content = [
            'message' => '对方已同意你的好友请求，现在可以开始聊天了。',
        ];
        $content2 = [
            'message' => '你已添加了对方，现在可以开始聊天了。',
        ];
        RongCloud::getInstance()->sendInfoMessage($this->in_userid, $this->to_userid, Json::encode($content2));
        RongCloud::getInstance()->sendInfoMessage($this->to_userid, $this->in_userid, Json::encode($content));
    }


    /**
     * 申请好友关联用户表
     *
     * @return void
     */
    public function getInUser()
    {
        return $this->hasOne(User::className(), ['userid' => 'in_userid'])->select('id,userid,username,nickname,iecid,headimgurl,initials');
    }

    /**
     * 好友关联用户表
     *
     * @return void
     */
    public function getToUser()
    {
        return $this->hasOne(User::className(), ['userid' => 'to_userid'])->select('id,userid,username,nickname,iecid,headimgurl,initials');
    }

    /**
     * @param $in_userid
     * @param $to_userid
     * @return bool|string
     */
    public static function getRemarkByUserid($in_userid, $to_userid)
    {
        $model = static::findOne([
            'in_userid' => $in_userid,
            'to_userid' => $to_userid,
        ]);

        return $model && $model->remark ? $model->remark : false;
    }

}
