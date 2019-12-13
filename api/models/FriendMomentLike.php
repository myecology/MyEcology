<?php

namespace api\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "iec_friend_moment_like".
 *
 * @property int $id
 * @property int $momentid ID
 * @property string $userid userid
 * @property int $type 类型
 * @property string $amount 币赞数量
 * @property int $status 状态
 * @property int $created_at
 */
class FriendMomentLike extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_friend_moment_like';
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
                    ActiveRecord::EVENT_BEFORE_INSERT => 'userid',
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

    /**s
     * 场景事物支持
     * @return [type] [description]
     */
    public function transactions()
    {
        return [
            'currency' => self::OP_INSERT | self::OP_UPDATE | self::OP_DELETE,
            'default' => self::OP_INSERT | self::OP_UPDATE | self::OP_DELETE,
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
            'currency' => ['type', 'momentid', 'amount'],
            'default' => ['type', 'momentid'],
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
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['momentid', 'type'], 'required'],
            ['type', 'in', 'range' => [1,2]],

            ['amount', 'required', 'on' => ['currency']],
            ['amount', 'number', 'on' => ['currency']],
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
            'userid' => 'Userid',
            'type' => 'Type',
            'amount' => 'Amount',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }








    /**
     * 关联用户
     *
     * @return void
     */
    public function getUser()
    {
        return $this->hasOne(\api\models\User::className(), ['userid' => 'userid'])->with(['friend'])->select('username,userid,nickname,iecid,headimgurl');
    }
}
