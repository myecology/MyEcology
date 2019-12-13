<?php

namespace common\models;

use Yii;
use api\controllers\APIFormat;
use yii\db\Exception;

/**
 * This is the model class for table "iec_verification".
 *
 * @property int $id
 * @property int $user_id 用户ID
 * @property int $status 审核状态
 * @property int $created_at 创建时间
 * @property string $name 名称
 * @property string $identity_number 号码
 * @property int $reviewed_at 审核时间
 * @property string $image_main 主图片
 * @property string $image_1 图片1
 * @property string $image_2 图片2
 * @property string $verification_sn 认证编号
 * @property string $bank_number 银行卡号
 * @property string $bank_address 开户行
 */
class Verification extends \yii\db\ActiveRecord
{
    public static $lib_type = [
        10 => '身份证',
    ];

    const TYPE_IDENTITY = 10;
    const TYPE_PASSPORT = 20;

    public static $lib_status = [
        0 => '未提交', //标记用户
        10 => '审核中',
        20 => '审核通过',
        30 => '已拒绝',
    ];
    const STATUS_UNSUBMITTED = 0;
    const STATUS_SUBMITTED = 10;
    const STATUS_DONE = 20;
    const STATUS_REJECTED = 30;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_verification';
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'created_at', 'reviewed_at', 'user_id'], 'integer'],
            [['name', 'identity_number', 'image_main', 'image_1', 'image_2'], 'string', 'max' => 512],
            [['verification_sn'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户名',
            'status' => '审核状态',
            'created_at' => '提交时间',
            'name' => '姓名',
            'identity_number' => '身份证',
            'reviewed_at' => '审核时间',
            'image_main' => '手持身份证',
            'image_1' => '身份证正面',
            'image_2' => '身份证反面',
            'verification_sn' => '编号',
        ];
    }

    /**
     * @param $user_id
     * @param $name
     * @param $identity_number
     * @param $image_main
     * @param $image_1
     * @param $image_2
     * @return bool|Verification
     */
    public static function createFromApi(
        $user_id
        , $name
        , $identity_number
        , $image_main
        , $image_1
        , $image_2
    )
    {
        $verification_model = Verification::find()->where(['user_id' => $user_id])->one();
        $model = $verification_model ? $verification_model : new Verification();
        $model->setAttributes([
            'user_id' => $user_id,
            'verification_sn' => static::generateSN(),
            'status' => static::STATUS_SUBMITTED,
            'created_at' => time(),
            'name' => $name,
            'identity_number' => $identity_number,
            'image_main' => $image_main,
            'image_1' => $image_1,
            'image_2' => $image_2,
        ]);

        return $model->save() ? $model : false;
    }

    /**
     * 更新
     * @param $user_id
     * @param $name
     * @param $identity_number
     * @param $image_main
     * @param $image_1
     * @param $image_2
     * @return array|bool|null|\yii\db\ActiveRecord
     * @throws \ErrorException
     */
    public static function UpdateFromApi(
        $user_id
        , $name
        , $identity_number
        , $image_main
        , $image_1
        , $image_2
    )
    {
        $verification = Verification::find()->where(['user_id' => $user_id, 'status'=> Verification::STATUS_REJECTED])->one();
        if (!$verification) {
            throw new \ErrorException('不符合更新状态',7005);

        }

        $verification->setAttributes([
            'id' => $verification->id,
            'user_id' => $user_id,
            'status' => static::STATUS_SUBMITTED,
            'name' => $name,
            'identity_number' => $identity_number,
            'image_main' => $image_main,
            'image_1' => $image_1,
            'image_2' => $image_2,
        ]);

        return $verification->save() ? $verification : false;
    }



    public static function generateSN()
    {
        return date('YmdHis') . rand(10, 99);
    }

    /**
     * @param $name
     * @param $number
     * @return bool
     */
    public static function existsValid($name, $number)
    {
        return static::find()
            ->where([
                'name' => $name,
                'identity_number' => $number,
            ])
            ->andWhere([
                'in',
                'status',
                [static::STATUS_SUBMITTED, static::STATUS_DONE]
            ])
            ->limit(1)
            ->exists();
    }

    /**
     * @param $name
     * @param $number
     * @return bool
     */
    public static function existsValidByIdNumber($number)
    {
        return static::find()
            ->where([
                'identity_number' => $number,
            ])
            ->andWhere([
                'in',
                'status',
                [static::STATUS_SUBMITTED, static::STATUS_DONE]
            ])
            ->limit(1)
            ->exists();
    }

    /**
     * 判断用户是否认证过
     * @param $user_id
     * @return bool
     */
    public static function existsValidByUserId($user_id)
    {
        return static::find()
            ->where([
                'user_id' => $user_id,
            ])
            ->andWhere([
                'in',
                'status',
                [static::STATUS_SUBMITTED, static::STATUS_DONE]
            ])
            ->limit(1)
            ->exists();
    }

    /**
     * 判断用户是否提交过认证
     * @param $user_id
     * @return bool
     */
    public static function isSubmitValid($user_id)
    {
        return static::find()
            ->where([
                'user_id' => $user_id,
            ])
            ->limit(1)
            ->exists();
    }


    public function attributesForCreated()
    {
        return [
            'verification_sn' => (string)$this->verification_sn,
            'status' => (string)$this->status,
            'status_text' => static::$lib_status[$this->status],
            'created_at' => (string)$this->created_at,
            'name' => $this->name,
            'identity_number' => (string)$this->identity_number,
            'image_main' => (string)$this->image_main,
            'image_1' => (string)$this->image_1,
            'image_2' => (string)$this->image_2,
        ];
    }


    public function attributesForList()
    {
        $status = $this->id ? $this->status : static::STATUS_UNSUBMITTED;
        return [
            'name' => (string)$this->name,
            'identity_number' => (string)$this->identity_number,
            'image_main' => (string)$this->image_main,
            'image_1' => (string)$this->image_1,
            'image_2' => (string)$this->image_2,
            'status' => (string)$status,
            'status_text' => '个人认证'.static::$lib_status[$status],
        ];
    }

    /**
     * @param $verification_sn
     * @return Verification|null
     */
    public static function findBySn($verification_sn)
    {
        return static::findOne([
            'verification_sn' => $verification_sn,
        ]);
    }


    /**
     * 更新认证
     */
    public function updateVerified($status)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {

            $this->status = $status;
            $this->reviewed_at = time();

            if(false === $this->save()){
                throw new \yii\base\ErrorException('状态更新');
            }

            $transaction->commit();
            return true;
        } catch (\Throwable $th) {
            $transaction->rollBack();

            throw new \yii\base\ErrorException($th->getMessage());
        }
        return false;
    }


    /**
     * 关联用户
     *
     * @return void
     */
    public function getUser()
    {
        return $this->hasOne(\api\models\User::className(), ['id' => 'user_id']);
    }




}
