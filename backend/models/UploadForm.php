<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;

/**
 * author Allen
 * File Upload Class
 */

class UploadForm extends Model
{
    const UPLOAD_FILE = 'file'; //上传文件
    const UPLOAD_IMAGE = 'image'; //上传图片

    public $inputFile; //上传的文件

    public $filePath; //文件路径
    public $fileDir = 'uploads/'; //文件目录

    /**
     *  规则
     */
    public function rules()
    {
        return [
            ['inputFile', 'required'],
            [
                ['inputFile'],
                'file',
                'skipOnEmpty' => false,
                'extensions' => ['txt', 'zip', 'pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', '7z', 'gz'],
//                'maxFiles' => 3,
                'on' => 'file',
            ],
            [
                ['inputFile'],
                'image',
                'skipOnEmpty' => true,
                'checkExtensionByMimeType' => false,
                'extensions' => 'png, jpg, gif',
                'on' => 'image',
            ],
        ];
    }

    /**
     * 场景
     */
    public function scenarios()
    {
        return [
            self::UPLOAD_FILE => ['inputFile'],
            self::UPLOAD_IMAGE => ['inputFile'],
        ];
    }

    /**
     * 上传
     */
    public function upload()
    {
        if ($this->validate()) {

            $string = $this->generateString();
            $this->filePath = $this->setUploadDir() . '/' . $string . '.' . $this->inputFile->extension;
            $this->inputFile->saveAs(Yii::getAlias('@images/web/' . $this->filePath));
            return true;
        } else {
            return false;
        }
    }



    /**
     * 设置默认的上传目录
     */
    protected function setUploadDir($fileDir = null)
    {
        $this->fileDir = $fileDir ?: $this->fileDir . date('Ymd');

        //判断是否创建文件夹
        $path = Yii::getAlias('@images/web/' . $this->fileDir);
        if (!file_exists($path)) {
            FileHelper::createDirectory($path);
        }
        return $this->fileDir;
    }

    /**
     * 生成string
     *
     * @return void
     */
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
