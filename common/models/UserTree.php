<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use backend\models\Setting;
use creocoder\nestedsets\NestedSetsBehavior;


/**
 * This is the model class for table "categories".
 *
 * @property integer $id
 * @property integer $root
 * @property integer $lft
 * @property integer $rgt
 * @property integer $lvl
 * @property string $name
 * @property string $icon
 * @property integer $icon_type
 * @property integer $active
 * @property integer $selected
 * @property integer $disabled
 * @property integer $readonly
 * @property integer $visible
 * @property integer $collapsed
 * @property integer $movable_u
 * @property integer $movable_d
 * @property integer $movable_l
 * @property integer $movable_r
 * @property integer $removable
 * @property integer $removable_all
 *
 * @property CategoryItems[] $categoryItems
 */
class UserTree extends ActiveRecord
{
    const NODE_ACTIVE = 10;
    const NODE_DELETED = 0;

    use \kartik\tree\models\TreeTrait {
        isDisabled as parentIsDisabled; // note the alias
    }

    /**
     * @var string the classname for the TreeQuery that implements the NestedSetQueryBehavior.
     * If not set this will default to `kartik	ree\models\TreeQuery`.
     */
    public static $treeQueryClass; // change if you need to set your own TreeQuery

    /**
     * @var bool whether to HTML encode the tree node names. Defaults to `true`.
     */
    public $encodeNodeNames = true;
 
    /**
     * @var bool whether to HTML purify the tree node icon content before saving.
     * Defaults to `true`.
     */
    public $purifyNodeIcons = true;
 
    /**
     * @var array activation errors for the node
     */
    public $nodeActivationErrors = [];
 
    /**
     * @var array node removal errors
     */
    public $nodeRemovalErrors = [];
 
    /**
     * @var bool attribute to cache the `active` state before a model update. Defaults to `true`.
     */
    public $activeOrig = true;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'iec_user_tree';
    }
    
    /**
     * Note overriding isDisabled method is slightly different when
     * using the trait. It uses the alias.
     */
    public function isDisabled()
    {
        // if (Yii::$app->user->username !== 'admin') {
        //     return true;
        // }
        return $this->parentIsDisabled();
    }

    public function behaviors()
    {
        return [
        	[
                'class' => NestedSetsBehavior::className(),
				'treeAttribute' => 'root',
				'depthAttribute' => 'lvl',
            ],
        ];
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['name', 'exist', 'targetAttribute' => 'username', 'targetClass' => '\api\models\User'];
        $rules[] = ['name', 'filter', 'filter' => function($value){
            $user = \api\models\User::findOne(['username' => $this->name]);
            $this->userid = $user->userid;
            $this->uid = $user->id;
            return $this->name;
        }];
        $rules[] = ['name', 'unique', 'targetClass' => '\common\models\UserTree'];
        return $rules;
    }
    
    /**
     * 事务
     *
     * @return void
     */
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * 查询
     *
     * @return void
     */
    public static function find()
    {
        return new MenuQuery(get_called_class());
    }

    /**
     * 添加关系网
     *
     * @return void
     */
    public static function addUserTree($upid, $name)
    {
        $topTree = self::findOne(['uid' => $upid]);
        $nameTree = new self(['name' => $name]);
        $nameTree->created_at = time();
        
        if(!$topTree){
            return $nameTree->makeRoot();
        }else{
            $nameTree->node_lvl = $topTree->node_lvl;
            return $nameTree->appendTo($topTree);
        }
    }


    /**
     * 获取购买超级节点的数量
     *
     * @return void
     */
    public static function supernodeBuyAmount()
    {
        $initAmount = Setting::read('supernode_init_amount');
        $lvlAmount = explode(',', Setting::read('supernode_lvl_amount'));

        $count = static::find()->where(['node' => 1])->count();
        $addAmount = intval($count / $lvlAmount[0]);
        $amount = $initAmount + $addAmount * $lvlAmount[1];
        return $amount;
    }


    /**
     * 更新 超级节点深度
     *
     * @return void
     */
    public function updateNodeDepth($insert = true)
    {
        $lvl = $insert ? 1 : -1;
        static::updateAllCounters(['node_lvl' => $lvl], [
            'AND',
            ['>', 'lft', $this->lft],
            ['<', 'rgt', $this->rgt],
            ['=', 'root', $this->root],
        ]);
    }

    /**
     * 关联用户
     *
     * @return void
     */
    public function getUser(){
        return $this->hasOne(\backend\models\User::className(), ['id' => 'uid'])->select("id,username,nickname,userid,created_at");
    }
}
