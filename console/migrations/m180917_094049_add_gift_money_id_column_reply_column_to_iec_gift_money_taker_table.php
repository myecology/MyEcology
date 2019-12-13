<?php

use yii\db\Migration;

/**
 * Handles adding gift_money_id_column_reply to table `iec_gift_money_taker`.
 */
class m180917_094049_add_gift_money_id_column_reply_column_to_iec_gift_money_taker_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_gift_money_taker', 'gift_money_id', $this->integer()->notNull()->comment("红包ID"));
        $this->addColumn('iec_gift_money_taker', 'reply', $this->string(255)->comment("领取者回复"));
        $this->addColumn('iec_gift_money_taker', 'reply_time', $this->integer()->comment("回复时间"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_gift_money_taker', 'gift_money_id');
        $this->dropColumn('iec_gift_money_taker', 'reply');
        $this->dropColumn('iec_gift_money_taker', 'reply_time');
    }
}
