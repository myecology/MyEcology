<?php

namespace api\models;

use api\controllers\APIFormat;
use common\models\Currency;
use common\models\UserAddress;
use yii\base\Model;
use yii\helpers\Url;

/**
 * AddressUpdate form
 */
class AddressUpdateForm extends Model
{
    public $id;
    public $alias;

    private $user_address;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'alias',], 'trim'],
            [['id', 'alias',], 'required'],

            ['alias', 'string', 'max' => 20],

            ['id', function ($attribute, $param) {
                if (!$this->hasErrors() && !$this->getUserAddress()) {
                    $this->addError($attribute, APIFormat::$code[5009]);
                }
                return true;
            }],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'alias' => '备注名',
        ];
    }

    /**
     * @return bool|UserAddress
     */
    public function update()
    {
        if ($this->validate()) {
            $model = $this->getUserAddress();
            $model->setAttributes([
                'name' => $this->alias,
            ]);

            return $model->save() ? $model : false;
        }

        return false;
    }

    protected function getUserAddress()
    {
        is_null($this->user_address) && $this->user_address = UserAddress::findOne(['user_id' => \Yii::$app->user->getId(), 'id' => $this->id]);
        return $this->user_address;
    }
}
