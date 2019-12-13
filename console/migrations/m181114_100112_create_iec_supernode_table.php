<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_supernode_node`.
 */
class m181114_100112_create_iec_supernode_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_supernode', [
            'id' => $this->primaryKey(),
            'uid' => $this->integer()->notNull()->comment('用户ID'),
            'status'=>$this->tinyInteger()->notNull()->defaultValue(10)->comment('状态：10开启/0关闭'),
            'lvl' => $this->smallInteger(5)->notNull()->defaultValue(0)->comment('超级节点等级'),
            'amount' => $this->money(20,8)->notNull()->defaultValue(0)->comment('数量'),
            'description' => $this->string()->notNull()->comment('描述'),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}    l
     */
    public function safeDown()
    {
        $this->dropTable('iec_supernode');
    }
}
