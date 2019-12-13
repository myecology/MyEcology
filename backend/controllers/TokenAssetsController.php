<?php

namespace backend\controllers;

use backend\models\AppointUser;
use backend\models\UploadForm;
use backend\models\User;
use backend\queue\AssetsQueueController;
use common\models\IecAssetsAmount;
use common\models\Message;
use common\models\RcUserToken;
use common\models\Wallet;
use common\models\WalletLog;
use http\Client\Response;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Yii;
use common\models\TokenAssets;
use common\models\search\TokenAssetsSearch;
use yii\base\ErrorException;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use api\controllers\APIFormat;

/**
 * TokenAssetsController implements the CRUD actions for TokenAssets model.
 */
class TokenAssetsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all TokenAssets models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TokenAssetsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TokenAssets model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TokenAssets model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TokenAssets();
        $data = Yii::$app->request->post('TokenAssets');

        $type = $data['type'];
        if($type == TokenAssets::TYPE_PHASE_RELEASE){//阶段释放
            $start_time = $data['start_time'];
            $end_time = $data['end_time'];
            $release_cycle = $data['release_cycle'];
            $release_cycle_time = $release_cycle * 86400;
            $diff_time = strtotime($end_time) - strtotime($start_time);
            $res = $diff_time / $release_cycle_time;
            if($res < 1 ){
                return $this->render('/site/error', ['name' => '结束时间设置不满足最低释放周期','message' => '']);
            }
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TokenAssets model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TokenAssets model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $res = RcUserToken::findOne(['token_assets_id'=>$id]);
        if($res){
                $name = '该代币已指定用户，不能删除';
                $message = '';
                return $this->render('/site/error', ['name' => $name,'message' => $message]);
        }else{
            $this->findModel($id)->delete();
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the TokenAssets model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TokenAssets the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TokenAssets::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    //指定用户页面
    public function actionAddUser($id)
    {
        $model = new AppointUser();
        $upload_model = new UploadForm();
        $model->setScenario('addUser');
        $token_info = TokenAssets::find()->where(['id' => $id])->with(['tokenType'])->one();
        if (time() > $token_info->start_time) {
          return $this->render('/site/error',['name'=>'该资产已开始释放，不能再指定用户','message'=>'']);
        }
        return $this->render('add-user', [
            'model' => $model,
            'upload_model' => $upload_model,
            'token_info' => $token_info,
            'token_assets_id' => $id,
        ]);
    }
    //批量指定用户
    public function actionReadExcel(){
        $token_assets_id = Yii::$app->request->post('UploadForm')['assets_id'];
        $tmp = $_FILES['UploadForm']['tmp_name']['inputFile'];
        if(empty($tmp)){
            return $this->render('/site/error',['name'=>'请选择要上传的文件','message'=>'']);
        }
        $reader = new Xlsx();
        $spreadsheet = $reader->load($tmp);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        array_shift($sheetData);//去掉第一行的标题
        $users = array_column($sheetData,'B');//只获取用户的手机号
        $phone = '';
        foreach ($users as $k=>$v){
            $v = trim((string)$v);//excel获取的手机号是float，这里转成字符串
            $user = User::findOne(['username' => $v]);
            if(!$user){
                $phone .= ' '.$v;
            }
            $users[$k] = $v;
        }
        if($phone){
            return $this->render('/site/error',['name'=>$phone.'手机号未注册','message'=>'']);
        }
       return $this->designatedUser($users,$token_assets_id);

    }
    //根据输入的手机号模糊查询用户手机号
    public function actionGetUserPhone()
    {
        $data = Yii::$app->request->post();
        $users = [];
        if ($data['username']) {
            $users = User::find()->select('id,username')
                ->where(['like', 'username', $data['username'] . '%', false])
                ->asArray()
                ->limit(10)
                ->all();
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return ['users' => $users, 'code' => 200];
    }
    //保存指定用户信息
    public function actionSaveUser()
    {
        $data = Yii::$app->request->post();
        //指定的用户手机号
        $checked_users_phone = $data['checked_users_phone'];
        //指定的代币id
        $token_assets_id = $data['token_assets_id'];
        return $this->designatedUser($checked_users_phone,$token_assets_id);
        foreach ($checked_users_phone as $k => $v) {
                $transaction = Yii::$app->db->beginTransaction();
                try{
                    $user = User::findOne(['username' => $v]);
                    $res = RcUserToken::find()->where(['user_id' => $user->id, 'token_assets_id' => $token_assets_id])->one();
                    $token_assets = TokenAssets::findOne($token_assets_id);
                    if ($res) {
                        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                        return ['message' => '不能重复指定' . $v . '用户', 'code' => 444];
                    }
                    $re_user_token = new RcUserToken();
                    $total_number_of_times = null;
                    $every_time_number = null;
                    $asset_number = date('YmdHis',time()).$user->id;
                    if($token_assets->type ==  TokenAssets::TYPE_PHASE_RELEASE){
                        //1.计算释放时间总差值
                        $diff_time = $token_assets->end_time - $token_assets->start_time;
                        //2.计算释放的总次数(向上取整)
                        $total_number_of_times = ceil($diff_time / ($token_assets->release_cycle * 86400));
                        //3.计算每次释放数量，四舍五入保留4位小数
                        $every_time_number = round($token_assets->currency_total / $total_number_of_times,4);
                        $re_user_token->every_time_number = $every_time_number;
                    }
                    //用户资产信息记录
                    $re_user_token->user_id = $user->id;
                    $re_user_token->token_type_id = $token_assets->token_type_id;
                    $re_user_token->token_assets_id = $token_assets_id;
                    $re_user_token->available_balance = 0;
                    $re_user_token->locked_balance = $token_assets->currency_total;
                    $re_user_token->asset_number = $asset_number;
                    $re_user_token->created_at = time();
                    $re_user_token->updated_at = time();
                    //用户总资产信息
                    $assets_amount_model = IecAssetsAmount::find()->where(['user_id'=>$user->id])->one();
                    if($assets_amount_model){//当用户总资产信息已存在
                        $assets_amount_model->amount_lock += $token_assets->currency_total;//添加锁定数量
                        $assets_amount_model->updated_at = time();//修改时间
                    }else{//当用户总资产信息不存在
                        $assets_amount_model = new IecAssetsAmount();
                        $assets_amount_model->amount = 0;
                        $assets_amount_model->amount_lock = $token_assets->currency_total;
                        $assets_amount_model->user_id = $user->id;
                        $assets_amount_model->updated_at = time();
                    }
                    //用户MFCC钱包锁定数量修改
                    $user_wallet = Wallet::createWallet($user->id,'MFCC');
                    $user_wallet->amount_lock += $token_assets->currency_total;
                    $user_wallet->updated_at = time();

                    //添加放币记录信息
                    $message = [
                        'asset_number'=>$asset_number,//编号
                        'type_name'=>$token_assets->personnel_type,//人员类型
                        'release_total_number'=>$token_assets->currency_total,//释放总数量
                        'unlock_number'=>$every_time_number ? $every_time_number : $token_assets->currency_total,//每次解锁数量，当为阶段释放时显示每次释放数
                        'release_type'=>TokenAssets::typeArray()[$token_assets->type],//释放类型
                    ];
                   $message_res = Message::addAssetsMessage(Message::TYPE_COIN_PLAY,$user->id,$message);
                if($user_wallet->save() && $re_user_token->save() && $assets_amount_model->save() && $message_res){
                    //将释放时间加入队列，延期指定时间执行
                    $this->actionCalculatingPeriod($user->id,$token_assets_id,$every_time_number,$total_number_of_times);
                    $transaction->commit();
                }
                }catch (ErrorException $e) {
                    $transaction->rollBack();
                    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    return ['message' => '添加失败', 'code' => 444];
                }
            }
        return $this->redirect(['release-information', 'id' => $token_assets_id]);
    }

    //指定代币方法
    private function designatedUser($users,$token_assets_id){
        foreach ($users as $k => $v) {
            $transaction = Yii::$app->db->beginTransaction();
            try{
                $user = User::findOne(['username' => $v]);
                if(!$user){
                    return $this->render('/site/error',['name'=>$v.'手机号未注册','message'=>'']);
                }
                $res = RcUserToken::find()->where(['user_id' => $user->id, 'token_assets_id' => $token_assets_id,'deleted_at'=>1])->one();
                $token_assets = TokenAssets::findOne($token_assets_id);
                if ($res) {
                    return $this->render('/site/error',['name'=>'不能重复指定' . $v . '用户','message'=>'']);
                }
                $re_user_token = new RcUserToken();
                $total_number_of_times = null;
                $every_time_number = null;
                $asset_number = date('YmdHis',time()).$user->id;
                if($token_assets->type ==  TokenAssets::TYPE_PHASE_RELEASE){
                    //1.计算释放时间总差值
                    $diff_time = $token_assets->end_time - $token_assets->start_time;
                    //2.计算释放的总次数(向上取整)
                    $total_number_of_times = ceil($diff_time / ($token_assets->release_cycle * 86400))+1;
                    //3.计算每次释放数量，四舍五入保留4位小数
                    $every_time_number = round($token_assets->currency_total / $total_number_of_times,4);
                    $re_user_token->every_time_number = $every_time_number;
                }
                //用户资产信息记录
                $re_user_token->user_id = $user->id;
                $re_user_token->token_type_id = $token_assets->token_type_id;
                $re_user_token->token_assets_id = $token_assets_id;
                $re_user_token->available_balance = 0;
                $re_user_token->locked_balance = $token_assets->currency_total;
                $re_user_token->asset_number = $asset_number;
                $re_user_token->created_at = time();
                $re_user_token->updated_at = time();
                //用户总资产信息
                $assets_amount_model = IecAssetsAmount::find()->where(['user_id'=>$user->id])->one();
                if($assets_amount_model){//当用户总资产信息已存在
                    $assets_amount_model->amount_lock += $token_assets->currency_total;//添加锁定数量
                    $assets_amount_model->updated_at = time();//修改时间
                }else{//当用户总资产信息不存在
                    $assets_amount_model = new IecAssetsAmount();
                    $assets_amount_model->amount = 0;
                    $assets_amount_model->amount_lock = $token_assets->currency_total;
                    $assets_amount_model->user_id = $user->id;
                    $assets_amount_model->updated_at = time();
                }
                //用户MFCC钱包锁定数量修改
                $user_wallet = Wallet::createWallet($user->id,'MFCC');
                $user_wallet->amount_lock += $token_assets->currency_total;
                $user_wallet->updated_at = time();

                //添加放币记录信息
                $message = [
                    'asset_number'=>$asset_number,//编号
                    'type_name'=>$token_assets->personnel_type,//人员类型
                    'release_total_number'=>$token_assets->currency_total,//释放总数量
                    'unlock_number'=>$every_time_number ? $every_time_number : $token_assets->currency_total,//每次解锁数量，当为阶段释放时显示每次释放数
                    'release_type'=>TokenAssets::typeArray()[$token_assets->type],//释放类型
                ];
                $message_res = Message::addAssetsMessage(Message::TYPE_COIN_PLAY,$user->id,$message);
                if($user_wallet->save() && $re_user_token->save() && $assets_amount_model->save() && $message_res){
                    //将释放时间加入队列，延期指定时间执行
                    $this->actionCalculatingPeriod($user->id,$token_assets_id,$every_time_number,$total_number_of_times);
                    $transaction->commit();
                }
            }catch (ErrorException $e) {
                $transaction->rollBack();
                return $this->render('/site/error',['name'=>'添加失败','message'=>'']);
            }
        }
        return $this->redirect(['release-information', 'id' => $token_assets_id]);
    }


    //查看释放信息
    public function actionReleaseInformation($id){
        $query = RcUserToken::find()->joinWith(['user','tokenAssets','tokenType'])
            ->where(['and',['token_assets_id'=>$id],['deleted_at'=>1]]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('release-information', [
            'dataProvider' => $dataProvider,
        ]);
    }


    //释放资产周期计算

    /**
     * @param $user_id
     * @param $token_assets_id 资产id
     * @param null $every_time_number 阶段释放类型每阶段释放数
     * @param null $total_number_of_times 阶段释放类型的总释放数
     */
    private function actionCalculatingPeriod($user_id,$token_assets_id,$every_time_number=null,$total_number_of_times=null){
        $token_assets = TokenAssets::findOne($token_assets_id);
        switch($token_assets->type){
            case TokenAssets::TYPE_ONE_RELEASE;//一次释放
                $release_time = $token_assets->end_time - time();
                \Yii::$app->queue->delay($release_time)->push(new AssetsQueueController([
                    'user_id' => $user_id,
                    'token_assets_id' => $token_assets_id,
                    'amount' => $token_assets->currency_total,
                ]));
                break;
            case TokenAssets::TYPE_PHASE_RELEASE;//阶段释放
                \Yii::$app->queue->push(new AssetsQueueController([
                    'user_id' => $user_id,
                    'token_assets_id' => $token_assets_id,
                    'amount' => $every_time_number,
                ]));
                break;
            default;
                break;
        }
    }


    //删除用户的资产
    public function actionDeleteUserAssets($id){
        try{
            $transaction = Yii::$app->db->beginTransaction();
            $user_assets = RcUserToken::findOne($id);
            //获取用户MFCC钱包
            $user_wallet = Wallet::findOne(['user_id'=>$user_assets->user_id,'symbol'=>'MFCC']);
            //获取用户总资产记录信息
            $user_all_assets = IecAssetsAmount::findOne(['user_id'=>$user_assets->user_id]);
            //获取资产信息
            $token_assets = TokenAssets::findOne(['id'=>$user_assets->token_assets_id]);
            $user_assets->deleted_at = time();
            //扣除钱包对应的待释放数量
            $user_wallet->amount_lock -= $user_assets->locked_balance;
            $user_wallet->updated_at = time();
            //扣除用户总资产记录里对应的待释放数量
            $user_all_assets->amount_lock -= $user_assets->locked_balance;
            $user_all_assets->updated_at = time();

            //添加放币记录信息
            $message = [
                'asset_number'=>$user_assets->asset_number,//编号
                'type_name'=>$token_assets->personnel_type,//人员类型
                'release_total_number'=>$token_assets->currency_total,//释放总数量
                'released_number'=>$user_assets->available_balance ,//已释放数量
                'delete_number'=>$user_assets->locked_balance ,//删除的未释放数量
            ];
            $message_res = Message::addAssetsMessage(Message::TYPE_DELETE_ASSETS,$user_assets->user_id,$message);
            if( $user_wallet->save() &&$user_all_assets->save() && $user_assets->save() && $message_res){
                $transaction->commit();
                return $this->redirect(['release-information','id'=>$user_assets->token_assets_id]);
            };
        }catch (\ErrorException $e){
            $transaction->rollBack();
            return $this->render('/site/error', ['name' => '删除失败','message' => '']);
        }
    }
}