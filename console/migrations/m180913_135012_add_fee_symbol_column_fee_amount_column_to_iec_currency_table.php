<?php

use yii\db\Migration;

/**
 * Handles adding fee_symbol_column_fee_amount to table `iec_currency`.
 */
class m180913_135012_add_fee_symbol_column_fee_amount_column_to_iec_currency_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_currency', 'fee_symbol', $this->string(32)->comment('手续费币种'));
        $this->addColumn('iec_currency', 'fee_withdraw_amount', $this->money(20, 8)->defaultValue(0)->comment('提现手续费金额'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_currency', 'fee_symbol');
        $this->dropColumn('iec_currency', 'fee_withdraw_amount');
    }
}
