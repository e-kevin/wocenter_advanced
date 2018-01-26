<?php

use wocenter\db\Migration;

class m170822_065758_create_table_module extends Migration
{
    
    public function safeUp()
    {
        $this->createTable('{{%viMJHk_module}}', [
            'id' => $this->string(64)->notNull()->comment('扩展ID'),
            'extension_name' => $this->char(255)->notNull()->comment('扩展名称'),
            'module_id' => $this->char(15)->notNull()->comment('模块ID'),
            'is_system' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('系统扩展 0:否 1:是'),
            'status' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('状态 0:禁用 1:启用'),
            'run' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('运行模式 0:系统扩展 1:开发者扩展'),
        ], $this->tableOptions . $this->buildTableComment('系统模块'));
        
        $this->addPrimaryKey('unique', '{{%viMJHk_module}}', 'id');
        
        $this->createIndex('idx-viMJHk_module-extension_name', '{{%viMJHk_module}}', 'extension_name');
    }
    
    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_module}}');
    }
    
}
