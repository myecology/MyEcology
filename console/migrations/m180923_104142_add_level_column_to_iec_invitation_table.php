<?php

use yii\db\Migration;

/**
 * Handles adding level to table `iec_invitation`.
 */
class m180923_104142_add_level_column_to_iec_invitation_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_invitation', 'level', $this->integer()->defaultValue(1)->comment("层级"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_invitation', 'level');
    }
}
