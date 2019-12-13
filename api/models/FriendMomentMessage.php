<?php

namespace api\models;

use Yii;

/**
 * This is the model class for table "iec_friend_moment_message".
 *
 * @property int $id
 * @property string $userid userid
 * @property int $type 类型
 * @property int $momentid 朋友圈ID
 * @property int $moment_type 朋友圈类型
 * @property string $in_userid 用户userid
 * @property string $to_userid 对方userid
 * @property int $is_reply 是否回复
 * @property string $content 内容
 * @property int $created_at 创建时间
 */
class FriendMomentMessage extends \yii\db\ActiveRecord
{

    const LIKE_TYPE = 10;
    const REPLY_TYPE = 20;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_friend_moment_message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userid', 'momentid', 'in_userid', 'created_at'], 'required'],
            [['moment_type', 'is_reply'], 'default', 'value' => 0],
            [['to_userid', 'content'], 'default', 'value' => ''],
            [['type', 'momentid', 'moment_type', 'is_reply', 'created_at'], 'integer'],
            [['userid', 'in_userid', 'to_userid', 'content'], 'string', 'max' => 255],
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
            'momentid' => 'Momentid',
            'moment_type' => 'Moment Type',
            'in_userid' => 'In Userid',
            'to_userid' => 'To Userid',
            'is_reply' => 'Is Reply',
            'content' => 'Content',
            'created_at' => 'Created At',
        ];
    }

    //  写入数据
    public static function add($users, $model, $type){

        //  查询朋友圈内容
        $friendMomentModel = FriendMoment::findOne($model->momentid);
        $data = [];
        
        $momentContent = $friendMomentModel->content ?: '';
        if($type == 10){
            $base = ['in_userid' => $model->userid, 'to_userid' => '', 'content' => '', $momentContent, 'is_reply' => 0];
        }else{
            $base = ['in_userid' => $model->in_userid, 'to_userid' => $model->to_userid, 'content' => $model->content, $momentContent, 'is_reply' => $model->is_reply];
        }
        foreach($users as $user){
            $data[] = array_merge([
                'userid' => $user,
                'type' => $type,
                'momentid' => $model->momentid,
                'created_at' => $model->created_at,
                'moment_type' => $friendMomentModel->type,
            ], $base);
        }

        //  一次性写入数据
        Yii::$app->db->createCommand()->batchInsert(
            static::tableName(),
            ['userid', 'type', 'momentid', 'created_at', 'moment_type', 'in_userid', 'to_userid', 'content', 'moment_content', 'is_reply'],
            $data
        )->execute();
    }



    //  图片
    public function getImage()
    {
        return $this->hasOne(\api\models\Image::className(), ['origin' => 'momentid'])->select("origin,thumbnail");
    }

    /**
     * 关联自身userid
     *
     * @return void
     */
    public function getInUser()
    {
        return $this->hasOne(\api\models\User::className(), ['userid' => 'in_userid'])->with(['friend'])->select('userid,username,nickname,iecid,headimgurl');
    }

    /**
     * 关联对方userid
     *
     * @return void
     */
    public function getToUser()
    {
        return $this->hasOne(\api\models\User::className(), ['userid' => 'to_userid'])->with(['friend'])->select('userid,username,nickname,iecid,headimgurl');
    }
}
