<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Shop */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Shops', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('plugins/layer/layer.js', ['depends' => \backend\assets\AppAsset::className()]);

?>
<div class="shop-view">

    <div class="box box-success">
        <div class="box-body">
            <p>
                <?= Html::a('更新', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('删除', ['delete', 'id' => $model->id], [
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
            'name',
            'contact',
            'phone',
            'type_id',
            [
                'attribute' => 'province_id',
                'value' => function($model){
                    return \common\models\Area::getAreaName($model->province_id);
                },
            ],
            [
                'attribute' => 'city_id',
                'value' => function($model){
                    return \common\models\Area::getAreaName($model->city_id);
                },
            ],
            [
                'attribute' => 'district_id',
                'value' => function($model){
                    return \common\models\Area::getAreaName($model->district_id);
                },
            ],
            'address',
            'introduction:ntext',
            [
                'attribute' => 'license',
                'value' => function($model){
                    return Html::img($model->license, ['height' => 100,'data-role' => 'image']);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'store_photos',
                'label' => '店铺照片',
                'format' => 'raw',
                'value' => function ($model) {
                    $store_photos = explode(',',$model->store_photos);
                    $html = '';
                    $html .= (!empty($store_photos[0]) ? '图1： ' . Html::img(
                            $store_photos[0], [
                            'width' => '120',
                            'height' => '50',
                            'class' => 'mb10',
                            'data-role' => 'image'
                        ]) . '&nbsp;' : '');
                    $html .= (!empty($store_photos[1]) ? '图2： ' . Html::img(
                            $store_photos[1], [
                            'width' => '120',
                            'height' => '50',
                            'class' => 'mb10',
                            'data-role' => 'image'
                        ]) . '&nbsp;' : '');
                    $html .= (!empty($store_photos[2]) ? '图3： ' . Html::img(
                            $store_photos[2] , [
                            'width' => '120',
                            'height' => '50',
                            'class' => 'mb10',
                            'data-role' => 'image'
                        ]) . '&nbsp;' : '');

                    return $html;
                },
                'filter' => false,
            ],


            [
                'attribute' => 'status',
                'value' => function($model){
                    return \common\models\Shop::$lib_status[$model->status];
                },
            ],
            'refuse_reason:ntext',
            'created_at:datetime',
            'updated_at:datetime',
            'weight',
//            'userid',
                ],
            ]) ?>
        </div>
    </div>
    

</div>
<script>
    window.onload = function () {
        $('[data-role="image"]').click(function () {
            var url = $(this).attr('src');

            layer.open({
                type: 1,
                area: ['600px', '400px'],
                // shade: false,
                title: false, //不显示标题
                content: '<div class="p20 text-center"><img src="' + url + '" width="500"/></div>',
            });
        })
    }
</script>