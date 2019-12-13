<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "iec_official".
 *
 * @property int $id
 * @property string $title 标题
 * @property string $content 内容
 * @property string $subtitle 副标题
 * @property int $display 显示 1为显示
 * @property string $language 语言类型
 * @property int $created_at 创建时间
 */
class Official extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_official';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            array('created_at','default','value'=>time()),
            [['title', 'content', 'subtitle', 'display', 'created_at'], 'required'],
            [['content'], 'string'],
            [['display', 'created_at'], 'integer'],
            [['title', 'subtitle'], 'string', 'max' => 255],
            [['language'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'content' => '内容',
            'subtitle' => '副标题',
            'display' => '显示',
            'language' => '语言',
            'created_at' => '创建时间',
        ];
    }

}
