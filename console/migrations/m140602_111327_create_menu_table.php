<?php

use mdm\admin\components\Configs;

/**
 * Migration table of table_menu
 * 
 * @author Misbahul D Munir <misbahuldmunir@gmail.com>
 * @since 1.0
 */
class m140602_111327_create_menu_table extends \yii\db\Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $menuTable = Configs::instance()->menuTable;
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable($menuTable, [
            'id' => $this->primaryKey(),
            'name' => $this->string(128)->notNull(),
            'parent' => $this->integer(),
            'route' => $this->string(),
            'order' => $this->integer(),
            'data' => $this->binary(),
            "FOREIGN KEY ([[parent]]) REFERENCES {$menuTable}([[id]]) ON DELETE SET NULL ON UPDATE CASCADE",
        ], $tableOptions);

        //插入一条数据
        $this->insert($menuTable, ['id' => 1, 'name' => 'System settings', 'order' => 100, 'data' => '{"icon" : "gear"}']);
        $this->insert($menuTable, ['id' => 2, 'name' => 'Permission settings', 'parent' => 1, 'order' => 100, 'data' => '{"icon" : "gear"}']);        
        $this->insert($menuTable, ['id' => 3, 'name' => 'Route', 'parent' => 2, 'route' => '/admin/route/index', 'order' => 0]);
        $this->insert($menuTable, ['id' => 4, 'name' => 'Permission', 'parent' => 2, 'route' => '/admin/permission/index', 'order' => 1]);
        $this->insert($menuTable, ['id' => 5, 'name' => 'Role', 'parent' => 2, 'route' => '/admin/role/index', 'order' => 2]);
        $this->insert($menuTable, ['id' => 6, 'name' => 'Assignment', 'parent' => 2, 'route' => '/admin/assignment/index', 'order' => 3]);
        $this->insert($menuTable, ['id' => 7, 'name' => 'Menu', 'parent' => 1, 'route' => '/admin/menu/index', 'order' => 99, 'data' => '{"icon" : "navicon"}']);
        $this->insert($menuTable, ['id' => 8, 'name' => 'User Management', 'order' => 99, 'data' => '{"icon" : "users"}']);
        $this->insert($menuTable, ['id' => 9, 'name' => 'User List', 'parent' => 8, 'route' => '/admin/user/index', 'order' => 0, 'data' => '{"icon" : "user"}']);
        $this->insert($menuTable, ['id' => 10, 'name' => 'New User', 'parent' => 8, 'route' => '/admin/user/signup', 'order' => 1, 'data' => '{"icon" : "user-plus"}']);
        $this->insert($menuTable, ['id' => 11, 'name' => 'System Log', 'parent' => 1, 'route' => '/log/index', 'order' => 98, 'data' => '{"icon" : "bug"}']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(Configs::instance()->menuTable);
    }
}
