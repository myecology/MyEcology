<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_message`.
 */
class m181220_023656_create_iec_message_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_message', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->comment('用户ID'),
            'source_id' => $this->integer()->notNull()->comment('来源ID'),
            'type' => $this->tinyInteger()->notNull()->comment('类型'),
            'title' => $this->string()->notNull()->comment('标题'),
            'symbol' => $this->string(50)->notNull()->comment('币种'),
            'amount' => $this->money(20,8)->notNull()->defaultValue(0)->comment('数量'),
            'description' => $this->string()->notNull()->comment('描述'), 
            'created_at' => $this->integer()->comment('创建时间'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_message');
    }
}
