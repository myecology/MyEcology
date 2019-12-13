<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Deposit */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Deposits', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="deposit-view">

    <div class="box box-success">
        <div class="box-body">

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
            'transaction_hash',
            [
                'label' => '用户名',
                'value' => function($model){
                    return $model->user->username;
                }
            ],
            [
                'label' => '收款地址',
                'value' => function($model){
                    return $model->walletAddress->address;
                }
            ],
            'symbol',
            'amount',
            'status',
            'source',
            'txid',
//            'address_id',
            'address',
            'fee',
            'fee_symbol',
//            'remark:ntext',
            'created_at:datetime',
            'updated_at:datetime',
                ],
            ]) ?>
        </div>
    </div>
    

</div>
