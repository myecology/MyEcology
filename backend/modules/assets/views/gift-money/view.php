<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\assets\models\GiftMoney */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '红包记录', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gift-money-view">

    <div class="box box-success">
        <div class="box-body">

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    [
                        'label' => '发送人',
                        'value' => function($model){
                            return $model->user->username;
                        }
                    ],
                    'amount',
                    'amount_left',
                    'created_at:datetime',
                    'status',
                    'type',
                    'amount_unit',
                    'count',
                    'expired_at',
                    'description',
                    'bind_taker',
                    'symbol',
                ],
            ]) ?>
        </div>


        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>领取人</th>
                    <th>领取时间</th>
                    <th>数量</th>
                    <th>回复内容</th>
                    <th>回复时间</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($model->giftMoneyTaker as $taker){ ?>
                <tr>
                    <td><?= isset($taker->user->username) ? $taker->user->username : '未领取' ?></td>
                    <td><?= $taker->taken_at ? date('Y-m-d H:i:s', $taker->taken_at) : '' ?></td>
                    <td><?= $taker->amount ?></td>
                    <td><?= $taker->reply ?></td>
                    <td><?= $taker->reply_time ? date('Y-m-d H:i:s', $taker->reply_time) : '' ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

    </div>
    

</div>
