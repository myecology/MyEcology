<?php

use yii\db\Migration;

/**
 * Handles adding txid to table `iec_deposit`.
 */
class m180928_145450_add_txid_column_to_iec_deposit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_deposit', 'txid', $this->string(64)->comment("交易ID"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_deposit', 'txid');
    }
}
