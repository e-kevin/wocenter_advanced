<?php
use wocenter\console\Migration;

class m170925_023208_create_table_module_function extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%viMJHk_module_function}}', [
            'id' => $this->string(64)->notNull()->comment('扩展ID'),
            'app' => $this->char(15)->notNull()->comment('所属应用'),
            'module_id' => $this->char(15)->notNull()->comment('模块ID'),
            'controller_id' => $this->char(64)->notNull()->comment('控制器ID'),
            'is_system' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('系统扩展 0:否 1:是'),
            'status' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('状态 0:禁用 1:启用'),
        ], $this->tableOptions . $this->buildTableComment('系统功能扩展'));
    
        $this->addPrimaryKey('unique', '{{%viMJHk_module_function}}', 'id');
    
        $this->batchInsert('{{%viMJHk_module_function}}', ['id', 'app', 'module_id', 'controller_id', 'is_system', 'status'], [
            ['03eedf833c61cf1f35512e4990046ebc', 'backend', '', 'site', 1, 1],
            ['6d3a8837476a027562c30694aba27917', 'frontend', '', 'site', 1, 1],
        ]);

        $this->createIndex('idx-viMJHk_module_function-app', '{{%viMJHk_module_function}}', 'app');
    }

    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_module_function}}');
    }

}
