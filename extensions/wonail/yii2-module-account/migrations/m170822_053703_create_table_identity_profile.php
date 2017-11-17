<?php
use wocenter\console\controllers\wocenter\Migration;

class m170822_053703_create_table_identity_profile extends Migration
{
    
    public function safeUp()
    {
        $this->setForeignKeyCheck();
        
        $this->createTable('{{%viMJHk_identity_profile}}', [
            'id' => $this->primaryKey(11)->unsigned()->comment('ID'),
            'identity_id' => $this->integer(11)->unsigned()->notNull()->comment('身份ID'),
            'profile_id' => $this->integer(11)->unsigned()->notNull()->comment('档案ID'),
        ], $this->tableOptions . $this->buildTableComment('身份档案关联表'));
        
        $this->insert('{{%viMJHk_identity_profile}}', ['id' => 1, 'identity_id' => 1, 'profile_id' => 1]);

        $this->createIndex('idx-viMJHk_identity_profile-identity_profile', '{{%viMJHk_identity_profile}}', ['identity_id', 'profile_id'], true);

        $this->addForeignKey('fk-viMJHk_identity_profile-identity_id', '{{%viMJHk_identity_profile}}', 'identity_id', '{{%viMJHk_identity}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-viMJHk_identity_profile-profile_id', '{{%viMJHk_identity_profile}}', 'profile_id', '{{%viMJHk_extend_profile}}', 'id', 'CASCADE');
        
        $this->setForeignKeyCheck(true);
    }
    
    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_identity_profile}}');
    }
    
}
