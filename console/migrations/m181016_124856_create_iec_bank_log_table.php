<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_bank_log`.
 */
class m181016_124856_create_iec_bank_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_bank_log', [
            'id' => $this->primaryKey(),
            'uid' => $this->integer()->notNull()->comment('用户ID'),
            'type' => $this->tinyInteger()->notNull()->comment('类型'),
            'has_id' => $this->integer()->notNull()->comment('关联ID'),
            'title' => $this->string()->notNull()->comment('标题'),
            'content' => $this->string()->notNull()->comment('内容'),
            'money' => $this->money(20,8)->notNull()->comment('数量'),
            'created_at' => $this->integer()->notNull()->comment('创建时间')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_bank_log');
    }
}
