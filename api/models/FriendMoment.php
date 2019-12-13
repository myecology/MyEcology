<?php

namespace api\models;

use api\controllers\RongCloud;
use api\models\Image;
use common\models\Alioss;
use yii\helpers\Json;
use api\models\UserFriend;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\imagine\Image as HelperImage;

/**
 * This is the model class for table "iec_friend_circle".
 *
 * @property int $id
 * @property string $userid userid
 * @property int $type 类型
 * @property string $content 正文内容
 * @property int $linkid 链接ID
 * @property string $address 地址
 * @property int $sort 状态
 * @property int $hot 状态
 * @property int $status 状态
 * @property int $created_at
 */
class FriendMoment extends ActiveRecord
{
    public $images;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_friend_moment';
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
                    return Yii::$app->user->identity->userid;
                },
            ],
            //创建时间
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }

    /**s
     * 场景事物支持
     * @return [type] [description]
     */
    public function transactions()
    {
        return [
            'Text' => self::OP_INSERT | self::OP_UPDATE | self::OP_DELETE,
            'LinkText' => self::OP_INSERT | self::OP_UPDATE | self::OP_DELETE,
            'ImgText' => self::OP_INSERT | self::OP_UPDATE | self::OP_DELETE,
        ];
    }

    /**
     * 写入后事件
     *
     * @param [type] $insert
     * @param [type] $changedAttributes
     * @return void
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {

            if ($this->type == 3) {
                $this->addImgText();
            }

            //redis 有序集合载入朋友圈关系
            $this->redisUserFriendMoment();
        } else {
            if ($this->status == 0) {
                $this->redisDeleteFriendMoment();
            }
        }
    }

    /**
     * 场景
     *
     * @return void
     */
    public function scenarios()
    {
        return [
            'update' => [],
            'delete' => [],
            'Text' => ['type', 'content', 'address'],
            'LinkText' => ['type', 'content', 'address', 'link'],
            'ImgText' => ['type', 'content', 'address', 'images'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['type', 'required'],
            ['type', 'in', 'range' => [1, 2, 3]],
            ['address', 'default', 'value' => ''],
            ['content', 'required', 'on' => ['Text']],
            ['address', 'string', 'max' => 255],
            ['link', 'required', 'on' => ['LinkText']],
            ['images', 'required', 'on' => 'ImgText'],
            ['images', 'file', 'skipOnEmpty' => true, 'checkExtensionByMimeType' => false, 'extensions' => 'png, jpg', 'maxFiles' => 9, 'maxSize' => 1024 * 1024 * 2, 'tooBig' => '图片大小不能超过2MB', 'on' => ['ImgText']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => 'Userid',
            'type' => 'Type',
            'content' => 'Content',
            'linkid' => 'Linkid',
            'address' => 'Address',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }

    /**
     * 处理图片
     *
     * @return void
     */
    public function addImgText()
    {
        foreach ($this->images as $file) {
            $model = new Alioss();
            $model->image = $file;

            $url = $model->upload();
//            $string = $this->generateString();
//            $basePath = 'uploads/moments/' . date('Ymd');
//            $imagePath = Yii::getAlias('@images/web/' . $basePath);
//            $imageName = '/' . $string . '.' . $file->extension;

            $imageInfo = getimagesize($file->tempName);
            /*//  如果文件夹不存在则创建
            if (!file_exists($imagePath)) {
                FileHelper::createDirectory($imagePath);
            }
            $file->saveAs($imagePath . $imageName);

            //  生成缩略图
            $thumbnailPath = $imagePath . '/' . $string . '_thumbnail.' . $file->extension;
            HelperImage::thumbnail($imagePath . $imageName, 400, 400)
                ->save($thumbnailPath, ['quality' => 80]);*/

            //  载入图片
            $image = new Image();
            $image->setAttributes([
                'type' => Image::TYPE_MOMENT,
                'userid' => Yii::$app->user->identity->userid,
                'origin' => $this->id,
                'url' => $url,
                'thumbnail' => $url,
                'width' => $imageInfo[0],
                'height' => $imageInfo[1],
            ]);
            if (false === $image->save()) {
                throw new \yii\web\HttpException(500, '上传图片失败');
            }
        }
    }

    /**
     * 写入朋友圈数据
     *
     * @param [type] $momentid
     * @return void
     */
    protected function redisUserFriendMoment()
    {
        //获取朋友关系
        $friends = UserFriend::find()->select('to_userid')->where(['in_userid' => Yii::$app->user->identity->userid, 'status' => 1])->asArray()->all();
        $friends[]['to_userid'] = Yii::$app->user->identity->userid;
        foreach ($friends as $friend) {
            $model = new FriendMomentUser();
            $model->userid = $friend['to_userid'];
            $model->moment_id = $this->id;
            $model->save();
            // Yii::$app->redis->zadd($friend['to_userid'], Yii::$app->redis->zcard($friend['to_userid']), $this->id);
        }
    }

    /**
     * 删除朋友圈 redis数据
     *
     * @return void
     */
    // protected function redisDeleteUserFriendMoment()
    // {
    //     $friends = UserFriend::find()->select('to_userid')->where(['in_userid' => Yii::$app->user->identity->userid, 'status' => 1])->asArray()->all();
    //     $friends[]['to_userid'] = Yii::$app->user->identity->userid;

    //     foreach ($friends as $friend) {
    //         $model = FriendMomentUser::find()->where(['userid' => $friend['to_userid']])->one();
    //         $model->delete();
    //         // Yii::$app->redis->zrem($friend['to_userid'], $this->id);
    //     }
    // }

    /**
     * 删除朋友圈数据
     *
     * @return void
     */
    protected function redisDeleteFriendMoment()
    {
        //  删除朋友圈
        $friends = UserFriend::find()->select('to_userid')->where(['in_userid' => Yii::$app->user->identity->userid])->asArray()->all();
        $friends[]['to_userid'] = Yii::$app->user->identity->userid;
        foreach ($friends as $friend) {
            $model = FriendMomentUser::find()->where(['userid' => $friend['to_userid'], 'moment_id' => $this->id])->one();
            if($model){
                $model->delete();
            }
            // Yii::$app->redis->zrem($friend['to_userid'], Yii::$app->redis->zcard($friend['to_userid']), $this->id);
        }
    }

    /**
     * 发送朋友圈通知
     *
     * @param [type] $momentID
     * @param [type] $askid
     * @param [type] $type
     * @return void
     */
    public static function sendMomentMessage($momentID, $model, $type)
    {
        //  发送朋友圈通知
        $likeUsers = FriendMomentLike::find()->select("userid")->where(['momentid' => $momentID, 'status' => 10])->column();
        $replyUsers = FriendMomentReply::find()->select("in_userid")->where(['momentid' => $momentID, 'status' => 10])->column();
        $data = array_unique(array_merge($likeUsers, $replyUsers));
        $data = array_flip($data);
        if(isset($data[Yii::$app->user->identity->userid])){
            unset($data[Yii::$app->user->identity->userid]);
        }

        $getUsers = array_flip($data);
        //  朋友圈发布者userid

        $friendMoment = static::findOne($momentID);
        $getUsers[] = $friendMoment->userid;

        $friendUsers = UserFriend::find()->select("to_userid")->where(["in_userid" => Yii::$app->user->identity->userid, 'status' => 1])->column();
        $toUsers = array_intersect($getUsers, $friendUsers);

        if(!empty($toUsers)){

            //  写入数据库
            FriendMomentMessage::add($toUsers, $model, $type);

            $content = [
                'momentId' => (string) $model->id,
                'extra' => (string) $model->created_at,
                'type' => $type,
            ];
            RongCloud::getInstance()->sendPrivateMomentMessage(Yii::$app->user->identity->userid, $toUsers, Json::encode($content));
        }
    }

    /**
     *  重置热度
     *
     * @param [type] $id
     * @return void
     */
    public static function resetHot($id)
    {
        $model = self::findOne($id);

        if ($model) {
            //  获取热度
            $model->scenario = 'update';
            $likeCount = FriendMomentLike::find()->where(['momentid' => $id, 'status' => 10])->count();
            $replyCount = FriendMomentReply::find()->where(['momentid' => $id, 'status' => 10])->count();
            $replyDistinctCount = FriendMomentReply::find()->select("in_userid")->distinct()->where(['momentid' => $id, 'status' => 10])->count();

            $model->like = $likeCount;
            $model->reply = $replyCount;
            $model->hot = $likeCount + $replyDistinctCount;
            if (false === $model->save()) {
                throw new \yii\web\HttpException(500, '点赞失败');
            }
        }
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

    /**
     * 关联用户
     *
     * @return void
     */
    public function getUser()
    {
        return $this->hasOne(\api\models\User::className(), ['userid' => 'userid'])->with(['friend'])->select('username,userid,nickname,iecid,headimgurl');
    }
    /**
     * 关联图片
     *
     * @return void
     */
    public function getImage()
    {
        return $this->hasMany(\api\models\Image::className(), ['origin' => 'id']);
    }

    /**
     * 关联点赞
     *
     * @return void
     */
    public function getMomentLike()
    {
        return $this->hasmany(\api\models\FriendMomentLike::className(), ['momentid' => 'id'])->with(['user'])->where(['status' => 10]);
    }

    public function getIsMomentLike()
    {
        return $this->hasOne(\api\models\FriendMomentLike::className(), ['momentid' => 'id'])->select("momentid,status")->where(['status' => 10, 'userid' => Yii::$app->user->identity->userid]);
    }

    /**
     * 关联回复
     *
     * @return void
     */
    public function getMomentReply()
    {
        return $this->hasMany(\api\models\FriendMomentReply::className(), ['momentid' => 'id'])->with(['inUser', 'toUser'])->where(['status' => 10]);
    }
}
