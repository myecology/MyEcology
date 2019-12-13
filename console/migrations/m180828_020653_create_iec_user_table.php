<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_user`.
 */
class m180828_020653_create_iec_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_user', [
            'id' => $this->primaryKey(),
            'upid' => $this->integer()->notNull()->defaultValue(0)->comment('上级用户ID'),
            'pool_id' => $this->integer()->notNull()->defaultValue(0)->comment('糖果ID'),
            'area' => $this->smallInteger()->notNull()->defaultValue(86)->comment('国家区号'),
            'initials' => $this->char(1)->notNull()->comment('首字母'),
            'username' => $this->string()->notNull()->unique(),
            'nickname' => $this->string()->notNull()->comment('昵称'),
            'iecid' => $this->string(50)->notNull()->unique(),
            'is_iec' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('是否修改IEC'),
            'is_wallet_protocol' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('是否阅读协议'),
            'userid' => $this->string()->notNull()->unique()->comment('userID'),
            'headimgurl' => $this->string()->comment('用户头像'),
            'access_token' => $this->string()->notNull()->comment('token'),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'payment_hash' => $this->string(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->unique(),
            'longitude' => $this->string()->comment('经度'),
            'latitude' => $this->string()->comment('纬度'),
            'sex' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('性别'),
            'age' => $this->smallInteger()->notNull()->defaultValue(0)->comment('年龄'),
            'country' => $this->string()->comment('国家'),
            'province' => $this->string()->comment('省'),
            // 'county' => $this->string()->comment('县'),
            'city' => $this->string()->comment('城市'),
            'area_id' => $this->integer()->defaultValue(0)->comment('区域ID'),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'code' => $this->char(6)->notNull()->unique()->comment('邀请码'),
            'friend' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('好友验证'),
            'crontab_status' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('定时计划状态'),
            'description' => $this->string()->comment('个性说明'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_user');
    }
}
