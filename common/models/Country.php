<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "rc_country".
 *
 * @property int $id
 * @property string $name 名称
 * @property string $alias 名称别名
 * @property string $country_flag 国旗
 * @property string $country_currency 国旗
 * @property string $telephone_code 电话国别码
 * @property int $weight 排序权重
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_country';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['weight'], 'integer'],
            [['name', 'country_currency'], 'string', 'max' => 128],
            [['alias', 'country_flag'], 'string', 'max' => 255],
            [['telephone_code'], 'string', 'max' => 64],
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
            'alias' => 'Alias',
            'country_flag' => 'Country Flag',
            'country_currency' => 'Country Currency',
            'telephone_code' => 'Telephone Code',
            'weight' => 'Weight',
        ];
    }

    /**
     * 加载所有可用国家列表
     * @return null|array
     */
    public static function loadAvailable()
    {
        return Yii::$app->cache->getOrSet('countries-available', function () {
            $result = static::find()
                ->orderBy(['weight' => SORT_DESC])
                ->asArray()
                ->limit(200)
                ->all();

            foreach ($result as $i => $item) {
                $result[$i] = [
                    'id' => (string)$item['id'],
                    'name' => (string)$item['name'],
                    'telephone_code' => (string)$item['telephone_code'],
                    'currency' => (string)$item['country_currency'],
                    'icon' => (string)$item['country_flag'],
                ];
            }

            return $result;
        }, 1);
    }

    /**
     * @param $code
     * @return Country|null
     */
    public static function findByCode($code){
        return static::findOne([
            'telephone_code' => $code,
        ]);
    }
}
