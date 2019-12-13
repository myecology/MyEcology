<?php

use yii\db\Migration;

/**
 * Class m180917_071151_alter_bind_user_column_from_iec_gift_money_table
 */
class m180917_071151_alter_bind_taker_column_from_iec_gift_money_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('iec_gift_money', 'bind_taker');
        $this->addColumn('iec_gift_money', 'bind_taker', $this->string(255)->notNull()->comment('绑定接收对象'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180917_071151_alter_bind_user_column_from_iec_gift_money_table cannot be reverted.\n";
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180917_071151_alter_bind_user_column_from_iec_gift_money_table cannot be reverted.\n";

        return false;
    }
    */
}
