<?php

use yii\db\Migration;

/**
 * Handles adding pulled_at to table `iec_wallet_address`.
 */
class m181205_124502_add_pulled_at_column_to_iec_wallet_address_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_wallet_address', 'pulled_at', $this->integer()->comment("更新时间"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_wallet_address', 'pulled_at');
    }
}
