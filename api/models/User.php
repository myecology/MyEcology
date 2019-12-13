<?php

namespace api\models;

use api\controllers\RongCloud;
use common\models\Area;
use common\models\Verification;
use common\models\Wallet;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "iec_user".
 *
 * @property int $id
 * @property int $upid
 * @property int $area 国家区号
 * @property string $initials 首字母
 * @property string $username
 * @property string $nickname 昵称
 * @property string $iecid
 * @property string $userid 融云userID
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
 * @property string $code
 * @property int $status
 * @property string $description 个性说明
 * @property int $created_at
 * @property int $updated_at
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    const TYPE_DEFAULT = 0;
    const TYPE_SHOP = 1;
    const TYPE_SALES = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_user';
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
                    // $code = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];
                    // return $code[intval(date('Y')) - 2018] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
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
     * 写入后事件
     *
     * @param [type] $insert
     * @param [type] $changedAttributes
     * @return void
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!$insert) {
            //  创建群拉取会员
            if (isset($changedAttributes['nickname']) || isset($changedAttributes['headimgurl'])) {
                RongCloud::getInstance()->refresh($this->userid, $this->nickname, $this->headimgurl);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        //  无效token
        if (!static::isAccessTokenValid($token)) {
            throw new \yii\web\UnauthorizedHttpException("token is invalid.");
        }

        return static::findOne(['access_token' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
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
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePayment(string $payment)
    {
        if (is_null($this->payment_hash)) {
            return false;
        }
        return Yii::$app->security->validatePassword($payment, $this->payment_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPayment($password)
    {
        $this->payment_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAccessToken()
    {
//         if(YII_DEBUG){
//             $this->access_token = 'DevNOtAccessToken'.mt_rand(1000,1000000);
//             return true;
//         }

        $result = RongCloud::getInstance()->getToken($this->userid, $this->nickname, $this->headimgurl);
        if (isset($result['token']) && $result['token']) {
            $this->access_token = $result['token'];
            return true;
        }
        return false;
    }

    /**
     * Finds out if access token is valid
     */
    public static function isAccessTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        return true;
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }


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

    /**
     * 关联朋友
     *
     * @return void
     */
    public function getFriend()
    {
        return $this->hasOne(\api\models\UserFriend::className(), ['to_userid' => 'userid'])->select("to_userid,remark,status,updated_at")->where(['in_userid' => Yii::$app->user->identity->userid]);
    }

    /**
     * @param $userid
     * @return User|null
     */
    public static function findByUserId($userid)
    {
        return static::findOne([
            'userid' => $userid,
        ]);
    }

    /**
     * @param $userid
     * @return User|null
     */
    public static function findByUid($uid)
    {
        return static::findOne([
            'id' => $uid,
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(User::className(), ['id' => 'upid']);
    }

    public function getNicknameText()
    {
        return $this->nickname ?: $this->username;
    }

    public function getUsernameText()
    {
        $result = $this->username;

        if (preg_match('/^\d+$/i', $result)) {
            $result = substr($result, 0, 3) . str_repeat('*', 3) . substr($result, -2);
        }

        return $result;
    }

    public static function queryNewbie()
    {
        return static::find()
            ->where('created_at > ' . (time() - 2 * 3600))
            ->orderBy(['id' => SORT_ASC]);
    }

    public function getWalletCount()
    {
        return $this->hasMany(Wallet::className(), ['user_id' => 'id'])->count();
    }

    /**
     * @return array
     */
    public function attributeForTransfer()
    {
        //  修改备注
        $userFriend = \api\models\UserFriend::find()->where(['in_userid' => Yii::$app->user->identity->userid, 'to_userid' => $this->userid])->one();
        if($userFriend && $userFriend->remark){
            $nickname = $userFriend->remark;
        }else{
            $nickname = $this->nickname;
        }

        return [
            'headimgurl' => $this->headimgurl,
            'nickname' => $nickname,
            'iecid' => $this->iecid,
        ];
    }


    /**
     * 实名
     *
     * @return void
     */
    public function getVerification()
    {
        return $this->hasOne(\common\models\Verification::className(), ['user_id' => 'id']);

    }

    public static function phoneWalletSymbol($phone,$symbol){
        $user = static::findOne(['username' => $phone]);
        if(empty($user)){
            throw new \ErrorException('用户不存在', 1000);
        }
        $user_wallet = Wallet::findOne(['user_id'=>$user->id,'symbol'=>$symbol]);
        if(empty($user_wallet)){
            throw new \ErrorException('你没有该类型钱包', 1001);
        }
        return $user_wallet;
    }

    public static function posSearch($params)
    {
        $query = static::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => isset($params['page_size']) ? $params['page_size'] : 200,
                'page' => isset($params['page']) ? $params['page'] - 1: 0,
            ]
        ]);
        return $dataProvider;
    }

}
