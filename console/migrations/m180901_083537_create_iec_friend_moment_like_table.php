<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_friend_moment_like`.
 */
class m180901_083537_create_iec_friend_moment_like_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_friend_moment_like', [
            'id' => $this->primaryKey(),
            'momentid' => $this->integer()->notNull()->comment('ID'),
            'userid' => $this->string()->notNull()->comment('userid'),
            'type' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('类型'),
            'amount' => $this->money(20,8)->notNull()->defaultValue(0)->comment('币赞数量'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(10)->comment('状态'),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_friend_moment_like');
    }
}
