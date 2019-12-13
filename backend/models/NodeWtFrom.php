<?php
namespace backend\models;
use yii\base\Model;
use common\models\node\NodeWt;

class NodeWtFrom extends model{
	public $id;
	public $name;// 节点名称
	public $amount;// 节点价格
	public $symbol;// 币种
	public $node_image;// 图片
	public $number; //数量
	public $total_num;// 节点金额
	public $node_symbol;// 节点币种
	public $node_rules;// 节点规则
	public $created_at;// 创建时间

	public $isNewRecord = true;

	/**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount', 'symbol', 'number', 'total_num', 'node_rules'], 'required'],
            [['amount'], 'number'],
            [['number', 'total_num'], 'integer'],
            [['node_rules'], 'string'],
            [['created_at'], 'safe'],
            [['name', 'node_image'], 'string', 'max' => 255],
            [['symbol', 'node_symbol'], 'string', 'max' => 20],
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
            'amount' => '购买价格',
            'symbol' => '购买币种',
            'node_image' => '图片',
            'number' => '数量',
            'total_num' => '总金额',
            'node_symbol' => '奖励币种',
            'node_rules' => '规则',
            'created_at' => '创建时间',
        ];
    }

    /**
     * [nodevalue description]
     * @return [type] [description]
     */
    public static function updatevalue($id){
    	$data = new static();
    	$model = NodeWt::get($id);
    	$data->id = $model->id;
    	$data->name = $model->name;
    	$data->symbol = $model->symbol;
    	$data->node_image = $model->node_image;
    	$data->number = $model->number;
    	$data->total_num = $model->total_num;
    	$data->node_symbol = $model->node_symbol;
    	$data->node_rules = $model->node_rules;
    	$datas->isNewRecord = false;
    	return $data;
    }
}