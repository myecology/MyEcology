<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_eth_gas`.
 */
class m181218_070721_create_iec_eth_gas_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_eth_gas', [
            'id' => $this->primaryKey(),
            'tx_hash' => $this->string(255)->comment('交易哈希'),
            'created_at' => $this->integer()->comment('创建时间'),
            'type' => $this->integer()->defaultValue(0)->comment('类别'),
            'business_sn' => $this->string(128)->comment('业务单号'),
            'amount' => $this->money(20,8)->comment('金额'),
            'gas_used' => $this->string(128)->comment('实际使用燃油数'),
            'gas_price' => $this->string(128)->comment('GAS单价'),
            'desc' => $this->string(255)->comment('描述'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_eth_gas');
    }
}
