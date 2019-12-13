<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\assets\models\Invitation */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Invitations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invitation-view">

    <div class="box box-success">
        <div class="box-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    // 'registerer_id',
                    // 'inviter_id',
                    [
                        'label' => '注册号码',
                        'value' => function($model){
                            return $model->registerUser->username;
                        }
                    ],
                    [
                        'label' => '邀请号码',
                        'value' => function($model){
                            return $model->inviterUser->username;
                        }
                    ],
                    'created_at:datetime',
                    'level',
                ],
            ]) ?>

            <br><br>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>时间</th>
                        <th>层级</th>
                        <th>币种</th>
                        <th>数量</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($model->inviteReward as $reward){ ?>
                    <tr>
                        <td><?= date('Y-m-d H:i:s', $reward->created_at) ?></td>
                        <td><?= $reward->level ?></td>
                        <td><?= $reward->symbol ?></td>
                        <td><?= $reward->amount ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    

</div>
