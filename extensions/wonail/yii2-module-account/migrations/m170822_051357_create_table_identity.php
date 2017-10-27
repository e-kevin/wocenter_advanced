<?php
use wocenter\console\Migration;

class m170822_051357_create_table_identity extends Migration
{
    
    public function safeUp()
    {
        $this->setForeignKeyCheck();

        $this->createTable('{{%viMJHk_identity}}', [
            'id' => $this->primaryKey(11)->unsigned()->comment('ID'),
            'name' => $this->string(25)->unique()->notNull(),
            'title' => $this->string(25)->notNull(),
            'identity_group' => $this->integer(11)->unsigned()->unique()->null()->comment('身份分组'),
            'description' => $this->string(500)->notNull()->comment('描述'),
            'is_invite' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('开启邀请注册'),
            'is_audit' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('开启审核'),
            'sort_order' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue(0)->comment('排序'),
            'status' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('状态'),
            'created_at' => $this->integer()->unsigned()->notNull()->comment('创建时间'),
            'updated_at' => $this->integer()->unsigned()->notNull()->comment('更新时间'),
        ], $this->tableOptions . $this->buildTableComment('身份列表'));
        
        $this->insert('{{%viMJHk_identity}}', [
            'id' => 1, 'name' => 'default', 'title' => '普通用户', 'identity_group' => NULL, 'description' => '普通用户',
            'is_invite' => 0, 'is_audit' => 0, 'sort_order' => 0, 'status' => 1, 'created_at' => 1457766196, 'updated_at' => 1500023198,
        ]);
        
        $this->addForeignKey('fk-viMJHk_identity-identity_group', '{{%viMJHk_identity}}', 'identity_group', '{{%viMJHk_identity_group}}', 'id', 'SET NULL');

        $this->setForeignKeyCheck(true);
    }
    
    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_identity}}');
    }
    
}
