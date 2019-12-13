<?php

use yii\db\Migration;

/**
 * Handles adding is_displayed to table `iec_wallet`.
 */
class m180913_074224_add_is_displayed_column_to_iec_wallet_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_wallet', 'is_displayed', $this->smallInteger(3)->defaultValue(10)->comment("开放显示"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_wallet', 'is_displayed');
    }
}
