<?php

use yii\db\Migration;

/**
 * Handles the creation of table `iec_setting`.
 */
class m181013_011939_create_iec_setting_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('iec_setting', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->comment('昵称'),
            'key' => $this->string()->notNull()->comment('键名'),
            'value' => $this->text()->comment('值'),
            'group' => $this->string()->notNull()->comment('分组'),
        ]);

        $this->insert('iec_setting', ['name' => '启动页', 'key' => 'init_page', 'value' => '', 'group' => 'iec']);
        $this->insert('iec_setting', ['name' => '钱包协议', 'key' => 'protocol_wallet', 'value' => '', 'group' => 'iec']);
        $this->insert('iec_setting', ['name' => '服务协议', 'key' => 'protocol_license', 'value' => '', 'group' => 'iec']);
        $this->insert('iec_setting', ['name' => '隐私协议', 'key' => 'protocol_privacy', 'value' => '', 'group' => 'iec']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('iec_setting');
    }
}
