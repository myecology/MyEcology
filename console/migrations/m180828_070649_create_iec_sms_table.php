<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_msm`.
 */
class m180828_070649_create_iec_sms_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_sms', [
            'id' => $this->primaryKey(),
            'type' => $this->smallInteger()->notNull()->defaultValue(1)->comment('短信类型'),
            'phone' => $this->char(11)->notNull()->comment('手机号码'),
            'code' => $this->char(6)->notNull()->comment('验证码'),
            'status' => $this->smallInteger()->notNull()->comment('返回状态吗'),
            'response' => $this->string(255)->notNull()->comment('返回信息'),
            'createtime' => $this->integer()->notNull()->comment('创建时间')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_sms');
    }
}
