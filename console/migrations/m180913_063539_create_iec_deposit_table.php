<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_deposit`.
 */
class m180913_063539_create_iec_deposit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_deposit', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->comment('用户ID'),
            'wallet_id' => $this->integer()->comment('钱包ID'),

            'symbol' => $this->string(32)->notNull()->comment('币种标识'),
            'amount' => $this->money(20, 8)->defaultValue(0)->comment('金额'),

            'created_at' => $this->integer()->comment('创建时间'),
            'updated_at' => $this->integer()->comment('最后修改时间'),
            'status' => $this->smallInteger(3)->defaultValue(10)->comment('状态'),

            'source' => $this->string(128)->comment('来源标识'),
        ], 'comment "充值记录"');

        $this->createTable('iec_withdraw', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->comment('用户ID'),
            'wallet_id' => $this->integer()->comment('钱包ID'),
            'address_id' => $this->integer()->notNull()->comment('钱包地址ID'),
            'address' => $this->string(128)->notNull()->comment('钱包地址'),

            'symbol' => $this->string(32)->notNull()->comment('币种标识'),
            'amount' => $this->money(20, 8)->defaultValue(0)->comment('金额'),
            'fee' => $this->money(20, 8)->defaultValue(0)->comment(''),

            'created_at' => $this->integer()->comment('创建时间'),
            'updated_at' => $this->integer()->comment('最后修改时间'),
            'status' => $this->smallInteger(3)->defaultValue(10)->comment('状态'),

            'checker_id' => $this->Integer()->comment('审核人ID'),
            'check_time' => $this->Integer()->comment('审核时间'),

            'source' => $this->string(128)->comment('来源标识'),
        ], 'comment "充值记录"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_deposit');
        $this->dropTable('iec_withdraw');
    }
}
