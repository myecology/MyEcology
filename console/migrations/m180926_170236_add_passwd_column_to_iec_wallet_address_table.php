<?php

use yii\db\Migration;

/**
 * Handles adding passwd to table `iec_wallet_address`.
 */
class m180926_170236_add_passwd_column_to_iec_wallet_address_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_wallet_address', 'passwd', $this->string(64)->comment("密钥"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_wallet_address', 'passwd');
    }
}
