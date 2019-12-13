<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;


/**
 * This is the model class for table "iec_shop_type".
 *
 * @property int $id
 * @property string $title 标题
 * @property string $icon 图标
 * @property int $weight 权重
 * @property int $created_at 添加时间
 * @property int $updated_at 更新时间
 */
class ShopType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_shop_type';
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
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => ['updated_at'],
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
            [['created_at', 'updated_at', 'weight'], 'integer'],
            [['title'], 'string', 'max' => 32],
            [['icon'], 'string', 'max' => 255],
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
            'icon' => '图标',
            'weight' => '权重',
            'created_at' => '添加时间',
            'updated_at' => '更新时间',
        ];
    }


    public function attributesForList()
    {
        return [
            'id'    => $this->id,
            'title' => $this->title,
            'icon'  => $this->icon,
        ];
    }


    public static function getList()
    {
        $data = ShopType::find()->orderBy(['weight'=>SORT_DESC])->all();
        foreach ($data as $key => $val) {
            $result[] = $val->attributesForList();
        }
        return $result;
    }




}
