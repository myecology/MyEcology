<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $orderModel common\models\bank\Order */
/* @var $model backend\modules\bank\models\ReissuedOrderModel  */
/* @var $profitModel common\models\bank\Profit  */

$this->title = '补发收益';
$this->params['breadcrumbs'][] = ['label' => '理财订单', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-create">

    <div class="box box-success">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <h3>产品信息</h3>
                    <?= \yii\widgets\DetailView::widget([
                        'model' => $orderModel,
                        'attributes' => [
                            [
                                'label' => '用户名',
                                'value' => function($model){
                                    return $model->user->username;
                                }
                            ],
                            [
                                'label' => '产品名称',
                                'value' => function($model){
                                    return $model->product->name;
                                }
                            ],
                            'amount',
                            [
                                'label' => '状态',
                                'value' => function($model){
                                    $statusArray = $model::statusArray();
                                    return $statusArray[$model->status];
                                }
                            ],
                            'day',
                            'created_at:datetime',
                        ],
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <h3>最后一条收益</h3>
                    <?php if(!empty($profitModel)):?>
                    <?= \yii\widgets\DetailView::widget([
                        'model' => $profitModel,
                        'attributes' => [
                            [
                                'label' => '用户名',
                                'value' => function($model){
                                    return $model->user->username;
                                }
                            ],
                            'amount',
                            'symbol',
                            'created_at:datetime',
                        ],
                    ]) ?>
                    <?php else: ?>
                        <h5>暂无数据</h5>
                    <?php endif;?>
                </div>
            </div>
            <div class="order-form">
                <?php $form = ActiveForm::begin(); ?>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="control-label">  　</label>
                            <div><?= Html::submitButton('补发', ['class' => 'btn btn-success']) ?></div>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
    

</div>
