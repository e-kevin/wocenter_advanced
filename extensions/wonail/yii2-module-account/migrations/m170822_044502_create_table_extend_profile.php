<?php
use wocenter\console\controllers\wocenter\Migration;

class m170822_044502_create_table_extend_profile extends Migration
{
    
    public function safeUp()
    {
        $this->createTable('{{%viMJHk_extend_profile}}', [
            'id' => $this->primaryKey(11)->unsigned()->comment('ID'),
            'profile_name' => $this->string(25)->unique()->notNull()->comment('名称'),
            'status' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('状态'),
            'visible' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('是否公开'),
            'sort_order' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue(0)->comment('排序'),
            'created_at' => $this->integer()->unsigned()->notNull()->comment('创建时间'),
        ], $this->tableOptions . $this->buildTableComment('扩展档案列表'));
        
        $this->batchInsert('{{%viMJHk_extend_profile}}', ['id', 'profile_name', 'status', 'visible', 'sort_order', 'created_at'], [
            [1, '个人档案', 1, 1, 0, 1469541550],
        ]);
    }
    
    public function safeDown()
    {
        $this->setForeignKeyCheck();

        $this->dropTable('{{%viMJHk_extend_profile}}');

        $this->setForeignKeyCheck(true);
    }
    
}
