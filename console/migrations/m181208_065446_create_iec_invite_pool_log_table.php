<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_invite_pool_log`.
 */
class m181208_065446_create_iec_invite_pool_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_invite_pool_log', [
            'id' => $this->primaryKey(),
            'uid' => $this->integer()->notNull()->defaultValue(0)->comment('用户ID'),
            'pool_id' => $this->integer()->notNull()->comment('糖果ID'),
            'type' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('类型，0:加款'),
            'symbol' => $this->string(50)->notNull()->comment('标示'),
            'amount' => $this->money(20,8)->notNull()->comment('数量'),
            'created_at' => $this->integer()->notNull()->comment('创建时间'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_invite_pool_log');
    }
}
