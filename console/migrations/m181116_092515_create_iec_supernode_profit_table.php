<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_supernode_profit`.
 */
class m181116_092515_create_iec_supernode_profit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_supernode_profit', [
            'id' => $this->primaryKey(),
            'uid' => $this->integer()->notNull()->comment('用户ID'),
            'in_uid' => $this->integer()->notNull()->comment('购买者用户ID'),
            'title' => $this->string()->notNull()->comment('标题'),
            'node' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('普通节点'),
            'type' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('收益类型'),
            'symbol' => $this->string(50)->notNull()->comment('标志'),
            'hasid' => $this->integer()->notNull()->comment('关联ID'),
            'amount' => $this->money(20,8)->notNull()->comment('数量'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(10)->comment('状态'),
            'created_at' => $this->integer()->notNull()->comment('创建时间')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_supernode_profit');
    }
}
