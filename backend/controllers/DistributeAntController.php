<?php

namespace backend\controllers;

use backend\models\User;
use backend\modules\member\models\search\WalletLogSearch;
use backend\modules\member\models\search\WalletSearch;
use Yii;
use yii\db\Exception;
use yii\web\Controller;
use common\models\Wallet;
use common\models\WalletLog;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use common\models\Currency;
use backend\models\Setting;
use common\models\Fill;
use backend\models\search\FillSearch;
use common\models\DistrbuteLog;
use common\models\ReleaseDistrbuteLog;

/**
 * ShopController implements the CRUD actions for Shop model.
 */
class DistributeAntController extends Controller
{

    /**
     * @return string
     */
    public function actionIndex()
    {
        $model = new WalletLogSearch();
        $param = Yii::$app->request->queryParams;
        $dataProvider = $model->searchShareBonus($param);
        // var_dump(Setting::read('symbol_list','reward'));die;
        //奖励拥有币种用户
        $list = explode(",",Setting::read('symbol_list','reward'));
        //奖励币种
        $symbol = explode(",",Setting::read('symbol_reward','reward'));
        //总antToken数
        $all_antToken = Wallet::find()->where(['and',['symbol'=>$list[0]],['>','amount',0]])->sum('amount');
        //拥有antToken总人数
        $count_antToken = Wallet::find()->where(['and',['symbol'=>$list[0]],['>','amount',0]])->count();
        //获取所有币种
        return $this->render('index', [
            'searchModel' => $model,
            'all_antToken' => $all_antToken,
            'count_antToken' => $count_antToken,
            'dataProvider' => $dataProvider,
            'symbollist' => $list, 
            'rewardlist' => $symbol,
        ]);
    }

    public function actionMessage(){
        $param = Yii::$app->request->queryParams;
        if(empty($param['share_ant_number']) || empty($param['symbol']) || empty($param['reward_symbol'])){
            return $this->redirect(['distribute-ant/index']);
        }
        $model = new WalletSearch();
        //查询当前拥有该币种的用户
        $list = $model->WalletList($param);
        return $this->render('message', [
                'share_ant_number'=>$param['share_ant_number'],
                'symbol' => $param['symbol'],
                'reward_symbol' => $param['reward_symbol'],
                'name' => empty($param['name'])?"":$param['name'],
                'dataProvider' => $list,
                'searchModel' => $model,
            ]);
    }

    /**
     * 对一个数字进行保留位数处理
     * @param $number
     * @param $position
     * @return float
     */
    public function counterAddAmount ($number, $position){
        $number = round($number,$position);
        return $number;
    }

    /**
     * 后台通证分红
     * @return bool|\yii\web\Response
     * @throws Exception
     * @throws \yii\base\ErrorException
     */
    public function actionShareBonus(){
        ini_set('max_execution_time', 0);
        $param = Yii::$app->request->queryParams;
        //后台分发的ant总数
        $share_ant_number = (int)$param['share_ant_number'];
        //奖励币种
        $rewardsymbol = $param['reward_symbol'];
        //拥有币种
        $symbol = $param['symbol'];
        //获取钱包中antToken币种的金额总数
        $wallet_model = new Wallet();
        $where = ['symbol'=>$rewardsymbol];
        $total_account = $wallet_model->find()->where($where)->sum('amount');
        if($share_ant_number >0 && $total_account > 0){
            //获取钱包中antToken币种的金额大于0的所有用户
            $all_users_wallet = $wallet_model->find()
                ->where(['>','amount',0])
                ->andWhere($where)
                ->all();
            //记录满足条件的用户钱包ant数量变化
            $transaction = \Yii::$app->db->beginTransaction();
            try{
                //添加活动
                $data = [
                    'name' => $param['name'],
                    'release_symbol' => $symbol,
                    'have_symbol'  => $rewardsymbol,
                    'total_amount' => $share_ant_number,
                    'release_num' => count($all_users_wallet),
                    'status' => 2,
                ];
                $model = DistrbuteLog::Add($data);
                foreach ($all_users_wallet as $key=>$user_wallet){
                    $add_amount = $this->counterAddAmount($user_wallet->amount * $share_ant_number / $total_account,8);
                    /*$user_wallet->shareBonus($user_wallet->user_id,$add_amount,$symbol);
                    $user = User::find()->where('id = '.$user_wallet->user_id)->one();
                    //发送消息
                    \common\models\Message::addMessage(\common\models\Message::TYPE_SHARE_BONUS, $user, $symbol, $add_amount, $user_wallet);*/
                    //添加释放成功的用户
                    $datas = [
                        'user_id' => $user_wallet->user_id,
                        'symbol' => $symbol,
                        'amount' => $add_amount,
                        'distrbute_id' => $model->id,
                        'remark' => "通证分红释放",
                        'status' => 10,
                    ];
                    ReleaseDistrbuteLog::AddLog($datas);
                }
                $transaction->commit();
                return $this->redirect(['distribute-ant/index']);
            }catch(\yii\base\ErrorException $e){
                $transaction->rollBack();
                throw new \yii\base\ErrorException($e->getMessage());
            }
            return false;
        }
        return $this->redirect(['distribute-log/view','id'=>$model->id]);
    }

