<?php

namespace backend\models;

use Yii;
use api\controllers\RongCloud;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "iec_user".
 *
 * @property int $id
 * @property int $upid 上级用户ID
 * @property int $area 国家区号
 * @property string $initials 首字母
 * @property string $username
 * @property string $nickname 昵称
 * @property string $iecid
 * @property string $userid userID
 * @property string $headimgurl 用户头像
 * @property string $access_token token
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $longitude 经度
 * @property string $latitude 纬度
 * @property int $sex 性别
 * @property int $age 年龄
 * @property string $country 国家
 * @property string $province 省
 * @property string $city 城市
 * @property int $status
 * @property string $code 邀请码
 * @property string $description 个性说明
 * @property int $created_at
 * @property int $updated_at
 * @property int $friend                    //  
 * @property string $payment_hash
 * @property int $is_iec
 */
class User extends ActiveRecord
{

    private $topUsername = '';
    public $password;

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    const CRONTAB_STATUS_DEFAULT = 0;   //  完成状态
    const CRONTAB_STATUS_SIGNUP = 10;   //  注册初始化

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_user';
    }

    /**s
     * 场景事物支持
     * @return [type] [description]
     */
    public function transactions()
    {
        return [
            'default' => self::OP_INSERT | self::OP_UPDATE | self::OP_DELETE,
        ];
    }

    /**
     * 模型行为
     * @return [type] [description]
     */
    public function behaviors()
    {
        return [
            //  code
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'code',
                ],
                'value' => function ($event) {
                    return $this->generateCode();
                },
            ],
            //  首字母
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'initials',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'initials',
                ],
                'value' => function ($event) {
                    return $this->generateInitials();
                },
            ],
            //  微信号
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'iecid',
                ],
                'value' => function ($event) {
                    return $this->generateIecid() . date('m');
                },
            ],
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
            ['username', 'trim'],
            [['username', 'nickname', 'password'], 'required'],
            ['password', function ($attribute, $params) {
                if (!$this->hasErrors()) {
                    $a = preg_match('/[0-9]/', $this->password);
                    $b = preg_match('/[a-z]/', $this->password);
                    // $c = preg_match('/[A-Z]/', $this->password);

                    if (!$a || !$b) {
                        $this->addError($attribute, '密码必须6-20位字母和数字');
                    }
                }
            }],
            ['password', 'string', 'min' => 6, 'max' => 20],
            ['username', 'match', 'pattern' => '/^[16789][345678][0-9]{9}$/', 'message' => '手机号码格式不对'],
            ['username', 'unique'],
            ['nickname', 'string', 'min' => 1, 'max' => 20],
            ['area', 'default', 'value' => 86],
            [['sex', 'age', 'area'], 'integer'],
            ['upid', 'filter', 'filter' => function($value){
                $upid = 0;
                if($this->upid){
                    $user = static::findOne($this->upid);
                    $this->topUsername = $user->username;
                    $upid = $this->upid;
                }
                return $upid;
            }],
            [['sex', 'age'], 'default', 'value' => 0],
            ['description', 'default', 'value' => 0],
            ['headimgurl', 'default', 'value' => Yii::$app->params['imagesUrl'] . '/images/default/default_headimgurl.png']
        ];
    }


    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate()) {
            $this->setPassword($this->password);
            $this->crontab_status = \backend\models\User::CRONTAB_STATUS_SIGNUP;
            $this->generateAuthKey();
            $this->generateUserid();

            if(!$this->generateAccessToken() || !$this->save()){
                return false;
            }

            if(false === $this->save()){
                return false;
            }
            return true;
        }
        return false;
    }


    /**
     * 性别
     *
     * @return void
     */
    public static function sexArray()
    {
        return [
            0 => '默认值',
            1 => '男',
            2 => '女'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'upid' => '上级用户ID',
            'area' => '区号',
            'initials' => '首字母',
            'username' => '手机号',
            'nickname' => '昵称',
            'iecid' => '@YOU号',
            'userid' => 'USERID',
            'headimgurl' => '头像',
            'access_token' => 'Token',
            'auth_key' => 'Auth Key',
            'password_hash' => '密码',
            'password' => '密码',
            'password_reset_token' => 'Password Reset Token',
            'email' => '邮箱',
            'longitude' => '经度',
            'latitude' => '维度',
            'sex' => '性别',
            'age' => '年龄',
            'country' => '国家',
            'province' => '省',
            'city' => '市',
            'status' => '镇',
            'code' => '邀请码',
            'description' => '为令牌打CALL',
            'created_at' => '注册时间',
            'updated_at' => '更新时间',
            'friend' => 'Friend',
            'payment_hash' => 'Payment Hash',
            'is_iec' => '是否修改@YOU号',
        ];
    }














    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * 生成userid
     *
     * @return void
     */
    public function generateUserid()
    {
        $length = 16;
        $arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        $str = '';
        $arr_len = count($arr);
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $arr_len - 1);
            $str .= $arr[$rand];
        }
        $this->userid = $str;
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAccessToken()
    {
        $result = RongCloud::getInstance()->getToken($this->userid, $this->nickname, $this->headimgurl);
        if (isset($result['token']) && $result['token']) {
            $this->access_token = $result['token'];
            return true;
        }
        return false;
    }

    /**
     * 首字母
     *
     * @return void
     */
    protected function generateInitials()
    {
        //  移除表情昵称
        $remove = function ($str) {
            $str = preg_replace_callback('/./u',
                function (array $match) {
                    return strlen($match[0]) >= 4 ? '' : $match[0];
                },
                $str);
            return $str;
        };

        $str = $remove($this->nickname);
        $initials = '#';

        $fchar = ord($str{0});
        if ($fchar >= ord('A') && $fchar <= ord('z')) {
            $initials = strtoupper($str{0});
        }

        $s1 = iconv('UTF-8', 'gb2312', $str);
        $s2 = iconv('gb2312', 'UTF-8', $s1);

        $s = $s2 == $str ? $s1 : $str;
        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;

        if ($asc >= -20319 && $asc <= -20284) {
            $initials = 'A';
        } elseif ($asc >= -20283 && $asc <= -19776) {
            $initials = 'B';
        } elseif ($asc >= -19775 && $asc <= -19219) {
            $initials = 'C';
        } elseif ($asc >= -19218 && $asc <= -18711) {
            $initials = 'D';
        } elseif ($asc >= -18710 && $asc <= -18527) {
            $initials = 'E';
        } elseif ($asc >= -18526 && $asc <= -18240) {
            $initials = 'F';
        } elseif ($asc >= -18239 && $asc <= -17923) {
            $initials = 'G';
        } elseif ($asc >= -17922 && $asc <= -17418) {
            $initials = 'H';
        } elseif ($asc >= -17417 && $asc <= -16475) {
            $initials = 'J';
        } elseif ($asc >= -16474 && $asc <= -16213) {
            $initials = 'K';
        } elseif ($asc >= -16212 && $asc <= -15641) {
            $initials = 'L';
        } elseif ($asc >= -15640 && $asc <= -15166) {
            $initials = 'M';
        } elseif ($asc >= -15165 && $asc <= -14923) {
            $initials = 'N';
        } elseif ($asc >= -14922 && $asc <= -14915) {
            $initials = 'O';
        } elseif ($asc >= -14914 && $asc <= -14631) {
            $initials = 'P';
        } elseif ($asc >= -14630 && $asc <= -14150) {
            $initials = 'Q';
        } elseif ($asc >= -14149 && $asc <= -14091) {
            $initials = 'R';
        } elseif ($asc >= -14090 && $asc <= -13319) {
            $initials = 'S';
        } elseif ($asc >= -13318 && $asc <= -12839) {
            $initials = 'T';
        } elseif ($asc >= -12838 && $asc <= -12557) {
            $initials = 'W';
        } elseif ($asc >= -12556 && $asc <= -11848) {
            $initials = 'X';
        } elseif ($asc >= -11847 && $asc <= -11056) {
            $initials = 'Y';
        } elseif ($asc >= -11055 && $asc <= -10247) {
            $initials = 'Z';
        }

        return $initials;
    }


    /**
     * 生成邀请码
     *
     * @return void
     */
    protected function generateCode()
    {
        $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $code[rand(0, 25)]
            . strtoupper(dechex(date('m')))
            . date('d')
            . substr(time(), -5)
            . substr(microtime(), 2, 5)
            . sprintf('%02d', rand(0, 99));
        for (
            $a = md5($rand, true),
            $s = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            $d = '',
            $f = 0;
            $f < 6;
            $g = ord($a[$f]),
            $d .= $s[($g ^ ord($a[$f + 8])) - $g & 0x1F],
            $f++
        ) ;
        return $d;
    }

    /**
     * 生成iec号
     *
     * @return void
     */
    public function generateIecid()
    {
        $length = 8;
        $arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        $str = '';
        $arr_len = count($arr);
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $arr_len - 1);
            $str .= $arr[$rand];
        }
        return $str;
    }

    public function getParent()
    {
        return $this->hasOne(User::className(), ['id' => 'upid']);
    }

}
