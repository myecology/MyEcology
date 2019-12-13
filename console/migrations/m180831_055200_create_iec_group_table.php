<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_group`.
 */
class m180831_055200_create_iec_group_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_group', [
            'id' => $this->primaryKey(),
            'groupid' => $this->string()->notNull()->comment('群ID'),
            'createid' => $this->string()->notNull()->comment('创建者ID'),
            'name' => $this->string()->notNull()->comment('群名称'),
            'groupimgurl' => $this->string()->notNull()->comment('群头像'),
            'sort' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('排名'),
            'hot' => $this->smallInteger()->notNull()->defaultValue(0)->comment('热度'),
            'nums' => $this->smallInteger()->notNull()->defaultValue(1)->comment('群人数'),
            'is_ban' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('是否禁言'),
            'is_verify' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('is verify'),
            'is_hot_show' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('is show'),
            'is_pull' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('is pull'),
            'max_nums' => $this->smallInteger()->notNull()->defaultValue(3000)->comment('最大人数'),
            'description' => $this->string()->comment('个性说明'),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_group');
    }
}
