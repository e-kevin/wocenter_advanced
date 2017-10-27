<?php
use wocenter\console\Migration;

class m170822_065929_create_table_user_score_type extends Migration
{
    
    public function safeUp()
    {
        $this->createTable('{{%viMJHk_user_score_type}}', [
            'id' => $this->primaryKey()->unsigned()->comment('ID'),
            'name' => $this->string(50)->unique()->notNull()->comment('名称'),
            'status' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('状态'),
            'unit' => $this->string(20)->notNull()->comment('单位'),
        ], $this->tableOptions . $this->buildTableComment('用户积分类型表'));
        
        $this->batchInsert('{{%viMJHk_user_score_type}}', ['id', 'name', 'status', 'unit'], [
            [1, '积分', 1, '分'],
            [2, '威望', 1, '点'],
            [3, '贡献', 1, '点'],
            [4, '余额', 1, '元'],
        ]);
    }
    
    public function safeDown()
    {
        $this->setForeignKeyCheck();

        $this->dropTable('{{%viMJHk_user_score_type}}');

        $this->setForeignKeyCheck(true);
    }
    
}
