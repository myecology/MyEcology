<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "iec_setting".
 *
 * @property int $id
 * @property string $name 昵称
 * @property string $key 键名
 * @property string $value 值
 * @property string $group 分组
 */
class Setting extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_setting';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'key', 'group'], 'required'],
            [['name', 'key', 'group'], 'string', 'max' => 255],
            ['value', 'default', 'value' => ''],
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
            'key' => '键名',
            'value' => '内容',
            'group' => '分组',
        ];
    }

    /**
     * @param null $config_name
     * @param string $group
     * @return array|null|string
     */
    public static function read($config_name = null, $group = '')
    {
        return Yii::$app->cache->getOrSet("setting-@{$group}-#{}$config_name", function () use ($config_name, $group) {
            $query = static::find()->filterWhere([
                'key' => $config_name,
                'group' => $group,
            ]);

            $return = null;
            if ($config_name) {
                $return = $query->limit(1)->one();
                $return && $return = $return->value;
            } elseif (!$config_name && $group) {
                $return = $query->asArray()->all();

                if ($return) {
                    foreach ($return as $i => $item) {
                        $return[$item['key']] = $item['value'];
                        unset($return[$i]);
                    }
                }
            }

            return $return;
        }, 120);

    }

}
