<?php

use yii\db\Migration;

/**
 * Handles adding fee_symbol to table `iec_withdraw`.
 */
class m180914_010027_add_fee_symbol_column_to_iec_withdraw_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_withdraw', 'fee_symbol', $this->string(32)->comment('手续费币种'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_withdraw', 'fee_symbol');
    }
}
