<?php

namespace common\models\node;

use Yii;
use yii\base\ErrorException;
use api\controllers\APIFormat;

/**
 * This is the model class for table "rc_node_wt1918".
 *
 * @property int $id
 * @property string $name 节点名称
 * @property string $income 图片
 * @property string $alte_price 备选价格
 * @property string $alte_symbol 备选币种
 * @property int $alte_number 备选数量
 * @property string $alte_rules 备选规则
 * @property int $total_awards 奖励总数
 * @property string $reward_symbol 奖励币种
 * @property int $super_number 超级节点数
 * @property int $super_factor 超级节点条件
 * @property string $super_rules 超级节点规则
 * @property string $super_price 超级节点价格
 * @property string $super_explain 超级节点s收益说明
 * @property string $super_symbol 超级节点币种
 * @property string $created_at 创建时间
 * @property string $update_at 修改时间
 */
class NodeWt extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_node_wt1918';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'income', 'alte_price', 'alte_symbol', 'alte_number', 'total_awards', 'reward_symbol', 'super_symbol', 'super_number', 'super_factor', 'super_rules', 'super_price'], 'required'],
            [['alte_price', 'super_price'], 'number'],
            [['alte_number', 'total_awards', 'super_number', 'super_factor'], 'integer'],
            [['alte_rules', 'super_rules', 'super_explain'], 'string'],
            [['created_at', 'update_at'], 'safe'],
            [['name', 'income'], 'string', 'max' => 255],
            [['alte_symbol', 'reward_symbol', 'super_symbol'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'income' => '图片',
            'alte_price' => '备选价格',
            'alte_symbol' => '备选币种',
            'alte_number' => '备选数量',
            'alte_rules' => '备选规则',
            'total_awards' => '奖励总数',
            'reward_symbol' => '奖励币种',
            'super_number' => '超级节点数',
            'super_factor' => '超级节点条件',
            'super_rules' => '超级节点规则',
            'super_price' => '超级节点价格',
            'super_explain' => '超级节点收益说明',
            'created_at' => '创建时间',
            'update_at' => '修改时间',
            'super_symbol' => '超级节点币种',
        ];
    }
}
