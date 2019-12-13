<?php

namespace api\modules\v1\controllers;

use api\controllers\APIFormat;
use common\models\Invitation;
use common\models\InviteReward;
use common\models\bank\Order;
use common\models\bank\Product;
use common\models\shop\MallLog;

class InviteController extends \api\modules\v1\controllers\BaseController
{
    public function actionReward()
    {
        $searchModel = new Invitation();
        $dataProvider = $searchModel->search(\Yii::$app->request->post(), \Yii::$app->user->getId());

        $summary = $searchModel->summaryAsInviter(\Yii::$app->user->getId());

        $registerers = [];
        /**
         * @param InviteReward $invite_reward
         */
        foreach ($dataProvider->getModels() as $i => $invite_reward) {
            $registerers[] = $invite_reward->pageData();
        }

        $summary['page_total'] = (string)$dataProvider->getPagination()->pageCount;
        $summary['count_total'] = (string)$dataProvider->getTotalCount();
        return APIFormat::success([
            'summary' => $summary,
            'list' => $registerers,
        ]);
    }

    public function actionTeam()
    {
        $searchModel = new Invitation();
        $dataProvider = $searchModel->search(\Yii::$app->request->post(), \Yii::$app->user->getId());

        $summary = $searchModel->summaryAsInviter(\Yii::$app->user->getId());

        $registerers = [];
        /**
         * @param Invitation $invite_invitation
         */
        foreach ($dataProvider->getModels() as $i => $invite_invitation) {
            $registerers[] = $invite_invitation->attributeForReward();
        }

        $summary['page_total'] = (string)$dataProvider->getPagination()->pageCount;
        $summary['count_total'] = (string)$dataProvider->getTotalCount();

        return APIFormat::success([
            'summary' => $summary,
            'list' => $registerers,
        ]);
    }

    /**
     * 用户十级统计
     * @return [type] [description]
     */
    public function actionExploit(){
        //获取用户当前id
        $user_id = \Yii::$app->user->getId();
        //查询产品名称
        $listname = Product::find()->select("id,name,symbol")->asArray()->all();
        foreach($listname as $key=>$val){
            $list[$key]['list'] = Invitation::find()
            ->select("sum(a.amount) as amount")
            ->leftJoin("iec_bank_order as a","a.uid=iec_invitation.registerer_id")
            ->where(['iec_invitation.inviter_id'=>$user_id])
            ->andwhere(['between','level',1,10])
            ->andwhere(["product_id"=>$val['id']])
            ->andwhere(['a.status'=>Order::STATUS_LOCK])
            ->Orwhere("a.status=".Order::STATUS_PROFIT." and iec_invitation.inviter_id=".$user_id." and product_id=".$val['id']." and level between 1 and 10")
            ->asArray()
            ->one();


            $list[$key]['number'] = Invitation::find()
            ->leftJoin("iec_bank_order as a","a.uid=iec_invitation.registerer_id")
            ->where(['iec_invitation.inviter_id'=>$user_id])
            ->andwhere(['between','level',1,10])
            ->andwhere(["product_id"=>$val['id']])
            ->andwhere(['a.status'=>Order::STATUS_LOCK])
            ->Orwhere("a.status=".Order::STATUS_PROFIT." and iec_invitation.inviter_id=".$user_id." and product_id=".$val['id']." and level between 1 and 10")
            ->groupBy("registerer_id")
            ->asArray()
            ->count();
            $list[$key]['name'] = $val['name'];
            $list[$key]['symbol'] = $val['symbol'];
        }
        $data = array();
        foreach($list as $k=>$v){
            if($v['list']["amount"] != null){
                $data[] = $list[$k];
            }
        }
        // var_dump($data);die;
        return APIFormat::success([
            'summary' => $data,
            'count' => count($data),
        ]);
    }

    /**
     * 商城酒链统计
     * @return [type] [description]
     */
    public function actionMallWt(){
        $user_id = \Yii::$app->user->getId();
        $list = Invitation::find()
        ->select("sum(number) as number,count(registerer_id) as count")
        ->leftJoin("iec_mall_log as a", "a.user_id=registerer_id")
        ->where(['iec_invitation.inviter_id'=>$user_id])
        ->andwhere(['between','level',1,2])
        ->andwhere(['a.status'=>2])
        ->asArray()->all();
        if($list[0]['number'] == null){
            $list[0]['number'] = 0;
        }
        return APIFormat::success([
            'summary' => $list
        ]);
    }
}
