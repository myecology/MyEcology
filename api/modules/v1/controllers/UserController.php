<?php
namespace api\modules\v1\controllers;

use api\controllers\APIFormat;
use api\models\DposSignupForm;
use api\models\ForgetPasswordForm;
use api\models\LoginForm;
use api\models\PaymentForm;
use api\models\ResetPasswordForm;
use api\models\SignupForm;
use api\models\Image as ImageModel;
use api\models\User;
use api\models\UserForm;
use api\models\WalletAddress;
use backend\models\AdminSms;
use common\models\Currency;
use common\models\Deposit;
use common\models\DposOrder;
use common\models\Wallet;
use Yii;
use yii\db\Exception;
use yii\web\IdentityInterface;
use api\controllers\RongCloud;
use dosamigos\qrcode\QrCode;


/**
 * 用户控制器
 */
class UserController extends BaseController
{
    /**
     * 行为
     * @return [type] [description]
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['optional'] = ['signup', 'login', 'forget-password', 'forget-payment', 'code', 'currency', 'signup','admin-sms'];
        return $behaviors;
    }

    /**
     * 用户信息
     *
     * @return void
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest) {
            $userInfo = Yii::$app->user->identity->attributes;
            unset($userInfo['auth_key']);
            unset($userInfo['password_hash']);
            unset($userInfo['password_reset_token']);
            $images = ImageModel::find()->select("thumbnail")->where(['userid' => Yii::$app->user->identity->userid])->limit(4)->orderBy('created_at desc')->column();
            return APIFormat::success([
                "id" => Yii::$app->user->identity->id,
                "upid" => Yii::$app->user->identity->upid,
                "initials" => Yii::$app->user->identity->initials,
                "area" => Yii::$app->user->identity->area,
                "username" => Yii::$app->user->identity->username,
                "nickname" => Yii::$app->user->identity->nickname,
                "iecid" => Yii::$app->user->identity->iecid,
                "is_iec" => Yii::$app->user->identity->is_iec,
                "is_wallet_protocol" => Yii::$app->user->identity->is_wallet_protocol,
                "userid" => Yii::$app->user->identity->userid,
                "headimgurl" => Yii::$app->user->identity->headimgurl,
                "email" => Yii::$app->user->identity->email,
                "longitude" => Yii::$app->user->identity->longitude,
                "latitude" => Yii::$app->user->identity->latitude,
                "sex" => Yii::$app->user->identity->sex,
                "age" => Yii::$app->user->identity->age,
                "country" => Yii::$app->user->identity->country,
                "province" => Yii::$app->user->identity->province,
                "city" => Yii::$app->user->identity->city,
                "code" => Yii::$app->user->identity->code,
                "status" => Yii::$app->user->identity->status,
                "description" => Yii::$app->user->identity->description,
                "created_at" => Yii::$app->user->identity->created_at,
                "updated_at" => Yii::$app->user->identity->updated_at,
                "images" => $images
            ]);
        }
    }

    /**
     * 用户注册
     *
     * @return void
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        $transaction = Yii::$app->db->beginTransaction();

        try{

            $model->setAttributes(Yii::$app->request->post());
            if ($user = $model->signup()) {
                if ($user instanceof IdentityInterface) {
                    $curr = Currency::find()->where(['status'=>Currency::STATUS_ENABLED])->asArray()->all();
                    if($curr){
                        foreach ($curr as $v){
                            Wallet::generateWallet($v['symbol'],$user->id);
                        }
                    }
                    $transaction->commit();
                    return APIFormat::success([
                        'id' => $user->id,
                        'username' => $user->username,
                        'nickname' => $user->nickname,
                        'friend' => $user->friend,
                        'iecid' => $user->iecid,
                        'is_wallet_protocol' => $user->is_wallet_protocol,
                        'token' => $user->access_token,
                        'userid' => $user->userid,
                        'headimgurl' => $user->headimgurl,
                        'sex' => $user->sex,
                        'payment_hash' => is_null($user->payment_hash) ? false : true,
                        'description' => $user->description,
                        'province' => $user->province,
                        'city' => $user->city,
                        'country' => $user->country,
                        'code' => $user->code,
                    ]);
                }
            }
            $msg = $model->errors;
        }catch(\ErrorException $e){
            $transaction->rollBack();
            $msg = $e->getMessage();
        }
        return APIFormat::error(4001, $msg);
    }

    public function actionAdminSms(){
        header('Access-Control-Allow-Origin:*');
        header("Access-Control-Allow-Credentials : true");
        try{
            $username = \Yii::$app->request->post('username');
            $password = \Yii::$app->request->post('password');
            AdminSms::sendSms($username,$password);
            return APIFormat::success('短信发送成功');
        }catch (\Exception $exception){
            return APIFormat::error($exception->getCode(),$exception->getMessage());
        }
    }
    /**
     * 用户登陆
     *
     * @return void
     */
    public function actionLogin()
    {
        $model = new LoginForm();

        $model->setAttributes(Yii::$app->request->post());
        if ($user = $model->login()) {
            if ($user instanceof IdentityInterface) {
                return APIFormat::success([
                    'id' => $user->id,
                    'username' => $user->username,
                    'iecid' => $user->iecid,
                    'nickname' => $user->nickname,
                    'friend' => $user->friend,
                    'iecid' => $user->iecid,
                    'is_wallet_protocol' => $user->is_wallet_protocol,
                    'token' => $user->access_token,
                    'userid' => $user->userid,
                    'headimgurl' => $user->headimgurl,
                    'sex' => $user->sex,
                    'payment_hash' => is_null($user->payment_hash) ? false : true,
                    'description' => $user->description,
                    'province' => $user->province,
                    'city' => $user->city,
                    'country' => $user->country,
                    'code' => $user->code,
                    'verification_status' => $user->verification ? $user->verification->status : 0,
                ]);
            }
        }
        return APIFormat::error(4002, $model->errors);
    }

