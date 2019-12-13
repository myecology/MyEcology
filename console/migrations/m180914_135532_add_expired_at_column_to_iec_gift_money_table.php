<?php

use yii\db\Migration;

/**
 * Handles adding expired_at to table `iec_gift_money`.
 */
class m180914_135532_add_expired_at_column_to_iec_gift_money_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_gift_money', 'expired_at', $this->integer()->comment('过期时间'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_gift_money', 'expired_at');
    }
}
