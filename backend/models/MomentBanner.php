<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;


/**
 * This is the model class for table "iec_moment_banner".
 *
 * @property int $id
 * @property string $title 标题
 * @property string $url 广告地址
 * @property string $link 链接地址
 * @property int $sort 排序
 * @property int $status
 * @property int $created_at 创建时间
 */
class MomentBanner extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_moment_banner';
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
            [['title', 'url'], 'required'],
            [['sort', 'status'], 'integer'],
            ['sort', 'default', 'value' => 0],
            ['link', 'url'],
            // ['url', 'filter', 'filter' => function($model){

            //     if(false === strpos($this->url, Yii::$app->params['apiUrl'])){
            //         $url = Yii::$app->params['apiUrl'] . '/' . $this->url;
            //     }else{
            //         $url = $this->url;
            //     }
            //     return $url;
            // }],
            ['link', 'default', 'value' => ''],
            [['title', 'url', 'link'], 'string', 'max' => 255],
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
            'url' => '图片',
            'link' => '跳转地址',
            'sort' => '排序',
            'status' => '状态',
            'created_at' => '创建时间',
        ];
    }


    public static function statusArray()
    {
        return [
            '10' => '启用',
            '0' => '禁用',
        ];
    }
}
