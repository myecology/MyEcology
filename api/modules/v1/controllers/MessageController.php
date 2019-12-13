<?php
namespace api\modules\v1\controllers;

use api\controllers\APIFormat;
use api\models\User;
use common\models\Message;
use common\models\Official;
use common\models\Transfer;
use Yii;
use yii\data\Pagination;

/**
 * 消息
 */
class MessageController extends BaseController {
	/**
	 * 通知消息列表
	 *
	 * @return void
	 */
	public function actionIndex() {
		$data = Message::find()->select(["*", "format(amount,2) as _amount"])->where(['user_id' => Yii::$app->user->identity->id]);

		$pagination = new Pagination([
			'defaultPageSize' => 10,
			'pageSizeLimit' => [1, 100],
			'totalCount' => $data->count(),
		]);
		$pagination->validatePage = false;

		$data = $data->offset($pagination->offset)
			->limit($pagination->limit)
			->orderBy(['created_at' => SORT_DESC])
			->asArray()
			->all();

		foreach ($data as $key => $val) {
		    if ($val['type'] == 12) {
		        $transfer = Transfer::findById($val['source_id']);
		        $user = User::findByUid($transfer->sender_id);
                $data[$key]['description'] = $user->nickname;
            }
        }

		$_meta = [
			'totalCount' => $pagination->totalCount,
			'pageCount' => $pagination->pageCount,
			'currentPage' => $pagination->getPage() + 1,
			'perPage' => $pagination->getPageSize(),
		];

		return APIFormat::success(['items' => $data, '_meta' => $_meta]);
	}

    /***
     * 官方消息通知
     */
    public function actionOfficial() {
        $data = Official::find()->where(['display' => 1]);

        $pagination = new Pagination([
            'defaultPageSize' => 10,
            'pageSizeLimit' => [1, 100],
            'totalCount' => $data->count(),
        ]);
        $pagination->validatePage = false;

        $data = $data->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy(['created_at' => SORT_DESC])
            ->asArray()
            ->all();

        foreach ($data as $key => $val) {
           $data[$key]['created_at'] = date('Y-m-d H:i:s',$val['created_at']);
        }

        $_meta = [
            'totalCount' => $pagination->totalCount,
            'pageCount' => $pagination->pageCount,
            'currentPage' => $pagination->getPage() + 1,
            'perPage' => $pagination->getPageSize(),
        ];

        return APIFormat::success(['items' => $data, '_meta' => $_meta]);
    }

