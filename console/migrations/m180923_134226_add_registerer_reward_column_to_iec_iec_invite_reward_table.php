<?php

use yii\db\Migration;

/**
 * Handles adding registerer_reward to table `iec_iec_invite_reward`.
 */
class m180923_134226_add_registerer_reward_column_to_iec_iec_invite_reward_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_invite_reward', 'registerer_reward', $this->money(20,8)->comment("注册人得奖"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_iec_invite_reward', 'registerer_reward');
    }
}
