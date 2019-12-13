<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_bank_order`.
 */
class m181016_093805_create_iec_bank_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_bank_order', [
            'id' => $this->primaryKey(),
            'uid' => $this->integer()->notNull()->comment('用户ID'),
            'product_id' => $this->integer()->notNull()->comment('产品ID'),
            'rate' => $this->money(20,8)->notNull()->comment('利率'),
            'amount' => $this->money(20,8)->notNull()->comment('数量'),
            'symbol' => $this->string(50)->notNull()->comment('币种'),
            'iec_bank_order' => $this->string()->notNull()->comment('超级节点受益人'),
            'status' => $this->tinyInteger()->notNull()->comment('状态'),
            'day' => $this->smallInteger()->notNull()->comment('周期天数'),
            'endtime' => $this->integer()->notNull()->comment('结束时间'),
            'created_at' => $this->integer()->notNull()->comment('创建时间'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_bank_order');
    }
}
