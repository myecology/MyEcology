<?php

namespace api\models;
use common\models\Verification;
use yii\base\Model;
use Yii;

/**
 * VerificationAdd form
 */
class VerificationAddForm extends Model
{
    public $name;
    public $identity_number;
    public $image_main;
    public $image_1;
    public $image_2;

    private $_user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'identity_number', 'image_main', 'image_1', 'image_2'],'trim'],
            [['name', 'identity_number','image_main', 'image_1', 'image_2'], 'required'],
            [['name', 'identity_number', 'image_main', 'image_1', 'image_2'], 'string', 'min' => 2,],
            ['identity_number' , function ($attribute,$param){
                if (YII_DEBUG) {
                    return true;
                }
                if (!$this->hasErrors()) {
                    if (!VerificationAddForm::checkIsIDCard($this->identity_number)) {
                        throw new \ErrorException('身份证格式错误', 3008);
                    }
                    if (Verification::existsValidByIdNumber($this->identity_number)) {
                        throw new \ErrorException('身份证号码已提交审核', 3009);
                    }
                }
            }],
            ['name', function ($attribute, $param) {
                if (!$this->hasErrors()) {
                    if (Verification::existsValidByUserId($this->getUser()->id)) {
                        throw new \ErrorException('您提交的认证信息正在审核中', 7003);
                    }
                }
            }],
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
            'status' => '状态',
            'name' => '姓名',
            'identity_number' => '身份证号码',
            'reviewed_at' => 'Reviewed At',
            'image_main' => '手持身份证',
            'image_1' => '身份证正面',
            'image_2' => '身份证反面',
            'verification_sn' => 'Verification SN',
            'created_at' => 'Created At',
        ];
    }


    /**
     * @return bool|Verification
     * @throws \ErrorException
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $model_verification = Verification::createFromApi(
            $this->getUser()->id
            , $this->name
            , strtoupper($this->identity_number)
            , $this->image_main
            , $this->image_1
            , $this->image_2
        );
        if (false === $model_verification) {
            throw new \ErrorException('', 8903);
        }
        return $model_verification;
    }


    /**
     * @return bool|Verification
     * @throws \ErrorException
     */
    public function update()
    {
        if (!$this->validate()) {
            return false;
        }

        $model_verification = Verification::UpdateFromApi(
            $this->getUser()->id
            , $this->name
            , strtoupper($this->identity_number)
            , $this->image_main
            , $this->image_1
            , $this->image_2
        );
        if (false === $model_verification) {
            throw new \ErrorException('', 8903);
        }
        return $model_verification;
    }



    /**
     * 判断身份证号码
     * @param $id_card
     * @return bool
     */
    public function checkIsIDCard($id_card)
    {
        if (mb_strlen($id_card) != 18) return false;
        $id_card = strtoupper($id_card);
        //校验位列表
        $remainder_list = [1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2];
        //加权除以11的余数
        $square_remainder = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
        //取得身份证号码最后一位校验位
        $check_num = mb_substr($id_card, 17);
        //身份证参与校验的前17位
        $id_card = mb_substr($id_card, 0, 17);
        //参与校验的必须是数字
        if (!is_numeric($id_card)) return false;
        $square_sum = 0;//每一位参与校验数乘以加权数的累加和
        $number_sum = 0;//每一位参与校验数乘以加权除以11的余数的累加和
        for ($i = 0; $i < 17; $i++) {
            //累加结果
            $square_sum += intval($id_card[$i]) * pow(2, 17 - $i);
            //累加结果
            $number_sum += intval($id_card[$i]) * $square_remainder[$i];
        }
        //从校验位列表中获取加权乘积和得到的校验位
        $check_get_square_remainder = isset($remainder_list[$square_sum % 11]) ? $remainder_list[$square_sum % 11] : -1;
        //从校验位列表中获取加权余数乘积和得到的校验位
        $check_get_number_remainder = isset($remainder_list[$number_sum % 11]) ? $remainder_list[$number_sum % 11] : -1;
        if ($check_get_square_remainder == $check_num && $check_get_number_remainder == $check_num) return true;
        return false;
    }


    /**
     *
     * @return User|null
     * @throws \ErrorException
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = Yii::$app->user->identity;
            if (!$this->_user) {
                throw new \ErrorException('', 7001);
            }
        }
        return $this->_user;
    }

}
