<?php

use yii\db\Migration;

/**
 * Handles adding weight to table `iec_currency`.
 */
class m180912_112703_add_weight_column_to_iec_currency_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_currency', 'weight', $this->integer()->defaultValue(1)->comment("排序权重"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_currency', 'weight');
    }
}
