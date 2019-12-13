<?php

namespace api\models;

use Yii;

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
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'url', 'created_at', 'endtime_at'], 'required'],
            [['sort', 'status', 'created_at', 'endtime_at'], 'integer'],
            [['name', 'url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'url' => 'Url',
            'sort' => 'Sort',
            'status' => 'Status',
            'created_at' => 'Created At',
            'endtime_at' => 'Endtime At',
        ];
    }
}
