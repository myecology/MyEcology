<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_image`.
 */
class m180831_084026_create_iec_image_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_image', [
            'id' => $this->primaryKey(),
            'type' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('类型'),
            'userid' => $this->string()->notNull()->comment('用户userid'),
            'origin' => $this->integer()->notNull()->comment('来源ID'),
            'url' => $this->string()->notNull()->comment('地址'),
            'thumbnail' => $this->string()->notNull()->comment('缩略图'),
            'width' => $this->integer()->notNull()->defaultValue(0)->comment('宽度'),
            'height' => $this->integer()->notNull()->defaultValue(0)->comment('高度'),
            'status' => $this->tinyInteger()->notNull()->defaultValue(10)->comment('状态'),
            'created_at' => $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_image');
    }
}
