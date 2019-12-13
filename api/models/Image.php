<?php

namespace api\models;

use Yii;
use yii\behaviors\TimestampBehavior;


/**
 * This is the model class for table "iec_image".
 *
 * @property int $id
 * @property int $type 类型
 * @property int $origin 来源ID
 * @property string $url 地址
 * @property string $thumbnail 缩略图
 * @property int $status 状态
 * @property int $created_at
 */
class Image extends \yii\db\ActiveRecord
{
    const TYPE_MOMENT = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_image';
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
            [['type', 'origin', 'url', 'thumbnail', 'userid'], 'required'],
            [['type', 'origin', 'width', 'height'], 'integer'],
            [['url', 'thumbnail'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'origin' => 'Origin',
            'url' => 'Url',
            'thumbnail' => 'Thumbnail',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }
}
