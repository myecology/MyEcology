<?php

use yii\db\Migration;

/**
 * Handles adding taken_at to table `iec_gift_money_taker`.
 */
class m181105_145726_add_taken_at_column_to_iec_gift_money_taker_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_gift_money_taker', 'taken_at', $this->integer()->comment("领取时间"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_gift_money_taker', 'taken_at');
    }
}
