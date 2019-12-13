<?php

namespace common\models\shop;

use api\models\User;
use Yii;

/**
 * This is the model class for table "iec_user_vip".
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $telephone 电话号码
 * @property int $proportion 比列
 * @property int $valid 是否生效
 */
class UserVip extends \yii\db\ActiveRecord
{


    const TAKE_EFFECT = 1;//生效
    const INOPERATIVE = 2;//不生效
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_user_vip';
    }

    public static  $valid_zh = [
      self::TAKE_EFFECT => '生效',
      self::INOPERATIVE => '不生效',
    ];



    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['telephone', 'proportion', 'valid'], 'required'],
            [['proportion', 'valid'], 'integer'],
            [['telephone'], 'string', 'max' => 30],
            ['telephone',function($attribute, $params){
                $user = User::findOne(['username'=>$this->telephone]);
//                var_dump($user);die;
                $this->user_id = $user->id;
            }]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户id',
            'telephone' => '用户手机',
            'proportion' => '比例',
            'valid' => '是否生效',
        ];
    }

    public static function vip($user_id){
        $vip = static::findOne([
            'user_id' => $user_id,
            'valid' => static::TAKE_EFFECT
        ]);
        if(!empty($vip)){
            return $vip->proportion;
        }
        return 0;
    }


}
