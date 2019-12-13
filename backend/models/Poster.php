<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "iec_poster".
 *
 * @property int $id
 * @property string $name 海报名称
 * @property string $url 地址
 * @property int $sort 排序
 * @property int $status 状态
 * @property int $created_at
 * @property int $endtime_at
 */
class Poster extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_poster';
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
            [['name', 'url'], 'required'],
            [['sort', 'status',], 'integer'],
            [['name', 'url'], 'string', 'max' => 255],
            [['endtime_at','sort'], 'default', 'value' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'url' => '图片地址',
            'sort' => '排序',
            'status' => '状态',
            'created_at' => '创建时间',
            'endtime_at' => 'Endtime At',
        ];
    }


    //  状态数组
    public static function statusArray()
    {
        return [
            '0' => '禁用',
            '10' => '启用',
        ];
    }
}
