<?php
use wocenter\db\Migration;

class m170925_023208_create_table_module_function extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%viMJHk_module_function}}', [
            'id' => $this->string(64)->notNull()->comment('扩展ID'),
            'extension_name' => $this->char(255)->notNull()->comment('扩展名称'),
            'module_id' => $this->char(15)->notNull()->comment('模块ID'),
            'controller_id' => $this->char(64)->notNull()->comment('控制器ID'),
            'is_system' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('系统扩展 0:否 1:是'),
            'status' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('状态 0:禁用 1:启用'),
        ], $this->tableOptions . $this->buildTableComment('系统功能扩展'));
    
        $this->addPrimaryKey('unique', '{{%viMJHk_module_function}}', 'id');
    
        $this->createIndex('idx-viMJHk_module_function-extension_name', '{{%viMJHk_module_function}}', 'extension_name');
    }

    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_module_function}}');
    }

}
