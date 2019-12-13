<?php

use yii\db\Migration;

/**
 * Class m180927_103440_alter_address_column_from_iec_wallet_address_table
 */
class m180927_103440_alter_address_column_from_iec_wallet_address_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('iec_wallet_address', 'address', 'varchar(255) comment "钱包地址"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180927_103440_alter_address_column_from_iec_wallet_address_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180927_103440_alter_address_column_from_iec_wallet_address_table cannot be reverted.\n";

        return false;
    }
    */
}
