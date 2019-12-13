<?php

use yii\db\Migration;

/**
 * Handles adding icon to table `currency`.
 */
class m180912_082450_add_icon_column_to_currency_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_currency', 'icon', $this->string(255)->comment("ICON"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_currency', 'icon');
    }
}