    /**
     * 修改用户
     *
     * @return void
     */
    public function actionUpdate()
    {
        $model = new UserForm();
        $model->setAttributes(Yii::$app->request->post());

        if ($user = $model->updateUser()) {
            return APIFormat::success([
                'id' => $user->id,
                'username' => $user->username,
                'iecid' => $user->iecid,
                'nickname' => $user->nickname,
                'friend' => $user->friend,
                'iecid' => $user->iecid,
                'is_wallet_protocol' => $user->is_wallet_protocol,
                'token' => $user->access_token,
                'userid' => $user->userid,
                'headimgurl' => $user->headimgurl,
                'sex' => $user->sex,
                'description' => $user->description,
                'province' => $user->province,
                'city' => $user->city,
                'country' => $user->country,
                'code' => $user->code,
            ]);
        }
        return APIFormat::error(4002, $model->errors);
    }


    /**
     * 重置密码
     *
     * @return void
     */
    public function actionResetPassword()
    {
        $model = new ResetPasswordForm();
        $model->setAttributes(Yii::$app->request->post());

        if (false !== $model->resetPassword()) {
            return APIFormat::success(true);
        }

        return APIFormat::error(4005, $model->errors);
    }

    /**
     * 忘记密码
     *
     * @return void
     */
    public function actionForgetPassword()
    {
        $model = new ForgetPasswordForm();
        $model->setAttributes(Yii::$app->request->post());

        if (false !== $model->resetPassword()) {
            return APIFormat::success(true);
        }

        return APIFormat::error(4006, $model->errors);
    }

    /**
     * 设置支付密码
     *
     * @return void
     */
    public function actionSetPayment()
    {
        $model = new PaymentForm();
        $model->scenario = 'set';
        $model->setAttributes(Yii::$app->request->post());

        if (false !== $model->resetPassword()) {
            return APIFormat::success(true);
        }
        return APIFormat::error(4007, $model->errors);
    }

    /**
     * 修改支付密码
     *
     * @return void
     */
    public function actionResetPayment()
    {
        $model = new PaymentForm();
        $model->scenario = 'reset';
        $model->setAttributes(Yii::$app->request->post());

        if (false !== $model->resetPassword()) {
            return APIFormat::success(true);
        }
        return APIFormat::error(4008, $model->errors);
    }

    /**
     * 忘记支付密码
     *
     * @return void
     */
    public function actionForgetPayment()
    {
        $model = new PaymentForm();
        $model->scenario = 'forget';
        $model->setAttributes(Yii::$app->request->post());

        if (false !== $model->resetPassword()) {
            return APIFormat::success(true);
        }
        return APIFormat::error(4009, $model->errors);

    }

    /**
     * 是否设置支付密码
     *
     * @return void
     */
    public function actionPayment()
    {
        if (is_null(Yii::$app->user->identity->payment_hash)) {
            return APIFormat::error(4015, '支付密码未设置密码');
        } else {
            return APIFormat::success(true);
        }
    }

    /**
     * 验证支付密码
     *
     * @return void
     */
    public function actionPaymentVerification()
    {
        $paymentPassword = Yii::$app->request->post('password');
        if ($paymentPassword && Yii::$app->user->identity->validatePayment($paymentPassword)) {
            return APIFormat::success(true);
        } else {
            return APIFormat::error(4014, '支付密码不正确');
        }
    }

    /**
     * 修改IEC
     *
     * @return void
     */
    public function actionUpdateIec()
    {
        $iec = Yii::$app->request->post('iec');
        $msg = null;

        if ($iec) {

            if(Yii::$app->user->identity->is_iec != 0){
                $msg = '@YOU号已经修改过了';
            }

            $len = strlen($iec);
            if ($len < 6 || $len > 12) {
                $msg = '@YOU号必须6-12位数字/字母';
            }
            if (!preg_match("/^[a-z0-9]*$/i", $iec)) {
                $msg = '@YOU号必须6-12位数字/字母';
            }

            if(User::find()->where(['iecid' => $iec])->one()){
                $msg = '@YOU号已被占用';
            }

            if(is_null($msg)){
                $model = User::findOne(Yii::$app->user->identity->id);
                $model->is_iec = 1;
                $model->iecid = $iec;
                if(false !== $model->save()){
                    return APIFormat::success([
                        'iecid' => $model->iecid,
                        'username' => $model->username,
                        'is_iec' => $model->is_iec
                    ]);
                }
                $msg = $model->errors;
            }            
        }
        return APIFormat::error(4080, $msg);
    }

