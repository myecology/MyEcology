<?php
namespace api\modules\v1\controllers;

use api\controllers\APIFormat;
use backend\models\Setting;
use common\models\UserTree;
use common\models\Supernode;
use common\models\SupernodeProfit;
use common\models\SupernodeForm;
use yii\data\Pagination;
use yii\helpers\Json;
use Yii;

/**
 *  超级节点
 */
class SupernodeController extends BaseController
{
    /**
     * 超级节点成员
     *
     * @return void
     */
    public function actionIndex()
    {
        $userTree = UserTree::findOne(['name' => Yii::$app->user->identity->username]);
        $andWhere = [
            'and',
            ['=', 'node', UserTree::NODE_ACTIVE],
            ['<=', 'node_lvl', $userTree->node_lvl + 1],
            ['>', 'lft', $userTree->lft],
            ['<', 'rgt', $userTree->rgt],
            ['=', 'root', $userTree->root]
        ];
        $data = UserTree::findOne(['name' => Yii::$app->user->identity->username])
            ->children()->select("name,userid,uid,node,node_lvl,created_at")->with(['user'])
            ->andWhere($andWhere);
        
        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'pageSizeLimit' => [1, 100],
            'totalCount' => $data->count(),
        ]);
        $pagination->validatePage = false;

        $data = $data->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();
        
        $memberNums = UserTree::findOne(['name' => Yii::$app->user->identity->username])->children()->andWhere(['AND',['=', 'node', UserTree::NODE_DELETED], ['<=', 'node_lvl', $userTree->node_lvl]])->count();
        
        $_meta = [
            'totalCount' => $pagination->totalCount,
            'pageCount' => $pagination->pageCount,
            'currentPage' => $pagination->getPage() + 1,
            'perPage' => $pagination->getPageSize(),
        ];

        return APIFormat::success(['items' => $data, 'memberNums' => $memberNums, '_meta' => $_meta]);
    }

    /**
     * 节点团队成员
     *
     * @return void
     */
    public function actionMember()
    {
        $uid = Yii::$app->request->get('uid');
        
        $userTree = UserTree::findOne(['uid' => $uid]);
        $andWhere = [
            'AND',
            ['<=', 'node_lvl', $userTree->node_lvl],
            ['>', 'lft', $userTree->lft],
            ['<', 'rgt', $userTree->rgt],
            ['=', 'root', $userTree->root]
        ];
        if($uid == Yii::$app->user->identity->id){
            $andWhere[] = ['=', 'node', UserTree::NODE_DELETED];
        }
        $data = UserTree::findOne(['uid' => $uid])->children()
            ->select("name,userid,uid,node,created_at")->with(['user'])
            ->andWhere($andWhere);
        
        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'pageSizeLimit' => [1, 100],
            'totalCount' => $data->count(),
        ]);
        $pagination->validatePage = false;

        $data = $data->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();
        
        $supernodeNums = UserTree::findOne(['uid' => $uid])->children()->andWhere(['AND', ['=', 'node', UserTree::NODE_ACTIVE], ['<=', 'node_lvl', $userTree->node_lvl + 1]])->count();
        
        $_meta = [
            'totalCount' => $pagination->totalCount,
            'pageCount' => $pagination->pageCount,
            'currentPage' => $pagination->getPage() + 1,
            'perPage' => $pagination->getPageSize(),
        ];

        return APIFormat::success(['items' => $data, 'supernodeNums' => $supernodeNums, '_meta' => $_meta]);
    }

    /**
     * 超级节点收益
     *
     * @return void
     */
    public function actionProfit()
    {
        $data = SupernodeProfit::find()->with(['user'])->select(["*", "round(amount,2) as _amount"])->where(['uid' => Yii::$app->user->identity->id]);
        $sumProfit = SupernodeProfit::find()->where(['uid' => Yii::$app->user->identity->id, 'type' => SupernodeProfit::TYPE_BANK_PRODUCT])->sum('amount');

        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'pageSizeLimit' => [1, 100],
            'totalCount' => $data->count(),
        ]);
        $pagination->validatePage = false;

        $data = $data->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['created_at' => SORT_DESC])
            ->asArray()
            ->all();

        //  额外的参数
        $supernode = Supernode::find()->where(['uid' => Yii::$app->user->identity->id, 'status' => Supernode::STATUS_ACTIVE])->orderBy('id desc')->one();
        $unlock = Setting::read('supernode_unlock_day');
        $symbol = Setting::read('supernode_symbol');
        $memberNums = UserTree::findOne(['uid' => Yii::$app->user->identity->id])->children()->andWhere(['node' => UserTree::NODE_DELETED])->count();
        $supernodeNums = UserTree::findOne(['uid' => Yii::$app->user->identity->id])->children(1)->andWhere(['node' => UserTree::NODE_ACTIVE])->count();
        $_extra = [
            'symbol' => $symbol,
            'time' => $supernode['created_at'] + $unlock * 86400,
            'sumProfit' => $sumProfit ? sprintf("%.2f", $sumProfit) : '0',
            'totalMember' => $memberNums + $supernodeNums,
        ];
            
        $_meta = [
            'totalCount' => $pagination->totalCount,
            'pageCount' => $pagination->pageCount,
            'currentPage' => $pagination->getPage() + 1,
            'perPage' => $pagination->getPageSize(),
        ];

        return APIFormat::success(['items' => $data, '_extra' => $_extra, '_meta' => $_meta]);
    }


    /**
     * 购买超级节点
     *
     * @return void
     */
    public function actionBuy()
    {
        $model = new SupernodeForm();
        $model->setAttributes(Yii::$app->request->post());
        if($model->buySupernode()){
            return APIFormat::success(true);
        }
        return APIFormat::error(4100, $model->errors);
    }

    /**
     * 退出超级节点
     *
     * @return void
     */
    public function actionRedeem()
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try{
            //  判断是否该用户
            $model = UserTree::find()->where(['name' => Yii::$app->user->identity->username, 'node' => UserTree::NODE_ACTIVE])->one();
            if(!$model){
                throw new \yii\base\ErrorException('无法找到目标');
            }

            //  改变状态
            $parentNode = UserTree::findOne(['uid' => Yii::$app->user->identity->id])->parents()->andWhere(['node' => UserTree::NODE_ACTIVE])->one();
            $nodeLvl = $parentNode ? $model['node_lvl'] - 1 : 0;
            $model->node_lvl = $nodeLvl > 0 ? $nodeLvl : 0;
            $model->node = UserTree::NODE_DELETED;
            if(false === $model->save()){
                throw new \yii\base\ErrorException('退出超级节点失败');
            }
            

            $supernodeModel = Supernode::find()->where(['uid' => Yii::$app->user->identity->id, 'status' => Supernode::STATUS_ACTIVE])->orderBy('id desc')->one();
            if(!$supernodeModel){
                throw new \yii\base\ErrorException('找不到购买记录');
            }
            $supernodeModel->status = Supernode::STATUS_DELETED;
            $supernodeModel->description = '退出超级节点时间为：' . date('Y-m-d H:i:s');
            if(false === $supernodeModel->save()){
                throw new \yii\base\ErrorException('超级节点状态更新失败');
            }

            //  更新超级节点深度
            $model->updateNodeDepth(false);

            if(YII_DEBUG){
                $root = UserTree::findOne(['id' => $model->root]);
                $childrens = $root->children()->all();
                $data = [];
                foreach($childrens as $val){
                    $data[] = [
                        'root' => $val->root,
                        'node' => $val->node,
                        'node_lvl' => $val->node_lvl,
                        'name' => $val->name,
                        'created_at' => $val->created_at,
                        'lft' => $val->lft,
                        'rgt' => $val->rgt,
                    ];
                }

                //  记录日志
                $message = Yii::$app->user->identity->username . '退出超级节点' . ',当前状态为 ' . Json::encode($data);
                Yii::info($message, 'tree');
            }

            //  发送通知 - 退出超级节点
            $user = \api\models\User::findOne($supernodeModel->uid);
            \common\models\Message::addMessage(\common\models\Message::TYPE_SUPERNODE_EXIT, $user, 'IEC', $supernodeModel->amount, $supernodeModel);

            $transaction->commit();
            return APIFormat::success(['node' => $model->node]);
        }catch(\Throwable $th){
            $transaction->rollBack();
            $msg = $th->getMessage();
        }
        return APIFormat::error(4101, $msg);
    }

    /**
     * 获取购买超级节点的参数
     *
     * @return void
     */
    public function actionParams()
    {
        return APIFormat::success([
            'symbol' => Setting::read('supernode_symbol'),
            'fee' => Setting::read('supernode_fee'),
            'amount' => UserTree::supernodeBuyAmount(),
            'rule' => Setting::read('supernode_rule'),
        ]);
    }

    /**
     * 判断是否超级节点
     *
     * @return void
     */
    public function actionVerify()
    {
        $node = UserTree::find()->where(['uid' => Yii::$app->user->identity->id, 'node' => UserTree::NODE_ACTIVE])->one();
        return $node ? APIFormat::success(true) : APIFormat::success(false);
    }
}