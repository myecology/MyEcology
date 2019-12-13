<?php

use yii\db\Migration;

/**
 * Handles adding model to table `iec_user_address`.
 */
class m180912_163355_add_model_column_to_iec_user_address_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('iec_user_address', 'model', $this->string(32)->comment("币种模型"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('iec_user_address', 'model');
    }
}
