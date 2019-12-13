<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_poster`.
 */
class m180921_140041_create_iec_poster_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_poster', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull()->comment('海报名称'),
            'url' => $this->string(255)->notNull()->comment('地址'),
            'sort' => $this->smallInteger()->notNull()->defaultValue(0)->comment('排序'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(10)->comment('状态'),
            'created_at' => $this->integer()->notNull(),
            'endtime_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_poster');
    }
}
