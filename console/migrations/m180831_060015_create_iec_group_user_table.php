<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_group_user`.
 */
class m180831_060015_create_iec_group_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_group_user', [
            'id' => $this->primaryKey(),
            'groupid' => $this->string()->notNull()->comment('群ID'),
            'userid' => $this->string()->notNull()->comment('用户userid'),
            'nickname' => $this->string()->notNull()->comment('用户昵称'),
            'permission' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('权限'),
            'is_ban' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('是否禁言'),
            'msg' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('消息免打扰'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(10)->comment('状态'),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_group_user');
    }
}
