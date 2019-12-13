<?php

use yii\db\Migration;

/**
 * Handles adding registerer_id to table `iec_invite_reward`.
 */
class m180923_130120_add_registerer_id_column_to_iec_invite_reward_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_invite_reward', 'registerer_id', $this->integer()->comment("注册人ID"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_invite_reward', 'registerer_id');
    }
}
