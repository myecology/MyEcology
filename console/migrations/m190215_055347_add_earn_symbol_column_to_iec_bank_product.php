<?php

use yii\db\Migration;

/**
 * Class m190215_055347_add_earn_symbol_column_to_iec_bank_product
 */
class m190215_055347_add_earn_symbol_column_to_iec_bank_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_bank_product', 'earn_symbol', $this->string(255)->comment("收益币种"));
        $this->addColumn('iec_bank_product', 'earn_currency_id', $this->integer(11)->comment("收益币种标识id"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190215_055347_add_earn_symbol_column_to_iec_bank_product cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190215_055347_add_earn_symbol_column_to_iec_bank_product cannot be reverted.\n";

        return false;
    }
    */
}
