<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_version`.
 */
class m180929_015640_create_iec_version_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_version', [
            'id' => $this->primaryKey(),
            'type' => $this->tinyInteger()->notNull()->defaultValue(0)->comment('类型'),
            'num' => $this->integer()->notNull()->comment('版本ID'),
            'update' => $this->boolean()->notNull()->defaultValue(false)->comment('是否强制更新'),
            'version' => $this->string()->notNull()->comment('版本号'),
            'size' => $this->integer()->notNull()->comment('大小'),
            'url' => $this->string()->notNull()->comment('下载URL'),
            'content' => $this->text(),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_version');
    }
}
