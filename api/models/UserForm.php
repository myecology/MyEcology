<?php
namespace api\models;

use Yii;
use api\models\User;
use yii\base\Model;

/**
 * Login form
 */
class UserForm extends Model
{
    public $nickname;
    public $headimgurl;
    public $email;
    public $longitude;
    public $latitude;
    public $friend;
    public $is_wallet_protocol;
    public $sex;
    public $age;
    public $country;
    public $province;
    public $city;
    public $description;

    private $_user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['nickname', 'string', 'min' => 1, 'max' => 10],
            [['headimgurl', 'email', 'description', 'province', 'city', 'country'], 'string', 'max' => 255],
            [['sex', 'age', 'friend'], 'integer'],
            [['friend', 'is_wallet_protocol'], 'in', 'range' => [0, 1]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nickname' => '昵称',
        ];
    }

    /**
     * 更新用户
     *
     * @return void
     */
    public function updateUser()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            foreach($this->attributes as $key=>$val) {
                if(($val != '') || $key == ('description')) {  //备注为空修改为空
                    $user->$key = $val;
                }
            }
            if(false !== $user->save()){
                return $user;
            }
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findOne(Yii::$app->user->identity->id);
        }

        return $this->_user;
    }
}
