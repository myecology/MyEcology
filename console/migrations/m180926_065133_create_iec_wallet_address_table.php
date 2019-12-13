<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_wallet_address`.
 */
class m180926_065133_create_iec_wallet_address_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_wallet_address', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->comment('用户ID'),
            'currency_id' => $this->integer()->notNull()->comment('币种ID'),
            'type' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('类型'),
            'symbol' => $this->string(32)->notNull()->comment('英文缩写'),
            'mode' => $this->string(32)->notNull()->comment('模型'),
            'amount' => $this->money(20,8)->notNull()->defaultValue(0)->comment('数量'),
            'address' => $this->string(255)->notNull()->comment('钱包地址'),
            'created_at' => $this->integer()->comment('创建时间'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_wallet_address');
    }
}
