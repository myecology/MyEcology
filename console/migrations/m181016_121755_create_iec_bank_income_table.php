<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_bank_income`.
 */
class m181016_121755_create_iec_bank_income_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_bank_income', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->comment('收益名称'),
            'day' => $this->integer()->notNull()->defaultValue(0)->comment('收益周期'),
            'num' => $this->smallInteger()->notNull()->defaultValue(0)->comment('次数'),
            'type' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('收益类型'),
            'created_at' => $this->integer()->notNull()->comment('创建时间')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_bank_income');
    }
}
