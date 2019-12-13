<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_bank_profit`.
 */
class m181016_093849_create_iec_bank_profit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_bank_profit', [
            'id' => $this->primaryKey(),
            'uid' => $this->integer()->notNull()->comment('用户ID'),
            'product_id' => $this->integer()->notNull()->comment('产品ID'),
            'type' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('类型'),
            'order_id' => $this->integer()->notNull()->comment('订单ID'),
            'amount' => $this->money(20,8)->notNull()->comment('数量'),
            'symbol' => $this->string(50)->notNull()->comment('币种'),
            'created_at' => $this->integer()->notNull()->comment('创建时间')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_bank_profit');
    }
}
