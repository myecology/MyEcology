<?php
namespace backend\modules\activation\models;

use common\models\activation\Activation;
use common\models\activation\ActivationParam;

class ActivationParamForm extends \yii\base\Model
{
    public $reward_symbol; // 奖励币种
    public $level_proportion; //奖励层级比列
    public $reward_level; //奖励层级
    public $expiration_time; //过期时间
    public $mode; //奖励方式
    public $royalty; //提成比列
    public $royalty_type;

    const ROYALTY_TYPE_USER = 'user';
    const ROYALTY_TYPE_SHOP = 'shop';
    const MODE_LEVEL = 'level';
    const MODE_ROYALTY = 'royalty';
    public static $modeArr = [
        ActivationParamForm::MODE_LEVEL => '等级奖励',
        ActivationParamForm::MODE_ROYALTY => '提成奖励',
    ];
    public static $royaltyTypeArr = [
        ActivationParamForm::ROYALTY_TYPE_USER => '提成给用户上级',
        ActivationParamForm::ROYALTY_TYPE_SHOP => '提成给商户上级',
    ];
    public function scenarios(){
        return[
            ActivationParamForm::MODE_LEVEL => ['reward_symbol','level_proportion','reward_level','expiration_time','mode'],
            ActivationParamForm::MODE_ROYALTY => ['reward_symbol','royalty','reward_level','royalty_type','mode'],
        ];
    }
    public function rules(){
        return [
            [['reward_symbol','reward_level','mode'],'required','on'=>[ActivationParamForm::MODE_LEVEL,ActivationParamForm::MODE_ROYALTY]],
            [['level_proportion'],'required','on' => ActivationParamForm::MODE_LEVEL],
            [['expiration_time'],'safe','on' => ActivationParamForm::MODE_LEVEL],
            [['expiration_time'],'number','on' => ActivationParamForm::MODE_LEVEL],
            [['royalty','royalty_type'],'required','on' => ActivationParamForm::MODE_ROYALTY],
            [['royalty'],'number','on' => ActivationParamForm::MODE_ROYALTY]
        ];
    }
    public function attributeLabels()
    {
        return [
            'reward_symbol' => '奖励币种',
            'level_proportion' => '奖励层级比列',
            'reward_level' => '奖励层级',
            'expiration_time' => '过期时间(天)',
            'mode' => '奖励方式',
            'royalty' => '提成比例',
            'royalty_type' => '奖励上级'
        ];
    }
    public static function paramData($id){
        $params = \common\models\activation\ActivationParam::getParam($id);
        $model = new static();
        $paramsKey = $params['key'];
        if(!empty($paramsKey)){
            $model->mode = $paramsKey['mode'];
            $model->reward_symbol = empty($paramsKey['reward_symbol']) ? null : $paramsKey['reward_symbol'];
            $model->level_proportion = empty($paramsKey['level_proportion']) ? null : $paramsKey['level_proportion'];
            $model->royalty = empty($paramsKey['royalty']) ? null  : $paramsKey['royalty'];
            $model->expiration_time = empty($paramsKey['expiration_time']) ? null : $paramsKey['expiration_time'];
            $model->royalty_type = empty($paramsKey['royalty_type'])? null : $paramsKey['royalty_type'];
        }
        $paramsGroup = $params['group'];
        if(!empty($paramsGroup)){
            $model->reward_level = $paramsGroup['reward_level'];
        }
        return $model;
    }
    public function save($id){
        if(!$this->validate()){
            return false;
        }
        $transaction = \Yii::$app->db->beginTransaction();
        try{
            $activationModel = Activation::findOne($id);
            if(empty($activationModel)){
                throw new \ErrorException('该活动不存在');
            }
            if($this->mode == static::MODE_LEVEL){
                $keys = ['reward_symbol','level_proportion','expiration_time','mode'];
                foreach ($keys as $key){
                    static::addParam($id,$key,$this->$key,$this->attributeLabels()[$key]);
                }
            }elseif($this->mode == static::MODE_ROYALTY){
                $keys = ['reward_symbol','royalty','royalty_type','mode'];
                foreach ($keys as $key){
                    static::addParam($id,$key,$this->$key,$this->attributeLabels()[$key]);
                }
            }
            if(empty($this->reward_level)){
                throw new \ErrorException('层级奖励不能为空');
            }else{
                static::updateLevel($id,'reward_level',$this->reward_level,$this->attributeLabels()['reward_level']);
            }
            $transaction->commit();
            return true;
        }catch (\Exception $exception){
            $transaction->rollBack();
            throw new \ErrorException($exception->getMessage());
        }
    }

    public static function addParam($id,$key,$value,$remark){
        $model = ActivationParam::findOne([
            'activation_id' => $id,
            'key' => $key,
            'type' => ActivationParam::TYPE_KEY
        ]);
        if(empty($model)){
           $paramModel =  new ActivationParam();
           $paramModel->key = $key;
           $paramModel->value = $value;
           $paramModel->type = ActivationParam::TYPE_KEY;
           $paramModel->activation_id = $id;
           $paramModel->remark = $remark;
           if($paramModel->save()){
               return true;
           }else{
               throw new \ErrorException($model->getFirstError());
           }
        }else{
            $model->value = $value;
            $model->remark = $remark;
            if($model->save()){
                return true;
            }else{
                throw new \ErrorException($model->getFirstError());
            }
        }
    }

    public static function updateLevel($id,$group,$arr,$remark){
        ActivationParam::deleteAll([
            'activation_id' => $id,
            'group' => $group,
            'type' => ActivationParam::TYPE_GROUP
        ]);
        $keyArr = [];
        foreach ($arr as $data){
            if(!is_numeric($data['value'])){
                throw new \ErrorException('数值只允许是数字类型');
            }
            $key = intval($data['key']);
            if($key <= 0){
                throw new \ErrorException('等级只允许是正整数类型');
            }
            if(isset($keyArr[$data['key']])){
                throw new \ErrorException('等级设置有重复的值');
            }else{
                $keyArr[$data['key']] = 1;
            }
            $model = new ActivationParam();
            $model->activation_id = $id;
            $model->group = $group;
            $model->type = ActivationParam::TYPE_GROUP;
            $model->remark = $remark;
            $model->key = trim($data['key']);
            $model->value = trim($data['value']);
            if(!$model->save()){
                throw new \ErrorException($model->getFirstError());
            }
        }
        return true;
    }

}