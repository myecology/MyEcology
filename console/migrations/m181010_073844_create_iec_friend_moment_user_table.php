<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_friend_moment_user`.
 */
class m181010_073844_create_iec_friend_moment_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_friend_moment_user', [
            'id' => $this->primaryKey(),
            'userid' => $this->string()->notNull()->comment('自己userid'),
            'moment_id' => $this->integer()->notNull()->comment('朋友圈ID'),
            'created_at' => $this->integer()->notNull()->comment('创建时间'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_friend_moment_user');
    }
}
