<?php 
namespace common\models\alcohol;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\base\Model;


	class Alcohol extends MOdel{
		public $token;
		/**
		 * 应用场景
		 * @return [type] [description]
		 */
		public function scenarios(){

		}

		/**
		 * 应用规则
		 * @return [type] [description]
		 */
		public function rules(){

		}

		/**
		 * 加钱
		 * @return [type] [description]
		 */
		public function addmoney(){

		}

		/**
		 * 验证token获取用户信息
		 * @return [type] [description]
		 */
		public function userinfo(){
			$token = User::findOne(['token'=>$this->token]);
		}
	}