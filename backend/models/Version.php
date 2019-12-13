<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "iec_version".
 *
 * @property int $id
 * @property int $num 版本ID
 * @property int $update 是否强制更新
 * @property string $version 版本号
 * @property int $size 大小
 * @property string $url 下载URL
 * @property string $content
 * @property int $created_at
 */

class Version extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_version';
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
            [['num', 'version', 'url', 'type'], 'required'],
            ['size', 'default', 'value' => 0],
            [['num', 'update', 'size', 'type'], 'integer'],
            [['content'], 'string'],
            [['version', 'url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '类型',
            'num' => '版本ID',
            'update' => '强制更新',
            'version' => '版本号',
            'size' => '文件大小',
            'url' => '下载地址',
            'content' => '备注内容',
            'created_at' => '添加时间',
        ];
    }


    /**
     * 类型
     *
     * @return void
     */
    public static function typeArray()
    {
        return [
            0 => '其他类型',
            1 => 'Android',
            2 => 'IOS'
        ];
    }

    /**
     * 更新
     *
     * @return void
     */
    public static function updateArray()
    {
        return [
            0 => '默认更新',
            1 => '强制更新',
        ];
    }
}
