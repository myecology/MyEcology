<?php

use yii\db\Migration;

/**
 * Handles adding weight to table `iec_wallet`.
 */
class m181019_174035_add_weight_column_to_iec_wallet_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_wallet', 'weight', $this->integer()->defaultValue(1)->comment('排序权重'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_wallet', 'weight');
    }
}
