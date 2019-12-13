<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "iec_start_page".
 *
 * @property int $id
 * @property string $img 图片
 * @property string $name 广告名称
 * @property int $type 类型:1安卓/10 IOS
 * @property int $sort 排序
 * @property int $status 状态:1启动/2禁用
 * @property int $time 广告时间
 * @property int $created_at
 */
class StartPage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_start_page';
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
            [['img', 'name'], 'required'],
            [['type', 'sort', 'status', 'time'], 'integer'],
            [['img', 'name'], 'string', 'max' => 255],
            ['redirecturl', 'default', 'value' => ''],
            [['sort','type'], 'default', 'value' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'img' => '图片地址',
            'name' => '名称',
            'type' => '类型',
            'sort' => '排序',
            'status' => '状态',
            'time' => '广告时间',
            'redirecturl' => '跳转地址',
            'created_at' => '创建时间',
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

    //  状态数组
    public static function typeArray()
    {
        return [
            '0' => '安卓',
            '10' => 'IOS',
        ];
    }


}
