<?php
use wocenter\console\controllers\wocenter\Migration;

class m170926_045127_create_table_backend_user extends Migration
{

    public function safeUp()
    {
        $this->setForeignKeyCheck();

        $this->createTable('{{%viMJHk_backend_user}}', [
            'id' => $this->primaryKey()->unsigned()->comment('ID'),
            'user_id' => $this->integer()->unsigned()->unique()->notNull()->comment('用户ID'),
            'status' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('管理员状态'),
        ], $this->tableOptions . $this->buildTableComment('后台用户表'));

        $this->insert('{{%viMJHk_backend_user}}', ['id' => 1, 'user_id' => 1, 'status' => 1]);

        $this->createIndex('idx-viMJHk_backend_user-status', '{{%viMJHk_backend_user}}', 'status');

        $this->addForeignKey('fk-viMJHk_backend_user-user_id', '{{%viMJHk_backend_user}}', 'user_id', '{{%viMJHk_user}}', 'id', 'CASCADE');

        $this->setForeignKeyCheck(true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_backend_user}}');
    }

}
