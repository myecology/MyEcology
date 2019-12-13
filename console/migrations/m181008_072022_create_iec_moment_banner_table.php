<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_moment_banner`.
 */
class m181008_072022_create_iec_moment_banner_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_moment_banner', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull()->comment('标题'),
            'url' => $this->string()->notNull()->comment('广告地址'),
            'link' => $this->string()->notNull()->comment('链接地址'),
            'sort' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('排序'),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->integer()->notNull()->comment('创建时间'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_moment_banner');
    }
}
