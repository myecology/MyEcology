<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Withdraw */

$this->title = '#' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '参数列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="withdraw-view">

    <div class="box box-success">
        <div class="box-body">
            <p>
                <?= Html::a('返回', ['index',], ['class' => 'btn btn-primary']) ?>
            </p>

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'user.username',
                    'address',
                    'symbol',
                    'amount',
                    'fee',
                    'fee_symbol',
                    'created_at:datetime',
                    'updated_at:datetime',
                    [
                        'attribute' => 'status',
                        'value' => function ($model) {
                            return \common\models\Withdraw::$lib_status[$model->status];
                        }
                    ],
//                    'checker_id' => 'Checker ID',
                    'check_time:datetime',
                    'source',
                    [
                        'attribute' => 'remark',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $content = $model->remark;
                            if(\common\modules\ethereum\models\EthereumService::txHashValidEth($content)){
                                $content .= Html::a(' <i class="glyphicon glyphicon-link"></i>', 'https://etherscan.io/tx/'.$content, ['target' => '_blank']);
                            }

                            return $content;
                        }
                    ],
                ],
            ]) ?>
        </div>
    </div>


</div>
