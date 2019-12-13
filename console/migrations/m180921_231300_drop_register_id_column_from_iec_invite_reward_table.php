<?php

use yii\db\Migration;

/**
 * Handles dropping register_id from table `iec_invite_reward`.
 */
class m180921_231300_drop_register_id_column_from_iec_invite_reward_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('iec_invite_reward', 'registerer_id');
        $this->addColumn('iec_invite_reward', 'user_id_rewarded', $this->integer()->comment('收益人'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
