<?php

namespace api\models;

use Yii;
use yii\behaviors\TimestampBehavior;


/**
 * This is the model class for table "iec_friend_moment_user".
 *
 * @property int $id
 * @property string $userid 自己userid
 * @property int $moment_id 朋友圈ID
 * @property int $created_at 创建时间
 */
class FriendMomentUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_friend_moment_user';
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

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userid', 'moment_id'], 'required'],
            [['moment_id'], 'integer'],
            [['userid'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => 'Userid',
            'moment_id' => 'Moment ID',
            'created_at' => 'Created At',
        ];
    }

}
