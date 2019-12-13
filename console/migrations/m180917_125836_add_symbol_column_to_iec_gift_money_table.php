<?php

use yii\db\Migration;

/**
 * Handles adding symbol to table `iec_gift_money`.
 */
class m180917_125836_add_symbol_column_to_iec_gift_money_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_gift_money', 'symbol', $this->string(32)->comment("币种标识"));
        $this->addColumn('iec_gift_money_taker', 'symbol', $this->string(32)->comment("币种标识"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_gift_money', 'symbol');
        $this->dropColumn('iec_gift_money_taker', 'symbol');
    }
}
