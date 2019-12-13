<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_shop_type`.
 */
class m190225_061803_create_iec_shop_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_shop_type', [
            'id' => $this->primaryKey(),
            'title' => $this->string(32)->defaultValue(null)->comment('标题'),
            'icon' => $this->string(255)->defaultValue(null)->comment('图标'),
            'created_at'  => $this->integer(11)->defaultValue(0)->comment('添加时间'),
            'updated_at' => $this->integer(11)->defaultValue(0)->comment('更新时间'),
        ],'COMMENT = 商家类型 ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_shop_type');
    }
}
