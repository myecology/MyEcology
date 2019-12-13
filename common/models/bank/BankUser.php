<?php

namespace common\models\bank;

use api\models\User;
use backend\models\Setting;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "iec_bank_user".
 *
 * @property int $id
 * @property int $user_id
 * @property string $amount
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 * @property string $symbol 币种
 */
class BankUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_bank_user';
    }

    private  $son = [];
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'amount', 'created_at', 'updated_at', 'symbol'], 'required'],
            [['user_id', 'created_at', 'updated_at'], 'integer'],
            [['amount'], 'number'],
            [['symbol'], 'string', 'max' => 20],
        ];
    }
    /**
     * 模型行为
     * @return [type] [description]
     */
    public function behaviors()
    {

        return [
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'amount',
                ],
                'value' => function ($event) {
                    $fee = explode(',', Setting::read('supernode_top_fee'));
                    $uids = [];
                    $uid = Yii::$app->user->identity->id;
                    foreach($fee as $key=>$val){
                        $supernodeTree = User::findOne(['id' => $uid]);
                        if($supernodeTree){
                            $supernode = $supernodeTree->upid;
                        }else{
                            $supernode = null;
                        }
                        $uid = $supernode ? $supernode : 0;
                        $uids[] = $uid;
                    }
                    return implode(',', $uids);
                },
            ],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'amount' => 'Amount',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'symbol' => 'Symbol',
        ];
    }

    // 根据user_id查询积分
    public static function amountByUserId($user_id){
        return BankUser::findOne(['user_id' => $user_id]);
    }

    /**
     * 获取子孙树
     * @param   array        $data   待分类的数据
     * @param   int/string   $id     要找的子节点id
     * @param   int          $lev    节点等级
     */
    public function getSubTree($data , $id = 0 , $lev = 0) {

        foreach($data as $key => $value) {
            if($value['pid'] == $id) {
                $this->son[] = [
                    'id' => $value->id,
                    'lev' => $lev,
                    'amount' => ''
                ];
                $this->getSubTree($data , $value['id'] , $lev+1);
            }
        }

        return $this->son;
    }

}
