<?php

use yii\db\Migration;

/**
 * Handles adding prize_grand_grand_inviter to table `iec_invite_pool`.
 */
class m181120_064929_add_prize_grand_grand_inviter_column_to_iec_invite_pool_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_invite_pool', 'prize_grand_grand_inviter', $this->integer()->comment("爷级邀请人比重"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_invite_pool', 'prize_grand_grand_inviter');
    }
}
