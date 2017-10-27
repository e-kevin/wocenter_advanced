<?php
use wocenter\console\Migration;

class m170822_065843_create_table_tag_user extends Migration
{
    
    public function safeUp()
    {
        $this->setForeignKeyCheck();
        
        $this->createTable('{{%viMJHk_tag_user}}', [
            'id' => $this->primaryKey(11)->unsigned()->comment('ID'),
            'uid' => $this->integer(11)->unsigned()->notNull()->comment('用户ID'),
            'tag_id' => $this->integer(11)->unsigned()->notNull()->comment('标签id'),
        ], $this->tableOptions . $this->buildTableComment('用户标签关联表'));
        
        $this->addForeignKey('fk-viMJHk_tag_user-uid', '{{%viMJHk_tag_user}}', 'uid', '{{%viMJHk_user}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-viMJHk_tag_user-tag_id', '{{%viMJHk_tag_user}}', 'tag_id', '{{%viMJHk_tag}}', 'id', 'CASCADE');
        
        $this->setForeignKeyCheck(true);
    }
    
    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_tag_user}}');
    }
    
}