    public function actionOfficialDetail($id){
        $model = Official::findOne($id);
        $model->created_at = date("Y-m-d H:i:s",$model->created_at);
        return APIFormat::success(['items' => $model]);
    }
	/**
	 *
	 *
	 * @param [type] $id
	 * @return void
	 */
	public function actionDetail($id) {
		$model = Message::findOne($id);

		$data = [];
		if ($model) {
			switch ($model->type) {
			//  付款
			case Message::TYPE_MONEY_YOU_PAYMENT:
				$data = [];
				break;
			//  收款
			case Message::TYPE_MONEY_YOU_RECEIPT:
				$data = [];
				break;
			//  提现
			case Message::TYPE_TRANSACTION_WITHDRAW:
				$sourceModel = \common\models\Withdraw::findOne($model->source_id);
				$addressModel = \api\models\WalletAddress::findOne(['user_id'=>$sourceModel->user_id,'symbol'=>$sourceModel->symbol]);
				$data = [
					'title' => '提现详情',
					'tip' => '',
					'symbol' => $model->symbol,
					'amount' => sprintf("%.2f", $model->amount),
					'from' => empty($addressModel) ? '未生成地址' :$addressModel->address,
					'to' => $sourceModel->address,
					'fee' => $sourceModel->fee,
					'fee_symbol' => $sourceModel->fee_symbol,
					'remark' => $sourceModel->remark,
					'datetime' => $sourceModel->updated_at,
				];
				break;
			//  充值
			case Message::TYPE_TRANSACTION_DEPOSIT:
				$sourceModel = \common\models\Deposit::findOne($model->source_id);
				$addressModel = \api\models\WalletAddress::findOne($sourceModel->address_id);
				$data = [
					'title' => '充值详情',
					'tip' => '',
					'symbol' => $model->symbol,
					'amount' => sprintf("%.2f", $model->amount),
					'from' => $sourceModel->address,
					'to' => $addressModel->address,
					'fee' => $sourceModel->fee,
					'fee_symbol' => $sourceModel->fee_symbol,
					'remark' => $sourceModel->source,
					'datetime' => $sourceModel->updated_at,
				];
				break;
			//  购买理财
			case Message::TYPE_FINANCIAL_BUY:
				$sourceModel = \common\models\bank\Order::findOne($model->source_id);
				$data = [
					'title' => '令牌理财详情',
					'tip' => '付款数量',
					'symbol' => $model->symbol,
					'amount' => sprintf("%.2f", $model->amount),
					'name' => $sourceModel->product->name,
					'status' => '支付成功',
					'datetime' => $sourceModel->created_at,
				];
				break;
			//  收益本金
			case Message::TYPE_FINANCIAL_PRINCIPAL:
				$sourceModel = \common\models\bank\Profit::findOne($model->source_id);
				$order = \common\models\bank\Order::findOne($sourceModel->order_id);
				if ($sourceModel->amount == $order->amount) {
					$min = 0;
					$max = sprintf("%.2f", $order->amount);
				} else {
					$profitAmount = \common\models\bank\Profit::find()->select('amount')->where(['type' => \common\models\bank\Profit::TYPE_PRINCIPAL, 'order_id' => $sourceModel->order_id])->sum('amount');
					$min = $order->amount - $profitAmount;
					$max = sprintf("%.2f", $order->amount);
				}
				$data = [
					'title' => '令牌理财详情',
					'tip' => '到账数量',
					'symbol' => $model->symbol,
					'amount' => sprintf("%.2f", $model->amount),
					'name' => $sourceModel->product->name,
					'status' => '已放入区块钱包',
					'datetime' => $sourceModel->created_at,
					'detail' => $min . '/' . $max . ' ' . $model->symbol,
				];
				break;
			//  收益利息
			case Message::TYPE_FINANCIAL_PROFIT:
				$sourceModel = \common\models\bank\Profit::findOne($model->source_id);
				$order = \common\models\bank\Order::findOne($sourceModel->order_id);
				$profitAmount = \common\models\bank\Profit::find()->select('amount')->where(['type' => \common\models\bank\Profit::TYPE_PROFIT, 'order_id' => $sourceModel->order_id])->sum('amount');
//				$fee = $order->rate * $order->amount / 100;
                $income = $order->product->income;
                $fee = (($order->amount * $order->rate * $order->currency_price / 100) / $order->earn_currency_price / $income->day * $order->day);
                if ($profitAmount == $fee) {
					$min = 0;
					$max = $fee;
				} else {
					$min = $fee - $profitAmount;
					$max = $fee;
				}
				$data = [
					'title' => '令牌理财详情',
					'tip' => '到账数量',
					'symbol' => $model->symbol,
					'amount' => sprintf("%.2f", $model->amount),
					'name' => $sourceModel->product->name,
					'status' => '已放入区块钱包',
					'datetime' => $sourceModel->created_at,
					'detail' => $min . '/' . $max . ' ' . $model->symbol,
				];
				break;

			//  购买超级节点
			case Message::TYPE_SUPERNODE_BUY:
				$sourceModel = \common\models\Supernode::findOne($model->source_id);
				$data = [
					'title' => '超级节点详情',
					'tip' => '付款数量',
					'symbol' => $model->symbol,
					'amount' => sprintf("%.2f", $model->amount),
					'name' => '超级节点竞选',
					'status' => '支付成功',
					'datetime' => $sourceModel->created_at,
				];
				break;
			//  退出超级节点
			case Message::TYPE_SUPERNODE_EXIT:
				$sourceModel = \common\models\Supernode::findOne($model->source_id);
				$data = [
					'title' => '超级节点详情',
					'tip' => '退款数量',
					'symbol' => $model->symbol,
					'amount' => sprintf("%.2f", $model->amount),
					'name' => '退出超级节点',
					'status' => '已放入区块钱包',
					'datetime' => $sourceModel->created_at,
				];
				break;
			//  超级节点收益
			case Message::TYPE_SUPERNODE_PROFIT:
				$sourceModel = \common\models\SupernodeProfit::findOne($model->source_id);
				$data = [
					'title' => '佣金收益详情',
					'tip' => '到账数量',
					'symbol' => $model->symbol,
					'amount' => sprintf("%.2f", $model->amount),
					'name' => $model->description,
					'status' => '已放入区块钱包',
					'datetime' => $sourceModel->created_at,
				];
				break;
			//  红包退回
			case Message::TYPE_HONGBAO_BACK:
				$sourceModel = \common\models\GiftMoney::findOne($model->source_id);
				$data = [
					'title' => '红包退款详情',
					'tip' => '退款数量',
					'symbol' => $model->symbol,
					'amount' => sprintf("%.2f", $model->amount),
					'type' => $sourceModel::$lib_type[$sourceModel->type],
					'name' => '超过24小时未被领取',
					'status' => '已放入区块钱包',
					'datetime' => $sourceModel->expired_at,
				];
				break;
				break;
			//  注册奖励
			case Message::TYPE_SIGNUP_REWARD:
				$sourceModel = \common\models\InviteReward::findOne($model->source_id);
				$m = ['M', 'M1', 'M2', 'M3'];
				$user = \api\models\User::findOne($sourceModel->registerer_id);
				$data = [
					'title' => '邀请详情',
					'tip' => '奖励数量',
					'symbol' => $model->symbol,
					'amount' => sprintf("%.2f", $model->amount),
					'name' => $user->nickname,
					'status' => '已放入区块钱包',
					'level' => $m[$sourceModel->level],
					'datetime' => $sourceModel->created_at,
				];
				break;
				break;
            //  通证分红
            case Message::TYPE_SHARE_BONUS:
                $data = [
                    'title' => '通证分红详情',
                    'tip' => '分红数量',
                    'symbol' => $model->symbol,
                    'amount' => sprintf("%.2f", $model->amount),
                    'name' => '通证分红',
                    'status' => '分红成功',
                    'datetime' => $model->created_at,
                ];
                break;
                //回滚
            case Message::TYPE_ROLL_WITHDRAW:
                $data = [
                    'title' => $model->title,
                    'tip' => '退回金额',
                    'symbol' => $model->symbol,
                    'amount' => sprintf("%.2f", $model->amount),
                    'name' => '提现失败回退',
                    'status' => '回退成功',
                    'datetime' => $model->created_at,
                ];
                break;
            case Message::TYPE_RELEASE_CROW:
            	$data = [
                    'title' => $model->title,
                    'tip' => '众筹释放',
                    'symbol' => $model->symbol,
                    'amount' => sprintf("%.2f", $model->amount),
                    'name' => $model->description,
                    'status' => '释放成功',
                    'datetime' => $model->created_at,
                ];
                break;
			default:
				exit;
				break;
			}
		}

		return APIFormat::success($data);
	}

}