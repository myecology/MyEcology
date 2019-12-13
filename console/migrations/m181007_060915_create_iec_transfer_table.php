<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_transfer`.
 */
class m181007_060915_create_iec_transfer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_transfer', [
            'id' => $this->primaryKey(),

            'sender_id' => $this->integer()->comment('转出人ID'),
            'receiver_id' => $this->integer()->comment('转入人ID'),

            'symbol' => $this->string(32)->notNull()->comment('币种标识'),
            'currency_id' => $this->integer()->notNull()->comment('币种ID'),
            'amount' => $this->money(20, 8)->defaultValue(0)->comment('金额'),

            'created_at' => $this->integer()->comment('创建时间'),
            'taken_at' => $this->integer()->comment('接收时间'),

            'status' => $this->smallInteger(3)->defaultValue(10)->comment('状态'),
            'description' => $this->string(255)->notNull()->comment('描述'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_transfer');
    }
}
