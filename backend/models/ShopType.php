<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "iec_shop_type".
 *
 * @property int $id
 * @property string $title 标题
 * @property string $icon 图标
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
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'integer'],
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
            'title' => 'Title',
            'icon' => 'Icon',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
