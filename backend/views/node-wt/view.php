<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\node\NodeWt */

$this->title = "超级节点";
// $this->params['breadcrumbs'][] = ['label' => '节点列表', 'url' => ['index']];
// $this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="node-wt-view">

    <p>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'income',
            'alte_price',
            'alte_symbol',
            'alte_number',
            'alte_rules:ntext',
            'total_awards',
            'reward_symbol',
            'super_number',
            'super_factor',
            'super_rules:ntext',
            'super_price',
            'super_symbol',
            'super_explain:ntext',
            'created_at',
            'update_at',
        ],
    ]) ?>

</div>
