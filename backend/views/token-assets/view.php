<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\TokenAssets */

$this->title = '代币信息';
$this->params['breadcrumbs'][] = ['label' => '代币设置', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$attributes = [
    'id',
    'personnel_type',
    'remark',
    [
        'attribute'=>'token_type_id',
        'value'=>function($model){
            $tokenArray = ArrayHelper::map(\common\models\RcTokenType::find()->all(), 'id', 'name');
            return $tokenArray[$model->token_type_id];
        },
    ],
    'currency_total',
    'start_time:datetime',
    'end_time:datetime',
    'release_cycle',
    [
        'attribute' => 'type',
        'value' => function($model){
            $typeArray = $model::typeArray();
            return $typeArray[$model->type];
        },
    ],
];

$data = json_decode($model->stage_data);
if($data){
    foreach ($data as $k => $v){
//    var_dump($v);die;
       $start_time = [
            'attribute'=>'start_time_'.$k,
            'value'=>$v->start_time
        ];
        $release_time = [
            'attribute'=>'release_time_'.$k,
            'value'=>$v->release_time
        ];
        $unlocking_ratio = [
            'attribute'=>'unlocking_ratio_'.$k,
            'value'=>$v->unlocking_ratio
        ];
        array_push($attributes,$start_time,$release_time,$unlocking_ratio);

    };
}

?>
<div class="token-asstes-view">

    <h1><?= Html::encode($this->title) ?></h1>



    <?= DetailView::widget([
        'model' => $model,
//        'attributes' => [
//            'id',
//            [
//                'attribute'=>'token_type_id',
//                'value'=>function($model){
//                    $tokenArray = ArrayHelper::map(\common\models\RcTokenType::find()->all(), 'id', 'name');
//                    return $tokenArray[$model->token_type_id];
//                },
//            ],
//            'currency_total',
//            'start_time:datetime',
//            'end_time:datetime',
//            'release_cycle',
//            [
//                'attribute' => 'type',
//                'value' => function($model){
//                    $typeArray = $model::typeArray();
//                    return $typeArray[$model->type];
//                },
//            ],
//
//            ]
        'attributes' => $attributes
    ]) ?>

</div>
