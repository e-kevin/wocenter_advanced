<?php
use wocenter\console\controllers\wocenter\Migration;

class m170822_054703_create_table_invite extends Migration
{

    public function safeUp()
    {
        $this->setForeignKeyCheck();
        
        $this->createTable('{{%viMJHk_invite}}', [
            'id' => $this->primaryKey(11)->unsigned()->comment('ID'),
            'invite_type' => $this->integer()->unsigned()->notNull()->comment('邀请类型id'),
            'code' => $this->string(25)->notNull()->comment('邀请码'),
            'uid' => $this->integer()->unsigned()->notNull()->check('UID'),
            'can_num' => $this->integer(10)->unsigned()->notNull()->defaultValue(1)->comment('可以注册用户（含升级）'),
            'already_num' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)->comment('已经注册用户（含升级）'),
            'status' => $this->boolean()->notNull()->comment('状态 0：已用完，1：还可注册，2：用户取消邀请，-1：管理员删除 -2:已过期'),
            'created_at' => $this->integer()->unsigned()->notNull()->comment('创建时间'),
            'expired_at' => $this->integer()->unsigned()->notNull()->comment('有效期至'),
        ], $this->tableOptions . $this->buildTableComment('邀请码表'));
        
        $this->addForeignKey('fk-viMJHk_invite-invite_type', '{{%viMJHk_invite}}', 'invite_type', '{{%viMJHk_invite_type}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-viMJHk_invite-uid', '{{%viMJHk_invite}}', 'uid', '{{%viMJHk_user}}', 'id', 'CASCADE');
        
        $this->setForeignKeyCheck(true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_invite}}');
    }

}
