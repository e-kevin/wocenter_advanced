<?php
use wocenter\console\controllers\wocenter\Migration;

class m170822_065758_create_table_module extends Migration
{
    
    public function safeUp()
    {
        $this->createTable('{{%viMJHk_module}}', [
            'id' => $this->string(64)->notNull()->comment('扩展ID'),
            'app' => $this->char(15)->notNull()->comment('所属应用'),
            'module_id' => $this->char(15)->notNull()->comment('模块ID'),
            'is_system' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('系统扩展 0:否 1:是'),
            'status' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('状态 0:禁用 1:启用'),
            'run' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('运行版本 0:扩展 1:开发者'),
        ], $this->tableOptions . $this->buildTableComment('系统模块'));
    
        $this->addPrimaryKey('unique', '{{%viMJHk_module}}', 'id');
    
        $this->batchInsert('{{%viMJHk_module}}', ['id', 'module_id', 'app', 'is_system', 'status'], [
            ['7a1ec3b7d7f09d4a3b4aa24f5d2e3f09', 'account', 'backend', 1, 1],
            ['3bf31bf55b42cfc89bf3ac23b982ca1c', 'action', 'backend', 1, 1],
            ['27af59cb66f08b1f8cf32250374e8eef', 'data', 'backend', 1, 1],
            ['9468d62547c0b1f38d017899535619dc', 'log', 'backend', 1, 1],
            ['ba63d61937bee15a00ab76d2560570ac', 'menu', 'backend', 1, 1],
            ['30a819a014c032452056366c60ec558c', 'extension', 'backend', 1, 1],
            ['936c77bf181aecc8a9a4d30477ea870a', 'notification', 'backend', 1, 1],
            ['8fc0608eff9135c21f2520b26f71aa9d', 'operate', 'backend', 1, 1],
            ['37e2ea97a7e5912b3f8b2864f4334d73', 'passport', 'backend', 1, 1],
            ['a35f7b8b646e7a6c0a6b61bb8f960163', 'system', 'backend', 1, 1],
        ]);
    
        $this->createIndex('idx-viMJHk_module-app', '{{%viMJHk_module}}', 'app');
    }
    
    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_module}}');
    }
    
}
