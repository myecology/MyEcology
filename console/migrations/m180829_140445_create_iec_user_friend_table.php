<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_user_friend`.
 */
class m180829_140445_create_iec_user_friend_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_user_friend', [
            'id' => $this->primaryKey(),
            'in_userid' => $this->string()->notNull()->comment('用户userid'),
            'to_userid' => $this->string()->notNull()->comment('好友userid'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0),
            'remark' => $this->string()->comment('备注'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_user_friend');
    }
}
