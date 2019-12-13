<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_eth_transaction`.
 */
class m180927_135810_create_iec_eth_transaction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_eth_transaction', [
            'id' => $this->primaryKey(),
            'block_number' => $this->integer()->comment('区块编号'),
            'from_address' => $this->string(255)->comment('转出地址'),
            'to_address' => $this->string(255)->comment('接收地址'),
            'created_at' => $this->integer()->comment('交易时间'),
            'transaction_hash' => $this->string(255)->comment('交易哈希'),

            "nonce" => $this->string(64)->defaultValue(0)->comment('同交易生效序列'),
            "block_hash" => $this->string(255)->comment('块哈希'),
            "transaction_index" => $this->integer()->comment('交易序列'),
            "value" => $this->string(64)->defaultValue(0)->comment('金额'),
            "gas" => $this->string(64)->comment('提交GAS'),
            "gas_price" => $this->string(64)->comment('GAS单价'),
            "is_error" => $this->integer()->comment('是否错误'),
            "txreceipt_status" => $this->integer()->comment('交易接收状态'),
            "input" => $this->string(512)->comment('交易附加信息'),
            "contract_address" => $this->string(255)->comment('合约地址'),
            "cumulative_gas_used" => $this->string(64)->comment('累计GAS开销'),
            "gas_used" => $this->string(64)->comment('GAS开销'),
            "confirmations" => $this->integer()->defaultValue(0)->comment('确认次数'),

            "contract_to" => $this->string(255)->comment('合约收款地址'),
            "contract_value" => $this->string(64)->comment('合约金额'),

            "status" => $this->tinyInteger()->defaultValue(0)->comment('状态'),
            "type" => $this->tinyInteger()->defaultValue(0)->comment('类型'),
        ]);

        $this->createIndex('index_from', 'iec_eth_transaction', ['from_address']);
        $this->createIndex('index_to', 'iec_eth_transaction', ['to_address']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_eth_transaction');
    }
}
