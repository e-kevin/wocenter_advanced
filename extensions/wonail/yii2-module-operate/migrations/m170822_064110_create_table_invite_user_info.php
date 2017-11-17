<?php
use wocenter\console\controllers\wocenter\Migration;

class m170822_064110_create_table_invite_user_info extends Migration
{

    public function safeUp()
    {
        $this->setForeignKeyCheck();

        $this->createTable('{{%viMJHk_invite_user_info}}', [
            'id' => $this->primaryKey(11)->unsigned()->comment('ID'),
            'invite_type' => $this->integer(11)->unsigned()->notNull()->comment('邀请类型id'),
            'uid' => $this->integer()->unsigned()->notNull()->comment('UID'),
            'num' => $this->integer()->unsigned()->notNull()->comment('可邀请名额'),
            'already_num' => $this->integer()->unsigned()->notNull()->comment('已邀请名额'),
            'success_num' => $this->integer()->unsigned()->notNull()->comment('成功邀请名额'),
        ], $this->tableOptions);

        $this->addForeignKey('fk-viMJHk_invite_user_info-uid', '{{%viMJHk_invite_user_info}}', 'uid', '{{%viMJHk_user}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-viMJHk_invite_user_info-invite_type', '{{%viMJHk_invite_user_info}}', 'invite_type', '{{%viMJHk_invite_type}}', 'id', 'CASCADE');

        $this->setForeignKeyCheck(true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_invite_user_info}}');
    }

}