    /**
     * excel表格下载
     * @return mixed
     */
    public function actionDownloadExcel()
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="通证分红日志列表.csv"');
        header('Cache-Control: max-age=0');
        set_time_limit(0);
        ini_set("memory_limit","256M");
        $walletLogModel = new WalletLog();

        $startTime = Yii::$app->request->get('start_time');
        $endTime = Yii::$app->request->get('end_time');
        $startTimeStr = strtotime($startTime.'00:00:00');
        $endTimeStr = strtotime($endTime.'23:59:59');

        if ($startTime && $endTime) {
            $where = ['and','type='.WalletLog::TYPE_SHARE_BONUS,['between','created_at',$startTimeStr,$endTimeStr]];
        }else if($startTime && !$endTime){
            $where = ['and','type='.WalletLog::TYPE_SHARE_BONUS,['>=','created_at',$startTimeStr]];

        }else if(!$startTime && $endTime){
            $where = ['and','type='.WalletLog::TYPE_SHARE_BONUS,['<=','created_at',$endTimeStr]];
        }else{
            $where['type'] = WalletLog::TYPE_SHARE_BONUS;
        }
        $count = $walletLogModel->find()->where($where)->count();

        $limit = $count < 2000 ? 1 : ceil($count / 2000);

        $fp = fopen('php://output', 'a');

        $head = array('币种', '用户名', '类型', '数量','时间','备注');
        foreach ($head as $i => $v) {
            $head[$i] = iconv("utf-8","gb2312//IGNORE",$v);
        }

        fputcsv($fp, $head);

