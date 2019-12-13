<?php

namespace api\models;

use common\models\Area;
use common\models\Shop;
use Yii;
use yii\base\ErrorException;
use yii\base\Model;

/**
 * ShopAdd form
 */
class ShopAddForm extends Model
{
    public $userid;
    public $name;
    public $contact;
    public $phone;
    public $type_id;
    public $province_id;
    public $city_id;
    public $district_id;
    public $address;
    public $introduction;
    public $license;
    public $store_photos;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'contact', 'phone', 'address', 'introduction'],'trim'],
            [['name', 'contact', 'type_id', 'phone', 'province_id', 'city_id', 'district_id', 'address', 'introduction',
                'license', 'store_photos'], 'required'],

            [['introduction',], 'string', 'max' => 120],

            ['phone', 'match', 'pattern' => '/^[1][3456789][0-9]{9}$/', 'message' => '手机号码格式不对'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'userid'  => 'userid',
            'name'    => '商家名称',
            'contact' => '联系人',
            'phone'   => '电话号码',
            'type_id' => '行业类型',
            'province_id' => '省份',
            'city_id' => '城市',
            'district_id' => '区域',
            'address' => '详细地址',
            'introduction' => '店铺简介',
            'license' => '营业执照',
            'store_photos' => '店铺照片',
            'status' => '审核状态',
            'refuse_reason' => '拒绝原因',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'lat' => '经度',
            'lng' => '维度',
        ];
    }


    /**
     * 商家入驻
     * @return bool|Shop
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $model = new Shop();

        $store_photos = is_array($this->store_photos) ? implode(',',$this->store_photos) : $this->store_photos;

        $area = Area::getLatAndLng($this->address);
        if (!$area) {
            throw new ErrorException('无法获取地址信息，请确认地址是否正确',1008);
        }

        $model->setAttributes([
            'name' => $this->name,
            'contact' => $this->contact,
            'phone' => $this->phone,
            'type_id' => $this->type_id,
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'district_id' => $this->district_id,
            'address' => $this->address,
            'introduction' => $this->introduction,
            'license' => $this->license,
            'store_photos' => $store_photos,
            'lat' => $area['lat'],
            'lng' => $area['lng'],
        ]);

        return $model->save() ? $model : false;
    }


    public function update($userid)
    {
        if (!$this->validate()) {
            return false;
        }

        $model = Shop::find()->where(['userid'=>$userid])->one();

        if (!$model) {
            throw new ErrorException('店铺信息不存在',2000);
        }

        $area = Area::getLatAndLng($this->address);
        if (!$area) {
            throw new ErrorException('无法获取地址信息，请确认地址是否正确',1008);
        }

        $store_photos = is_array($this->store_photos) ? implode(',',$this->store_photos) : $this->store_photos;

        $model->setAttributes([
            'name' => $this->name,
            'contact' => $this->contact,
            'phone' => $this->phone,
            'type_id' => $this->type_id,
            'province_id' => $this->province_id,
            'city_id' => $this->city_id,
            'district_id' => $this->district_id,
            'address' => $this->address,
            'introduction' => $this->introduction,
            'license' => $this->license,
            'store_photos' => $store_photos,
            'status' => 0,
            'updated_at' => time(),
            'lat' => $area['lat'],
            'lng' => $area['lng'],
        ]);

        return $model->save() ? $model : false;
    }



    /**
     * 返回第一条错误信息
     * @param $model
     * @return bool|mixed
     */
    public static function getModelError($model) {
        $errors = $model->getErrors();    //得到所有的错误信息
        if(!is_array($errors)){
            return true;
        }
        $firstError = array_shift($errors);
        if(!is_array($firstError)) {
            return true;
        }
        return array_shift($firstError);
    }
}
