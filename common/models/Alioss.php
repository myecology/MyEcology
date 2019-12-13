<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 2019/10/29
 * Time: 10:42 AM
 */

namespace common\models;


use api\controllers\APIFormat;
use OSS\OssClient;
use yii\base\Model;

class Alioss extends Model
{
    private  $accessKeyId = 'LTAI4FuRcHDARqE9fpoGgoQZ';
    private  $accessKeySecret = 'Nk69tilGLtITwusaRsFKYhaiKtuBfs';
    private  $endpoint = "https://oss-cn-zhangjiakou.aliyuncs.com";
    private  $bucket = "antcimages";
    public  $object;
    public  $content;
    public $image;
    public function rules(){
        return [
            [
                ['image'],
                'image',
                'skipOnEmpty' => true,
                'checkExtensionByMimeType' => true,
                'extensions' => 'png, jpg, gif',
            ],
            ['image',function(){
                $this->object = date('Ymd').'/'.$this->generateString().'.'.$this->image->extension;
                $this->content = $this->image->tempName;
            }]
        ];
    }
    public function upload(){
        if($this->validate()){
            try {
                $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
                $data = $ossClient->uploadFile($this->bucket, $this->object, $this->content);
                return $data['oss-request-url'];
            }catch (\OssException $e) {
                throw new \ErrorException($e->getMessage(),$e->getCode());
            }
        }
        throw new \ErrorException(APIFormat::popError($this->getErrors()),1000);
    }

    protected function generateString()
    {
        $length = 16;
        $arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        $str = '';
        $arr_len = count($arr);
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $arr_len - 1);
            $str .= $arr[$rand];
        }
        return $str;
    }
}