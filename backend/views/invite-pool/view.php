<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\InvitePool */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Invite Pools', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invite-pool-view">

    <div class="box box-success">
        <div class="box-body">
            <p>
                <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ],
                ]) ?>
            </p>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
            'currency_id',
            'symbol',
            'amount',
            'amount_left',
            'created_at',
            'expired_at',
            'status',
            'prize',
            'prize_registerer',
            'prize_inviter',
            'prize_grand_inviter',
                ],
            ]) ?>
        </div>
    </div>
    

</div>
