<?php
namespace common\models;

use Yii;
use api\models\User;
use yii\base\Model;
use common\models\Wallet;
use common\models\UserTree;

/**
 * Login form
 */
class SupernodeForm extends Model
{
    public $password;
    private $_user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePayment($this->password)) {
                $this->addError($attribute, '支付密码不正确');
            }
        }
    }


    public function buySupernode()
    {
        if(!$this->validate()){
            return false;
        }

        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $model = UserTree::find()->where(['uid' => Yii::$app->user->identity->id])->one();
            //  是否有这个关系网
            if(!$model){
                $this->addError('password', '账号异常');
                return false;
            }

            //  是否超级节点
            if($model['node'] == UserTree::NODE_ACTIVE){
                $this->addError('password', '您已经是超级节点');
                return false;
            }

            //  获取超级结点购买数量
            $amount = UserTree::supernodeBuyAmount();
            //  检查账户余额
            $wallet = Wallet::find()->where(['user_id' => Yii::$app->user->identity->id, 'symbol' => 'IEC'])->one();
            if(!$wallet || $amount > ($wallet['amount'] - $wallet['amount_lock'])){
                $this->addError('password', 'IEC数量不足');
                return false;
            }

            //  上级超级节点深度
            $parentNode = UserTree::findOne(['uid' => Yii::$app->user->identity->id])->parents()->andWhere(['node' => UserTree::NODE_ACTIVE])->one();
            $nodeLvl = $parentNode ? $model['node_lvl'] + 1 : 1;
            //  改变状态
            $model->node = UserTree::NODE_ACTIVE;
            $model->node_lvl = $nodeLvl;
            $model->created_at = time();
            if(false === $model->save()){
                throw new \yii\base\ErrorException('超级节点设置异常');
            }

            //  记录购买超级节点
            $supernodeModel = new Supernode();
            $supernodeModel->amount = $amount;
            if(false === $supernodeModel->save()){
                throw new \yii\base\ErrorException('超级节点数据异常');
            }

            //  更新超级节点深度
            $model->updateNodeDepth();

            if(YII_DEBUG){
                $root = UserTree::findOne(['id' => $model->root]);
                $childrens = $root->children()->all();
                $data = [];
                foreach($childrens as $val){
                    $data[] = [
                        'root' => $val->root,
                        'node' => $val->node,
                        'node_lvl' => $val->node_lvl,
                        'name' => $val->name,
                        'created_at' => $val->created_at,
                        'lft' => $val->lft,
                        'rgt' => $val->rgt,
                    ];
                }

                //  记录日志
                $message = Yii::$app->user->identity->username . '加入超级节点' . ',当前状态为 ' . \yii\helpers\Json::encode($data);
                Yii::info($message, 'tree');
            }

            //  发送通知 - 购买超级节点
            $user = \api\models\User::findOne($supernodeModel->uid);
            \common\models\Message::addMessage(\common\models\Message::TYPE_SUPERNODE_BUY, $user, 'IEC', $supernodeModel->amount, $supernodeModel);

            $transaction->commit();
            return true;
        } catch (\Throwable $th) {
            $transaction->rollBack();
            throw new \yii\base\ErrorException($th->getMessage());
        }

        return false;
    }


    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername(Yii::$app->user->identity->username);
        }

        return $this->_user;
    }
}