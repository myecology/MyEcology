<?php

use yii\db\Migration;

/**
 * Handles adding balance_changed_column_private_key to table `iec_wallet_address`.
 */
class m181105_171421_add_balance_changed_column_private_key_column_to_iec_wallet_address_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_wallet_address', 'balance_changed', $this->smallInteger(3)->comment("金额变动"));
        $this->addColumn('iec_wallet_address', 'private_key', $this->string(255)->comment("私钥"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_wallet_address', 'balance_changed');
        $this->dropColumn('iec_wallet_address', 'private_key');
    }
}
