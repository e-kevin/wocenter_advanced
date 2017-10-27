<?php
use wocenter\console\Migration;

class m171020_023208_create_table_theme extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%viMJHk_theme}}', [
            'id' => $this->string(64)->notNull()->comment('扩展ID'),
            'app' => $this->char(15)->notNull()->comment('所属应用'),
            'name' => $this->char(15)->notNull()->comment('主题名称'),
            'is_system' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('系统扩展 0:否 1:是'),
            'status' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('状态 0:禁用 1:启用'),
        ], $this->tableOptions . $this->buildTableComment('系统主题扩展'));
    
        $this->addPrimaryKey('unique', '{{%viMJHk_theme}}', 'id');
    
        $this->batchInsert('{{%viMJHk_theme}}', ['id', 'app', 'name', 'is_system', 'status'], [
            ['36437752f8d5425e56c258d05d1e4baa', 'backend', 'adminlte', 1, 1],
            ['8e037c5adfc507dc98726a801af1aabd', 'frontend', 'basic', 1, 1],
        ]);

        $this->createIndex('idx-viMJHk_theme-app', '{{%viMJHk_theme}}', 'app');
    }

    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_theme}}');
    }

}
