<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_start_page`.
 */
class m181029_035452_create_iec_start_page_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_start_page', [
            'id' => $this->primaryKey(),
            'img'=>$this->string()->notNull()->comment('图片'),
            'name'=>$this->string()->notNull()->comment('广告名称'),
            'type' => $this->tinyInteger()->notNull()->defaultValue(1)->comment('类型:1安卓/10 IOS'),
            'sort'=>$this->integer()->notNull()->comment('排序'),
            'status'=>$this->tinyInteger()->notNull()->defaultValue(1)->comment('状态:1启动/10禁用'),
            'time'=>$this->integer()->notNull()->defaultValue(3)->comment('广告时间'),
            'redirecturl'=>$this->string()->comment("广告url"),
            'created_at' => $this->integer()->notNull(),

        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_start_page');
    }
}