        $i = 1;
        $n = 0;
        $libType  = \common\models\WalletLog::$lib_type;
        while($n < $limit) {
            $list = $walletLogModel->find()->with('user')->where($where)->offset($n * 2000)->limit(2000)->asArray()->all();
            foreach ($list as $val) {
                $newData = [
                    'symbol' => ' '.$val['symbol'],
                    'user'   => ' ' .iconv("utf-8","gb2312//IGNORE",$val['user']['username']),
                    'type'   => iconv("utf-8","gb2312//IGNORE",$libType[$val['type']]),
                    'amount' => ' '.$val['amount'],
                    'created_at' => ' ' .date('Y-m-d H:i:s',$val['created_at']),
                    'remark' => ' ' .iconv("utf-8","gb2312//IGNORE",$val['remark']),
                ];
                if (!$newData) {
                    break;
                }
                fputcsv($fp, $newData);
                $i++;
            }
            if ($i > 2000) {//读取一部分数据刷新下输出buffer
                ob_flush();
                flush();
                $i = 0;
            }
            $n++;
        }
        fclose($fp);
        exit;
    }

    /**
     * 币种拥有用户统计
     * @return [type] [description]
     */
    public function actionAjaxTotal(){
        $symbol = Yii::$app->request->post("symbol");
        //总antToken数
        $all_antToken = Wallet::find()->where(['and',['symbol'=>$symbol],['>','amount',0]])->sum('amount');
        //拥有antToken总人数
        $count_antToken = Wallet::find()->where(['and',['symbol'=>$symbol],['>','amount',0]])->count();
        $data = [
            'total' => empty($all_antToken)?0:$all_antToken,
            'count' => $count_antToken,
        ];
        echo json_encode($data);die;
    }

    /**
     * 添加补发列表
     * @return [type] [description]
     */
    public function actionAjaxAdd(){
        $user = Yii::$app->request->post("user");
        if(empty($user)){
            echo json_encode(['status'=>4200,'msg'=>'请选择补发用户']);
            die;
        }
        $post = Yii::$app->request->post();
        //查询当前拥有该币种的用户
        $wallet_model = new Wallet();
        $where = ['symbol'=>$post['release_symbol']];
        $transaction = \Yii::$app->db->beginTransaction();//开启事务
        try{
            if(empty($post['ids'])){
                $all_users_wallet = $wallet_model->find()
                ->where(['>','amount',0])
                ->andWhere($where)
                ->all();
                //添加活动
                $data = [
                    'name' => $post['name'],
                    'release_symbol' => $post['symbol'],
                    'have_symbol'  => $post['release_symbol'],
                    'total_amount' => $post['amount'],
                    'release_num' => count($all_users_wallet),
                    'status' => 1,
                ];
                $res = DistrbuteLog::Add($data);
                $ids = $res->id;
            }else{
                $ids = $post['ids'];
            }
            foreach($user as $val){
                $model = new Fill();
                $user_fill = Fill::find()
                ->where(['user_id'=>$val])
                ->andWhere(['distrbute_id'=>$ids])->one();
                //判断是否重复添加用户
                if(empty($user_fill)){
                    $data = [
                        'user_id' => $val,
                        'symbol' => $post['symbol'],
                        'release_symbol' => $post['release_symbol'],
                        'amount' => $post['amount'],
                        'distrbute_id' => $ids,
                    ];
                    if(!$model->AddFill($data)){
                        throw new \ErrorException("添加失败", 4200);
                    }
                }
            }
            $transaction->commit();
            $data = ['status' => 200,'msg' => '添加成功','data'=>$ids];
        }catch(\ErrorException $e){
            $transaction->rollBack();
            $data = ['status' => 4200,'msg' => $e->getMessage()];
        }
        echo json_encode($data);die;
    }
    /**
     * 删除
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function actionDelete($id){
        $model = Fill::findOne($id);
        $model->is_del = 2;
        $model->save();
        return $this->redirect(['fill']);
    }
    /**
     * 补发列表
     * @return [type] [description]
     */
    public function actionFill(){
        $searchModel = new FillSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('fill', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 补发分红
     * @return [type] [description]
     */
    public function actionFillUser(){
        ini_set('max_execution_time', 0);
        //查询所有没有补发的信息
        $user = Fill::find()->where(['is_del'=>1])
        ->andWhere(['status'=>1])->asArray()->all();
        $transaction = \Yii::$app->db->beginTransaction();
        try{
            foreach($user as $key=>$val){
                //获取钱包中antToken币种的金额总数
                $wallet_model = new Wallet();
                $where = ['symbol'=>$val['symbol']];
                $total_account = $wallet_model->find()->where($where)->sum('amount');
                //获取用户该钱包数量
                $user_wallet = $wallet_model->find()
                    ->where(['>','amount',0])
                    ->where(['user_id'=>$val['user_id']])
                    ->andWhere($where)
                    ->one();
                //用户占有金额
                $add_amount = $this->counterAddAmount($user_wallet->amount * $val['amount'] / $total_account,8);
                //释放通证分红
                /*$user_wallet->shareBonus($user_wallet->user_id,$add_amount,$val['release_symbol']);
                $user = User::find()->where('id = '.$user_wallet->user_id)->one();
                //发送消息
                \common\models\Message::addMessage(\common\models\Message::TYPE_SHARE_BONUS, $user, $val['release_symbol'], $add_amount, $user_wallet);*/
                //删除补发信息，更改补发状态 
                Fill::Del($val['id']);
                //添加通证分红释放记录
                $data = [
                    'user_id' => $val['user_id'],
                    'amount' => $add_amount,
                    'symbol' => $val['release_symbol'],
                    'distrbute_id' => $val['distrbute_id'],
                    'remark' => '补发通证分红释放',
                    'status' => 10,
                ];
                ReleaseDistrbuteLog::AddLog($data);
                //修改通证分红记录
                DistrbuteLog::UpdateOne($val['distrbute_id']);
            }
            $transaction->commit();
        }catch(\ErrorException $e){
            $transaction->rollBack();
            throw new \yii\base\ErrorException($e->getMessage());
        }
        return $this->redirect(['distribute-ant/index']);
    }

    /**
     * 补发单条活动
     * @return [type] [description]
     */
    public function actionFillOne(){
        //获取通证分红信息id
        $param = Yii::$app->request->post();
        $distrbute = DistrbuteLog::findOne($param['id']);
        $wallet_model = new Wallet();
        $where = ['symbol'=>$distrbute->have_symbol];
        $total_account = $wallet_model->find()->where($where)->sum('amount');
        //获取钱包中antToken币种的金额大于0的所有用户
        $all_users = Fill::find()
        ->where(['distrbute_id'=>$param['id']])
        ->andWhere(['is_del'=>1])
        ->andWhere(['status'=>1])->all();
        $transaction = \Yii::$app->db->beginTransaction();
        try{
            //循环所有信息
            foreach($all_users as $user){
                //获取用户钱包信息
                $user_wallet = $wallet_model->find()
                    ->where($where)
                    ->andWhere(['user_id'=>$user->user_id])
                    ->one();
                //该用户获取的币种数量
                $add_amount = $this->counterAddAmount($user_wallet->amount * $distrbute->total_amount / $total_account,8);
                $data = [
                    'user_id' => $user->user_id,
                    'symbol' => $distrbute->release_symbol,
                    'amount' => $add_amount,
                    'distrbute_id' => $param['id'],
                    'remark' => "补发通证分红释放",
                    'status' => 10,
                ];
                ReleaseDistrbuteLog::AddLog($data);
            }
            //修改通证信息状态
            $distrbute->status = 2;
            if(!$distrbute->save()){
                throw new \ErrorException("修改状态失败", 1);
            }
            $transaction->commit();
            $data = ['status'=>200,'msg'=>'补发通证分红成功'];
        }catch(\ErrorException $e){
            $transaction->rollBack();
            $data = ['status'=>4200,'msg'=>$e->getMessage()];
        }
        echo json_encode($data);die;
    }
}