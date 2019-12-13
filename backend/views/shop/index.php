<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;


/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\ShopSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '商家列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-index table-responsive">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="box box-success">
        <div class="box-body">

            <p>
                <?= Html::a('添加', ['create'], ['class' => 'btn btn-success']) ?>
            </p>
                    <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
        'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

//                    'id',
            'name',
            'contact',
            'phone',
            [
                'attribute' => 'type_id',
                'value' => function($model){
                    return $model->shopType->title;
                },
//                'filter' => $searchModel::statusArray()
            ],
            //'province_id',
            //'city_id',
            //'district_id',
            //'address',
            //'introduction:ntext',
            //'license',
            //'store_photos:ntext',
            [
                'attribute' => 'status',
                'value' => function($model){
                    $statusArray = \common\models\Shop::$lib_status;
                    return $statusArray[$model->status];
                },
                'filter' => \common\models\Shop::$lib_status
            ],
            //'refuse_reason:ntext',
            'created_at:datetime',
            //'updated_at',
            //'weight',
            //'userid',

//                    ['class' => 'yii\grid\ActionColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}  {success}  {refuse}',
                'buttons' => [
                    'view' => function($url, $model, $key){
                        return Html::a('查看', $url, [
                            'class' => 'btn btn-info btn-xs',
                        ]);
                    },
                    'success' => function($url, $model, $key){
                        if($model->status == \common\models\Shop::STATUS_SUBMITTED) {
                            return Html::a('通过', $url, [
                                'class' => 'btn btn-success btn-xs',
                                'data' => [
                                    'confirm' => '是否通过审核',
                                ]
                            ]);
                        }
                    },
                    'refuse' => function($url, $model, $key) {
                        if($model->status == \common\models\Shop::STATUS_SUBMITTED) {
                            return Html::a('拒绝', $url, [
                                'class' => 'btn btn-warning btn-xs modal-wallet',
                                'data' => [
                                    'confirm' => '是否拒绝审核',
                                ]
                            ]);
                        }
                    }
                ],
            ],
                ],
            ]); ?>
                </div>
    </div>

</div>

<?php
Modal::begin([
    'id' => 'wallet',
    'header' => '<h4 class="modal-title">拒绝原因</h4>',
    'footer' => '<a href="javascript:;" id="submit" class="btn btn-success">提交</a><a href="javascript:;" class="btn btn-primary" data-dismiss="modal">关闭</a>'
]);

echo Html::textarea( 'refuse_reason', '', ['class' => 'form-control', 'id' => 'refuse_reason']);

$js = <<<JS

    var url = '';
    var id = '';
    $('.modal-wallet').click(function(ev){
        id = $(this).attr('data-id');
        $('#wallet').modal('show');
        if($(this).attr('data-type') == 'resufe'){
            url = '/shop/refuse';
        }
    });
    
    $('#submit').click(function(ev){
        
        var refuse_reason = $('#refuse_reason').val();
        if(!refuse_reason){
            alert('请填写拒绝理由');
            return false;
        }

        $.post(url, {'id' : id , 'refuse_reason' : refuse_reason}, function(data){
            $('#wallet').modal('hide');
            if(data.status > 0){
                alert('操作成功');
            }else{
                alert('操作失败');
            }
        }, 'json');
    })

JS;
$this->registerJs($js);
Modal::end(); ?>
