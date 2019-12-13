<?php

namespace api\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;


/**
 * This is the model class for table "iec_friend_moment_reply".
 *
 * @property int $id
 * @property int $momentid ID
 * @property string $in_userid 回复人userid
 * @property string $to_userid 对象人userid
 * @property string $content 回复内容
 * @property int $status 状态
 * @property int $created_at
 */
class FriendMomentReply extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_friend_moment_reply';
    }

    const COMMENT_TYPE = 10;              //  评论
    const REPLY_TYPE = 20;                //  回复

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
                    ActiveRecord::EVENT_BEFORE_INSERT => 'in_userid',
                ],
                'value' => function ($event) {
                    return Yii::$app->user->identity->userid;
                },
            ],
            //创建时间
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['momentid', 'content','to_userid'], 'required'],
            ['momentid', 'integer'],
            ['is_reply', 'default', 'value' => static::COMMENT_TYPE],
            ['is_reply', 'in', 'range' => [static::COMMENT_TYPE, static::REPLY_TYPE]],
            ['to_userid', 'exist', 'targetClass' => '\api\models\User', 'targetAttribute' => 'userid', 'message' => '找不到该用户'],
            ['to_userid', 'string', 'max' => 255], 
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'momentid' => 'Momentid',
            'in_userid' => 'In Userid',
            'to_userid' => 'To Userid',
            'is_reply' => 'Is Reply',
            'content' => 'Content',
            'status' => 'Status',
            'created_at' => 'Created At',
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

        //  重置热度
        FriendMoment::resetHot($this->momentid);
    }

    /**
     * 关联自身userid
     *
     * @return void
     */
    public function getInUser()
    {
        return $this->hasOne(\api\models\User::className(), ['userid' => 'in_userid'])->with(['friend'])->select('userid,username,nickname,iecid,headimgurl');
    }

    /**
     * 关联对方userid
     *
     * @return void
     */
    public function getToUser()
    {
        return $this->hasOne(\api\models\User::className(), ['userid' => 'to_userid'])->with(['friend'])->select('userid,username,nickname,iecid,headimgurl');
    }
}
