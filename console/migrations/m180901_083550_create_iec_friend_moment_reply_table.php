<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_friend_moment_reply`.
 */
class m180901_083550_create_iec_friend_moment_reply_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_friend_moment_reply', [
            'id' => $this->primaryKey(),
            'momentid' => $this->integer()->notNull()->comment('ID'),
            'in_userid' => $this->string()->notNull()->comment('回复人userid'),
            'to_userid' => $this->string()->notNull()->comment('对象人userid'),
            'is_reply' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('是否回复'),
            'content' => $this->string()->notNull()->comment('回复内容'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(10)->comment('状态'),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_friend_moment_reply');
    }
}
