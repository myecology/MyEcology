<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "iec_shop".
 *
 * @property int $id
 * @property string $name 商家名称
 * @property string $contact 联系人
 * @property string $phone 电话号码
 * @property int $type_id 行业类别
 * @property int $province_id 省id
 * @property int $city_id 市id
 * @property int $district_id 区id
 * @property string $address 详细地址
 * @property string $introduction 店铺简介
 * @property string $license 营业执照
 * @property string $store_photos 店铺照片
 * @property int $status 审核状态 0审核中 10已通过 20已拒绝
 * @property string $refuse_reason 拒绝原因
 * @property int $created_at 创建时间
 * @property int $updated_at 更新时间
 * @property int $weight 权重
 * @property string $userid
 * @property string $lat
 * @property string $lng
 */
class Shop extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_shop';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lat', 'lng'], 'safe'],
            [['type_id', 'province_id', 'city_id', 'district_id', 'status', 'created_at', 'updated_at', 'weight'], 'integer'],
            [['introduction', 'store_photos', 'refuse_reason'], 'string'],
            [['name', 'address', 'license'], 'string', 'max' => 255],
            [['contact', 'phone', 'userid'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '店铺名称',
            'contact' => '联系人',
            'phone' => '电话号码',
            'type_id' => '行业类别',
            'province_id' => '省',
            'city_id' => '市',
            'district_id' => '区',
            'address' => '地址',
            'introduction' => '店铺简介',
            'license' => '营业执照',
            'store_photos' => '店铺照片',
            'status' => '审核状态',
            'refuse_reason' => '拒绝原因',
            'created_at' => '添加时间',
            'updated_at' => '更新时间',
            'weight' => '权重',
        ];
    }


    /**
     * 审核状态
     */
    public function updateStatus($status,$refuse_reason = '')
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {

            $this->status = $status;
            $this->refuse_reason = $refuse_reason;
            $this->updated_at = time();

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
}
