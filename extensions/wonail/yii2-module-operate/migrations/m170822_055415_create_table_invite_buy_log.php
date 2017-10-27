<?php
use wocenter\console\Migration;

class m170822_055415_create_table_invite_buy_log extends Migration
{

    public function safeUp()
    {
        $this->setForeignKeyCheck();

        $this->createTable('{{%viMJHk_invite_buy_log}}', [
            'id' => $this->primaryKey(11)->unsigned()->comment('ID'),
            'invite_type' => $this->integer()->unsigned()->notNull()->comment('邀请类型id'),
            'uid' => $this->integer(11)->unsigned()->notNull()->comment('UID'),
            'num' => $this->integer(10)->unsigned()->notNull()->comment('可邀请名额'),
            'content' => $this->string(200)->unsigned()->notNull()->comment('记录信息'),
            'created_at' => $this->integer()->unsigned()->notNull()->comment('创建时间（做频率用）'),
        ], $this->tableOptions.$this->buildTableComment('用户购买邀请名额记录'));
    
        $this->addForeignKey('fk-viMJHk_invite_buy_log-uid', '{{%viMJHk_invite_buy_log}}', 'uid', '{{%viMJHk_user}}', 'id','CASCADE');
        $this->addForeignKey('fk-viMJHk_invite_buy_log-invite_type', '{{%viMJHk_invite_buy_log}}', 'invite_type', '{{%viMJHk_invite_type}}', 'id','CASCADE');

        $this->setForeignKeyCheck(true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_invite_buy_log}}');
    }

}
