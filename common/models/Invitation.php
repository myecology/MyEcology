<?php

namespace common\models;

use common\models\shop\WineLevel;
use Yii;
use yii\data\ActiveDataProvider;
use backend\models\Setting;
use common\models\InvitePool;
use common\models\Verification;
use api\models\User;

/**
 * This is the model class for table "iec_invitation".
 *
 * @property int $id
 * @property int $registerer_id 注册人ID
 * @property int $inviter_id 邀请人ID
 * @property int $level 层级
 * @property int $created_at 创建时间
 */
class Invitation extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'iec_invitation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['registerer_id', 'inviter_id', 'level', 'created_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'registerer_id' => 'Registerer ID',
            'inviter_id' => 'Inviter ID',
            'created_at' => 'Created At',
            'level' => 'Level',
        ];
    }

    /**
     * @param \api\models\User $user
     */
    public static function createInvitation(\api\models\User $user)
    {
        $key = 1;
        $user_id = $user->id;
        $time = $user->created_at;
        while(!empty($user)){
            if(empty($user->parent)){
                break;
            }
            $invitation = Invitation::findOne([
                'registerer_id' => $user_id,
                'inviter_id' => $user->parent->id,
            ]);
            if(empty($invitation)){
                $invitation = new static();
                $invitation->setAttributes([
                    'registerer_id' => $user_id,
                    'inviter_id' => $user->parent->id,
                    'created_at' => $time,
                    'level' => $key
                ]);
                $invitation->save();
            }
            $user = $user->parent;
            $key++;
        }
    }


    public function search($params, $user_id)
    {
        {
            $query = static::find()
                ->with(['registerer'])
                ->andWhere(['inviter_id' => $user_id])
                ->andWhere(['in','level',[1,2,3]])
                ->orderBy(['created_at' => SORT_DESC]);

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'page' => Yii::$app->request->post('page') - 1,
                ]
            ]);

            $this->load($params, '');

            if (!$this->validate()) {
                return $dataProvider;
            }

            return $dataProvider;
        }
    }

    public function pageData(){
        
        return [
            'nickname' => (string)$this->registerer->nicknameText,
            'amount' => 0,
            'symbol' => 'ANT',
            'created_at' => (string)$this->created_at,
            'level' => (string)Invitation::levelText($this->level),
        ];
    }

    public function getRegisterer()
    {
        return $this->hasOne(\api\models\User::className(), ['id' => 'registerer_id']);
    }

    public function getWine()
    {
        return $this->hasOne(WineLevel::className(), ['user_id' => 'inviter_id']);
    }

    /**
     * @param $user_id
     * @return array
     */
    public function summaryAsInviter($user_id)
    {
        return [
            'count' => static::find()
                ->where(['inviter_id' => $user_id])
                ->count(),
        ];
    }

    public function attributeForReward()
    {
        return [
            'userid' => $this->registerer->userid,
            'nickname' => (string)$this->registerer->nicknameText,
            'username' => (string)$this->registerer->usernameText,
            'created_at' => (string)$this->created_at,
            'level' => (string)static::levelText($this->level),
        ];
    }

    /**
     * @param int $level
     * @return bool|mixed
     */
    public static function levelText($level)
    {

        return "M".$level;
    }

    public function getVerification(){
        return $this->hasOany(Verification::className(), ['user_id' => 'registerer_id']);
    }

    /**
     * 获取下级用户
     */
    public static function Userleve($phone){
        //获取当前用户id
        // $user = User::findOne(['username'=>$phone]);
        //查询下级所有用户
        $list = static::find()
        ->leftJoin("iec_user as b","inviter_id=b.id")
        ->select("registerer_id")
        ->where(['b.username'=>$phone])
        ->asArray()->all();
        return $list;

    }

}
