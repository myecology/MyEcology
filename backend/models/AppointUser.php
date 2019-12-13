<?php

namespace backend\models;

use Yii;
use api\controllers\RongCloud;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\base\Model;
class AppointUser extends ActiveRecord
{

    public $username;
    public $items;
    public function scenarios()
    {
        return [
            'addUser' => ['username'],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username','trim','on' => ['addUser']],
            ['items','trim','on' => ['addUser']],
        ];
    }
    public function attributeLabels()
    {
        return [
            'username' => '手机号码',
            'items' => '',
        ];
    }
}
