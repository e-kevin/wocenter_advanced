<?php
use wocenter\console\controllers\wocenter\Migration;

class m170822_065746_create_table_menu_category extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%viMJHk_menu_category}}', [
            'id' => $this->string(64)->notNull()->comment('ID'),
            'name' => $this->string(64)->notNull()->comment('菜单名'),
            'description' => $this->string(512)->notNull()->comment('菜单描述'),
            'PRIMARY KEY (id)',
        ], $this->tableOptions . $this->buildTableComment('菜单分类表'));
        
        $this->batchInsert('{{%viMJHk_menu_category}}', ['id', 'name', 'description'], [
            ['backend', '后台菜单', ''],
            ['footer', '底部菜单', ''],
            ['main', '主导航', ''],
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_menu_category}}');
    }

}