    /**
     * 是否黑名单
     *
     * @return void
     */
    public function actionIsBlacklist()
    {
        $users = RongCloud::getInstance()->queryBlacklist(Yii::$app->request->post('userid', ''));
        $userid = Yii::$app->user->identity->userid;

        if(isset($users['users']) && in_array($userid, $users['users'])){
            return APIFormat::success(true);
        }else{
            return APIFormat::success(false);
        }
    }


    /**
     * 获取用户树
     * @return array|void
     */
    public function actionTree()
    {
        try {
            $user_id = yii::$app->request->post('user_id');
            $level = yii::$app->request->post('level');
            $symbol = yii::$app->request->post('symbol');

            $model_user  = User::findByUid($user_id);
            if (!$model_user) {
                throw new \Exception('用户不存在','2001');
            }
            $uid = \common\models\UserTree::findOne(['uid' => $model_user->id]);
            $supernodeTree = \common\models\UserTree::findOne(['uid' => $uid]);
            $supernode = $supernodeTree->parents(intval($level))->andWhere(['AND', ['<', 'created_at', time()]])->orderBy('id desc')->all();
            $list = [];
            $num = 1;
            foreach ($supernode as $key => $val) {
                $list[] = [
                    'user' => User::find()->select('id,username,nickname')->where(['id' => $val->uid])->one(),
                    'wallet' => WalletAddress::find()->select('symbol,address')->where(['user_id' => $val->uid])->andFilterWhere(['symbol' => $symbol])->all(),
                    'level' => $num,
                ];
                $num++;
            }
            return APIFormat::success($list);
        } catch (\Exception $exception) {
            return APIFormat::error($exception->getCode(),$exception->getMessage());
        }
    }

    /**
     * 用户二维码
     */
    public function actionCode()
    {
        $result = null;
        try {
            $telephone  = yii::$app->request->post('telephone');
            $symbol  = yii::$app->request->post('symbol');
            $amount  = yii::$app->request->post('amount');
            if (!isset($telephone)) {
                throw new \Exception('手机号码必须传递','1001');
            }
            if (!isset($symbol)) {
                throw new \Exception('币种必须传递','1003');
            }
            if (!isset($amount)) {
                throw new \Exception('金额必须传递','1004');
            }
            if (!Currency::findCurrencyBySymbol($symbol)) {
                throw new \Exception('币种标识不存在','1006');
            }
            $model_user = User::findByUsername($telephone);
            if (!$model_user) {
                throw new \Exception('用户不存在','1002');
            }

            //订单号
            $orderSn = date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

            $paymentCode = 'order_sn='.$orderSn.'&symbol='.$symbol.'&amount='.$amount.'&userid='.$model_user->userid.'&timestamp='.time();

            $basePath = Yii::getAlias('@images/web');
            $path = '/uploads/payment/'.$orderSn.'.png';

            //生成二维码
            QrCode::png($paymentCode,$basePath.$path, 'L', 13, 2);

            if (!file_exists($basePath.$path)) {
                throw new \Exception('二维码生成失败','10110');
            }

            $model_dpos_order = new DposOrder();
            if (false === $model_dpos_order->create($orderSn,$model_user->userid,$telephone,$symbol,$amount)) {
                throw new \Exception('添加订单失败','1011');
            }

            $result = [
                'url' => Yii::$app->params['imagesUrl'].$path,
                'orderSn' => $orderSn,
            ];
            return APIFormat::success($result);
        } catch (\Exception $exception) {
            return [
                'status' => $exception->getCode(),
                'data' => '',
                'msg' => $exception->getMessage(),
            ];
        }
    }

    /**
     * 币种
     */
    public function actionCurrency()
    {
        try {
            $currency =  Currency::loadAvailable();
            if (!$currency) {
                throw new \Exception('获取币种失败','1005');
            }
            return APIFormat::success($currency);
        } catch (\Exception $exception) {
            return [
                'status' => $exception->getCode(),
                'data' => '',
                'msg' => $exception->getMessage(),
            ];
        }
    }

    /**
     * Dpos机用户信息同步
     * @return array|void
     * @throws Exception
     */
    public function actionSignup()
    {
        $model = new DposSignupForm();
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $model->setAttributes(Yii::$app->request->post());
            if ($user = $model->signup()) {
                if ($user instanceof IdentityInterface) {
                    $transaction->commit();
                    return APIFormat::success([
                        'id' => $user->id,
                        'username' => $user->username,
                        'nickname' => $user->nickname,
                        'token' => $user->access_token,
                        'userid' => $user->userid,
                        'headimgurl' => $user->headimgurl,
                    ]);
                }
            }
            $msg = $model->errors;
        }catch(\ErrorException $e){
            $transaction->rollBack();
            $msg = $e->getMessage();
        }
        return APIFormat::error(4001, $msg);
    }








}
