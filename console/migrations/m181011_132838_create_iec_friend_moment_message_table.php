<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_friend_moment_message`.
 */
class m181011_132838_create_iec_friend_moment_message_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_friend_moment_message', [
            'id' => $this->primaryKey(),
            'userid' => $this->string()->notNull()->comment('userid'),
            'type' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('类型'),
            'momentid' => $this->integer()->notNull()->comment('朋友圈ID'),
            'moment_type' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('朋友圈类型'),
            'in_userid' => $this->string()->notNull()->comment('用户userid'),
            'to_userid' => $this->string()->notNull()->comment('对方userid'),
            'is_reply' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('是否回复'),
            'content' => $this->string()->notNull()->comment('内容'),
            'moment_content' => $this->string()->notNull()->comment('朋友圈内容'),
            'created_at' => $this->integer()->notNull()->comment('创建时间'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_friend_moment_message');
    }
}
