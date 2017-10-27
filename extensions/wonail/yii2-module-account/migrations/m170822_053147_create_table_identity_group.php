<?php
use wocenter\console\Migration;

class m170822_053147_create_table_identity_group extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%viMJHk_identity_group}}', [
            'id' => $this->primaryKey(11)->unsigned()->comment('ID'),
            'title' => $this->string(25)->unique()->notNull()->comment('名称'),
            'updated_at' => $this->integer()->unsigned()->notNull()->comment('更新时间'),
        ], $this->tableOptions . $this->buildTableComment('身份分组'));
    }

    public function safeDown()
    {
        $this->setForeignKeyCheck();

        $this->dropTable('{{%viMJHk_identity_group}}');

        $this->setForeignKeyCheck(true);
    }

}
