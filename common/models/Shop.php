<?php

namespace common\models;

use yii\data\ActiveDataProvider;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
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
 * @property int $lat 经度
 * @property int $lng 维度
 *
 */
class Shop extends \yii\db\ActiveRecord
{

    public static $lib_status = [
        0  =>  '审核中',
        10 =>  '审核通过',
        20 =>  '审核拒绝',
    ];

    public $_user_lat;
    public $_user_lng;

    const STATUS_SUBMITTED = 0; //审核中
    const STATUS_PASS = 10;  //审核通过
    const STATUS_REFUSE = 20;  //审核拒绝


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
            [['type_id', 'province_id', 'city_id', 'district_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['introduction', 'store_photos', 'refuse_reason'], 'string'],
            [['name', 'address', 'license'], 'string', 'max' => 255],
            [['contact', 'phone'], 'string', 'max' => 32],
        ];
    }

    /**
     * 模型行为
     * @return [type] [description]
     */
    public function behaviors()
    {
        return [
            //  code
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'userid',
                ],
                'value' => function ($event) {
                    return yii::$app->user->identity->userid;
                },
            ],
            //创建时间
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
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

    public static function getUserLat()
    {
        $user_lat = yii::$app->request->post('lat');
        return $user_lat ? $user_lat : '39.90';
    }

    public static function getUserLng()
    {
        $user_lng = yii::$app->request->post('lng');
        return $user_lng ? $user_lng : '39.90';
    }

    /**
     * 商家列表搜索条件
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $lat = $params['lat'];
        $lng = $params['lng'];

        $query = Shop::find()->select(new Expression('*,6378.138 * 2 * ASIN(SQRT(POW(SIN(('.$lat.' * PI() / 180 - lat * PI() / 180) / 2), 2) + COS('.$lat.' * PI() / 180) * COS(lat * PI() / 180) * POW(SIN(('.$lng.' * PI() / 180 - lng * PI() / 180) / 2), 2))) *1000 as distance'))
                            ->orderBy(['distance'=>SORT_ASC])
                            ->where(['status'=>self::STATUS_PASS,'city_id'=>$params['city_id']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => isset($params['page_size']) ? $params['page_size'] : 20,
                'page' => isset($params['page']) ? $params['page'] - 1 : 0,
            ]
        ]);

        $this->load($params, '');

        if (!$this->validate()) {
            $query->andWhere("false");
            return $dataProvider;
        }

        if (isset($params['keyword'])) {
            $query->andWhere(['like', 'name', $params['keyword']]);
        }

        if ($this->type_id) {
            $query->andWhere(['type_id'=> $this->type_id]);
        }

        return $dataProvider;

    }


    public function attributesForList()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'introduction' => $this->introduction,
            'store_photos' => $this->store_photos,
            'address' => $this->address,
            'distance' => Area::getDistance($this->getUserLat(),$this->getUserLng(),$this->lat,$this->lng),
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }


    /**
     * 获取猜你喜欢列表
     */
    public static function getLikeShopList()
    {
        $city_id = yii::$app->request->post('city_id');
        $shopList = null;

        $lat  = self::getUserLat();
        $lng  = self::getUserLng();

        $list = Shop::find()->select(new Expression('*,6378.138 * 2 * ASIN(SQRT(POW(SIN(('.$lat.' * PI() / 180 - lat * PI() / 180) / 2), 2) + COS('.$lat.' * PI() / 180) * COS(lat * PI() / 180) * POW(SIN(('.$lng.' * PI() / 180 - lng * PI() / 180) / 2), 2))) *1000 as distance'))
            ->orderBy(['distance'=>SORT_ASC,'id'=>SORT_DESC])
            ->where(['status'=>self::STATUS_PASS,'city_id'=>$city_id])
            ->limit(2)
            ->all();
        /**
         * @var Shop $model_shop
         */
        foreach ($list as $key => $model_shop) {
            $shopList[] = $model_shop->attributesForList();
        }
        return $shopList;
    }


    /**
     * 获取商家详情
     * @param $id
     * @return array|null
     */
    public function getDetailById($id)
    {
        $result = null;
        $detail = Shop::find()->where(['id'=>$id])->one();
        if ($detail) {
            $result = [
                'userid' => $detail->userid,
                'name' => $detail->name,
                'contact' => $detail->contact,
                'phone' => $detail->phone,
                'address' => $detail->address,
                'introduction' => $detail->introduction,
                'license' => $detail->license,
                'store_photos' => $detail->store_photos,
                'status' => $detail->status,
                'status_text' => self::$lib_status[$detail->status],
                'refuse_reason' => $detail->refuse_reason,
                'is_self' => $this->getIsSelf($detail->userid),
                'distance' => Area::getDistance($this->getUserLat(),$this->getUserLng(),$detail->lat,$detail->lng),
                'lat' => $detail->lat,
                'lng' => $detail->lng,
            ];
        }
        return $result;
    }

    /**
     * 获取商家详情
     * @param $userid
     * @return array|null
     */
    public function getDetailByUserid($userid)
    {
        $result = null;
        $detail = Shop::find()->where(['userid'=>$userid])->one();
        if ($detail) {
            $result = [
                'userid' => $detail->userid,
                'name' => $detail->name,
                'tyep_id' => $detail->type_id,
                'contact' => $detail->contact,
                'phone' => $detail->phone,
                'province_id' => $detail->province_id,
                'city_id' => $detail->city_id,
                'district_id' => $detail->district_id,
                'address' => $detail->address,
                'introduction' => $detail->introduction,
                'license' => $detail->license,
                'store_photos' => $detail->store_photos,
                'status' => $detail->status,
                'status_text' => self::$lib_status[$detail->status],
                'refuse_reason' => $detail->refuse_reason,
                'is_self' => $this->getIsSelf($detail->userid),
            ];
        }
        return $result;
    }


    public function getIsSelf($userid = null)
    {
        $now_userid = yii::$app->user->identity->userid;
        return $userid == $now_userid ? true : false;
    }


    public static function isExist($userid)
    {
        return Shop::find()->where(['userid'=>$userid])->exists();
    }


    public function getShopType()
    {
        return $this->hasOne(\common\models\ShopType::className(), ['id' => 'type_id']);
    }

    public static function userIsShop($userid){
        $shop = static::findOne([
            'userid' => $userid,
            'status' => static::STATUS_REFUSE
        ]);
        if(!empty($shop)){
            return 1;
        }else{
            return 0;
        }
    }

    public function getUser(){
        return $this->hasOne(\api\models\User::className(),['userid' => 'userid']);
    }

}
