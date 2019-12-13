<?php

use yii\db\Migration;

/**
 * Handles adding remark to table `iec_withdraw`.
 */
class m180913_085722_add_remark_column_to_iec_withdraw_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_withdraw', 'remark', $this->text()->comment("备注"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_withdraw', 'remark');
    }
}
