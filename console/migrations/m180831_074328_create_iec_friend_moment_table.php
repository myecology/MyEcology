<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_friend_circle`.
 */
class m180831_074328_create_iec_friend_moment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_friend_moment', [
            'id' => $this->primaryKey(),
            'userid' => $this->string()->notNull()->comment('userid'),
            'type' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('类型'),
            'content' => $this->text()->comment('正文内容'),
            'linkid' => $this->integer()->notNull()->defaultValue(0)->comment('链接ID'),
            'address' => $this->string()->notNull()->comment('地址'),
            'sort' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('排名'),
            'hot' => $this->integer()->notNull()->defaultValue(0)->comment('热度'),
            'like' => $this->integer()->notNull()->defaultValue(0)->comment('点赞数'),
            'reply' => $this->integer()->notNull()->defaultValue(0)->comment('回复数'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(10)->comment('状态'),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_friend_moment');
    }
}
