<?php

use yii\db\Migration;

/**
 * Handles adding business_sn to table `iec_wallet_log`.
 */
class m181127_155804_add_business_sn_column_to_iec_wallet_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_wallet_log', 'business_sn', $this->string(255)->comment("业务单号"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_wallet_log', 'business_sn');
    }
}
