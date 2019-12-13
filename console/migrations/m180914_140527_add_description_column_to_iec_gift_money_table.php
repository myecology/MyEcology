<?php

use yii\db\Migration;

/**
 * Handles adding description to table `iec_gift_money`.
 */
class m180914_140527_add_description_column_to_iec_gift_money_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_gift_money', 'description', $this->string(255)->comment('红包祝福语'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_gift_money', 'description');
    }
}
