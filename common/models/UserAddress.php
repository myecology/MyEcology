<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "iec_user_address".
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property string $name 用户备注
 * @property string $address 钱包地址
 * @property int $created_at 钱包地址
 * @property string $model 钱包类型
 */
class UserAddress extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_user_address';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'created_at'], 'integer'],
            [['name', 'address'], 'required'],
            [['name'], 'string', 'max' => 64],
            [['address'], 'string', 'max' => 128],

            [['model'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'name' => 'Name',
            'address' => 'Address',
            'created_at' => 'Created At',
            'model'=>'Model'
        ];
    }

    /**
     * 用户id查询地址列表
     * @param $user_id
     * @param null $coin_model
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function loadByUserId($user_id, $coin_model = null)
    {
        $data = static::find()->where(['user_id' => $user_id])
            ->andFilterWhere([
                'model' => $coin_model,
            ])
            ->orderBy(['created_at' => SORT_DESC])
            ->asArray()
            ->all();

        return $data;
    }

    /**
     * @param $address_id
     * @param $user_id
     * @param $coin_model
     * @return UserAddress|null
     */
    public static function getByAddressId($address_id, $user_id, $coin_model)
    {
        return static::findOne([
            'id' => $address_id,
            'user_id' => $user_id,
            'model' => $coin_model,
        ]);
    }

    /**
     * @param $address
     * @return null
     */
    public static function detectModelByAddress($address)
    {
        //TODO 根据地址规则来判断公链模型
        return null;
    }
}
