<?php

namespace common\models;

use Yii;
use creocoder\nestedsets\NestedSetsQueryBehavior;

class MenuQuery extends \yii\db\ActiveQuery
{
    //  行为
    public function behaviors() {
        return [
            NestedSetsQueryBehavior::className(),
        ];
    }

}
