<?php

use yii\db\Migration;

/**
 * Class m180914_151000_alter_symbol_column_from_iec_wallet_log_table
 */
class m180914_151000_alter_symbol_column_from_iec_wallet_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('iec_wallet_log', 'symbol', 'varchar(32) comment "币种标识"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('iec_wallet_log', 'symbol', 'int comment "币种标识"');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180914_151000_alter_symbol_column_from_iec_wallet_log_table cannot be reverted.\n";

        return false;
    }
    */
}
