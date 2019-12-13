<?php
namespace backend\controllers;

use backend\models\AdminSms;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use mdm\admin\models\form\ChangePassword;
use backend\models\User;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'change-password', 'bill', 'test'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post']
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionTest()
    {
        $data = \common\models\bank\Order::find()->where(['symbol' => ''])->limit(5)->all();

        foreach($data as $val){
            $product = \common\models\bank\Product::findOne($val->product_id);
            if(!$product){
                continue;
            }
            $val->scenario = 'update';
            $val->symbol = $product->symbol;
            $fee = explode(',', \backend\models\Setting::read('supernode_top_fee'));
            $uids = [];
            $uid = $val->uid;
            foreach($fee as $k=>$v){
                $supernodeTree = \common\models\UserTree::findOne(['uid' => $uid]);
                if($supernodeTree){
                    $supernode = $supernodeTree->parents()->andWhere(['AND', ['=', 'node', \common\models\UserTree::NODE_ACTIVE], ['<', 'created_at', time()]])->orderBy('id desc')->one();
                }else{
                    $supernode = null;
                }
                $uid = $supernode ? $supernode['uid'] : 0;
                $uids[] = $uid;
            }
            $val->supernode_uid = implode(',', $uids);

            $val->save();
            //  
            $amount = $val->amount * $product->super_rate / 100;
            \common\models\SupernodeProfit::addProfit($amount , $val->symbol, \common\models\SupernodeProfit::TYPE_BANK_EXPECT, $val);
        }
        echo 'ok';
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {   
        $statime = strtotime(date('Y-m-d'));
        $endtime = time();

        //  用户总数
        $member['total'] = User::find()->count();
        //  今日新增用户
        $member['today'] = User::find()->where(['between', 'created_at', $statime, $endtime])->count();

        return $this->render('index', [
            'member' => $member,
        ]);
    }

    public function actionBill()
    {
        $data = \common\models\Invitation::find()->select(['count(1) as cun', 'registerer_id', 'inviter_id', 'level'])->groupBy(['registerer_id', 'inviter_id', 'level'])->having(['>', 'cun', 1])->limit(10)->all();
        $transaction = Yii::$app->db->beginTransaction();

        try {
            foreach($data as $val){

                $repeatData = \common\models\Invitation::find()->where(['registerer_id' => $val->registerer_id, 'inviter_id' =>$val->inviter_id, 'level' => $val->level])->all();
                
                $invitation = $repeatData[1];
                if(false === $invitation->delete()){
                    throw new \yii\base\ErrorException('删除邀请记录失败');
                }

                $inviteReward = \common\models\InviteReward::find()->where(['invitation_id' => $invitation->id])->all();
                foreach($inviteReward as $invite){
                    if(false === $invite->delete()){
                        throw new \yii\base\ErrorException('删除邀请金额记录失败');
                    }

                    //  扣钱操作
                    $wallet = \common\models\Wallet::find()->where(['symbol' => $invite->symbol, 'user_id' => $invite->user_id_rewarded])->one();
                    if(!$wallet->spendMoney($invite->amount, \common\models\WalletLog::TYPE_SYSTEM_COST)){
                        throw new \yii\base\ErrorException('消费失败');
                    }
                }
            }
            $transaction->commit();
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw new \yii\base\ErrorException($th->getMessage());
        }
        echo 'ok';        
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        // print_r(Yii::$app->request->post());die;
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }
    public function actionSms(){
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try{
            $username = \Yii::$app->request->post('username');
            $password = \Yii::$app->request->post('password');
            AdminSms::sendSms($username,$password);
            return [
                'code' => 200,
                'msg' => '短信发送成功',
                'data' => [],
            ];
        }catch (\Exception $exception){
            return [
                'code' => $exception->getCode(),
                'msg' => $exception->getMessage(),
                'data' => [],
            ];
        }
    }
    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Change Password.
     * 
     * @return string
     */
    public function actionChangePassword()
    {
        $model = new ChangePassword();
        if ($model->load(Yii::$app->getRequest()->post()) && $model->change()) {
            return $this->goHome();
        }

        return $this->render('change-password', [
                'model' => $model,
        ]);
    }
}
