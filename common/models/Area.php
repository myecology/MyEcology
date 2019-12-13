<?php

namespace common\models;
use yii\helpers\ArrayHelper;

use Yii;

/**
 * This is the model class for table "area".
 *
 * @property int $id
 * @property int $pid
 * @property string $node
 * @property string $name
 * @property int $level
 * @property double $lat
 * @property double $lng
 * @property string $first_letter
 * @property string $is_hot
 */
class Area extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'area';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'name', 'level', 'lat', 'lng'], 'required'],
            [['id', 'pid', 'level'], 'integer'],
            [['lat', 'lng'], 'number'],
            [['node'], 'string', 'max' => 64],
            [['name'], 'string', 'max' => 32],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => 'Pid',
            'node' => 'Node',
            'name' => 'Name',
            'level' => 'Level',
            'lat' => 'Lat',
            'lng' => 'Lng',
            'first_letter' => 'First Letter',
            'is_hot' => 'Is Hot',
        ];
    }


    /**
     * 获取热门城市
     */
    public static function getHotCity()
    {
        $result = null;
        $list = Area::find()->where(['is_hot'=>1])->limit(4)->orderBy(['first_letter'=>SORT_ASC])->all();
        foreach ($list as $key => $val) {
            $result[] = $val->attributesList();
        }
        return $result;
    }


    public function attributesList()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }


    public static function getAreaName($id)
    {
        $area = Area::find()->where(['id'=>$id])->one();
        return $area ? $area->name : '';
    }

    public static function getProvince()
    {
        return Area::find()->where(['level'=>1])->all();
    }


    public static function getCity($id)
    {
        return Area::find()->where(['pid'=>$id,'level'=>2])->asArray()->all();
    }


    public static function getCityList($pid = null)
    {
        $model = Area::findAll(['pid'=>$pid]);
        return ArrayHelper::map($model, 'id', 'name');
    }


    public static function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6367000;
        $lat1 = ($lat1 * pi()) / 180;
        $lng1 = ($lng1 * pi()) / 180;
        $lat2 = ($lat2 * pi()) / 180;
        $lng2 = ($lng2 * pi()) / 180;
        $calcLongitude      = $lng2 - $lng1;
        $calcLatitude      = $lat2 - $lat1;
        $stepOne  = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo            = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;
        return round($calculatedDistance).'m';
    }


    /**
     * 根据地址获取经纬度
     * @param $address
     * @return array
     */
    public static function getLatAndLng($address)
    {
        $url = 'http://api.map.baidu.com/geocoder/v2/?address=' . $address . '&output=json&ak=7w0ElbkFWRhABwXKx4NsvDBVLBsdGnQG';
        if ($result = file_get_contents($url)) {
            $data = array();
            $res = json_decode($result, true);

            if ($res['status'] == 0) {
                $results = $res['result'];
                $data['lng'] = $results['location']['lng'];
                $data['lat'] = $results['location']['lat'];
            }
            return $data;
        }
    }

    /**
     * 经纬度坐标转换成百度的
     * @param $lat
     * @param $lng
     * @return mixed
     */
    public static function changeToBaidu($lat,$lng)
    {
        $apiurl = 'http://api.map.baidu.com/geoconv/v1/?coords='.$lng.','.$lat.'&from=1&to=5&ak=7w0ElbkFWRhABwXKx4NsvDBVLBsdGnQG';
        $file = file_get_contents($apiurl);$arrpoint = json_decode($file, true);
        return $arrpoint['result'][0];
    }





}
