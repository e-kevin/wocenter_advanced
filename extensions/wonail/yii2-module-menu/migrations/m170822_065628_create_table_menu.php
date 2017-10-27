<?php
use wocenter\console\Migration;

class m170822_065628_create_table_menu extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%viMJHk_menu}}', [
            'id' => $this->primaryKey()->unsigned()->comment('ID'),
            'parent_id' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('父类ID'),
            'category_id' => $this->string(64)->notNull()->comment('分类'),
            'name' => $this->string(64)->notNull()->comment('名称'),
            'alias_name' => $this->string(64)->notNull()->comment('菜单别名'),
            'url' => $this->string(512)->notNull()->comment('菜单路由地址'),
            'params' => $this->string(200)->notNull()->comment('URL参数'),
            'target' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('打开方式0:_self 1:_blank'),
            'description' => $this->string(512)->notNull()->comment('描述'),
            'status' => $this->boolean()->notNull()->unsigned()->defaultValue(1)->comment('状态'),
            'is_dev' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('是否开发可见'),
            'sort_order' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue(0)->comment('排序'),
            'icon_html' => $this->string(30)->notNull()->comment('图标'),
            'created_type' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('创建方式 0-用户(手动) 1-模块(自动)'),
            'modularity' => $this->string(20)->notNull()->comment('模块'),
            'show_on_menu' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('显示菜单 默认不显示'),
        ], $this->tableOptions . $this->buildTableComment('菜单表') . ' AUTO_INCREMENT=1000');

        $this->createIndex('idx-viMJHk_menu-category_id', '{{%viMJHk_menu}}', 'category_id');
    }

    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_menu}}');
    }

}
