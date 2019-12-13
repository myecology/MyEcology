<?php

use yii\db\Migration;

/**
 * Handles adding bind_taker to table `iec_gift_money`.
 */
class m180917_032925_add_bind_taker_column_to_iec_gift_money_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_gift_money', 'bind_taker', $this->integer()->defaultValue(0)->comment("个人红包绑定领取人"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_gift_money', 'bind_taker');
    }
}
