<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\User */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '用户列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <div class="box box-success">
        <div class="box-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'upid',
                    'area',
                    'initials',
                    'username',
                    'nickname',
                    'iecid',
                    'userid',
                    'headimgurl',
                    'access_token',
                    // 'auth_key',
                    // 'password_hash',
                    // 'password_reset_token',
                    'email:email',
                    'longitude',
                    'latitude',
                    'sex',
                    'age',
                    'country',
                    'province',
                    'city',
                    'status',
                    'code',
                    'description',
                    'created_at',
                    'updated_at',
                    'friend',
                    // 'payment_hash',
                    'is_iec',
                ],
            ]) ?>
        </div>
    </div>
    

</div>
