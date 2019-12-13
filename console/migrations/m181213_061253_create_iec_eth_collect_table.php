<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_eth_collect`.
 */
class m181213_061253_create_iec_eth_collect_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_wallet_address', 'lock_collect', $this->smallInteger(3)->defaultValue(0)->comment("归集锁"));
//        $this->batchInsert('iec_currency_param',
//            ['currency_id', 'symbol', 'key', 'value',],
//            [
//                [5, 'IEC', 'collect_switch', '1'],
//                [5, 'IEC', 'collect_gas', '0.0003'],
//                [5, 'IEC', 'collect_threshold', '10000'],
//                [5, 'IEC', 'cold_threshold', '60000'],
//            ]);

        $this->createTable('iec_eth_collect', [
            'id' => $this->primaryKey(),
            'symbol' => $this->string(32)->notNull()->comment('币种标识'),
            'user_id' => $this->integer()->comment('用户ID'),
            'from_address' => $this->string(255)->notNull()->comment('钱包地址'),
            'created_at' => $this->integer()->comment('创建时间'),
            'status' => $this->smallInteger(3)->defaultValue(0)->comment('任务状态'),

            'gas_cost' => $this->money(20, 8)->defaultValue(0)->comment('归集GAS'),
            'gas_amount' => $this->money(20, 8)->defaultValue(0)->comment('归集金额'),
            'gas_time' => $this->integer()->comment('归集提交时间'),
            'gas_tx_hash' => $this->string(255)->comment('归集交易哈希'),
            'gas_tx_status' => $this->smallInteger(3)->defaultValue(0)->comment('归集交易状态'),

            'collect_amount' => $this->money(20, 8)->defaultValue(0)->comment('归集金额'),
            'collect_time' => $this->integer()->comment('归集提交时间'),
            'collect_tx_hash' => $this->string(255)->comment('归集交易哈希'),
            'collect_tx_status' => $this->smallInteger(3)->defaultValue(0)->comment('归集交易状态'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_wallet_address', 'lock_collect');
        $this->delete('iec_currency_param', 'currency_id=5 AND symbol="IEC" AND `key` in ("collect_gas", "collect_threshold", "cold_threshold", "collect_switch")');
        $this->dropTable('iec_eth_collect');
    }
}
