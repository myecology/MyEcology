<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\DepositSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '充值列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="deposit-index table-responsive">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="box box-success">
        <div class="box-body">

                    <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
        'columns' => [


//                    'id',
//            'transaction_hash',
            [
                'label' => '用户名',
                'attribute' => 'username',
                'value' => 'user.username',
            ],
            [
                'label' => '充值地址',
                'attribute' => 'address',
                'value' => 'walletAddress.address',
            ],
            'symbol',
            'amount',
            'created_at:datetime',
            //'updated_at',
            //'status',
            //'source',
            //'txid',
            //'address_id',
            //'address',
            //'fee',
            //'fee_symbol',
            //'remark:ntext',

                    ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
                ],
            ]); ?>
                </div>
    </div>

</div>
