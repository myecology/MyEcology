<?php

namespace common\models\bank;

use Yii;
use \yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeBehavior;

/**
 * This is the model class for table "iec_bank_log".
 *
 * @property int $id
 * @property int $uid 用户ID
 * @property int $type 类型
 * @property int $has_id 关联ID
 * @property string $title 标题
 * @property string $content 内容
 * @property string $money 数量
 * @property int $created_at 创建时间
 */
class Log extends ActiveRecord
{
    const TYPE_BACK_PRODUCT = -1;               //  退回
    const TYPE_LOCK_PRODUCT = 10;               //  锁仓
    const TYPE_UN_LOCK_PRODUCT = 20;            //  解锁
    const TYPE_PROFIT_PRODUCT = 30;             //  收益

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_bank_log';
    }

    /**
     * 模型行为
     * @return [type] [description]
     */
    public function behaviors()
    {
        return [
            //创建时间
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'has_id', 'title', 'money'], 'required'],
            [['type', 'has_id'], 'integer'],
            [['money'], 'number'],
            ['content', 'default', 'value' => ''],
            [['title', 'content'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'type' => 'Type',
            'has_id' => 'Has ID',
            'title' => 'Title',
            'content' => 'Content',
            'money' => 'Money',
            'created_at' => 'Created At',
        ];
    }

    /**
     * 明细
     *
     * @param [type] $type
     * @param [type] $model
     * @return void
     */
    public static function bankLog($type, $model)
    {
        $logModel = new self();
        switch ($type) {
            case static::TYPE_LOCK_PRODUCT:
                $logModel->title = $model->product->name;
                break;
            case static::TYPE_UN_LOCK_PRODUCT:
                $logModel->title = $model->product->name;
                break;
            case static::TYPE_PROFIT_PRODUCT:
                $logModel->title = $model->product->name;
                break;
            case static::TYPE_BACK_PRODUCT:
                $logModel->title = $model->product->name;
                break;
            default:
                exit;
                break;
        }
        $logModel->uid = $model->uid;
        $logModel->type = $type;
        $logModel->has_id = $model->id;
        $logModel->money = $model->amount;

        if(false === $logModel->save()){
            throw new \yii\base\ErrorException('日志写入失败');
        }

        return $logModel;
    }

    /**
     * 关联订单
     *
     * @return void
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'has_id']);
    }



}
