<?php
use wocenter\console\Migration;

class m170822_065858_create_table_user_identity extends Migration
{

    public function safeUp()
    {
        $this->setForeignKeyCheck();
        
        $this->createTable('{{%viMJHk_user_identity}}', [
            'id' => $this->primaryKey(11)->unsigned()->comment('ID'),
            'uid' => $this->integer(11)->unsigned()->notNull()->comment('UID'),
            'identity_id' => $this->integer(11)->unsigned()->notNull()->comment('身份ID'),
            'status' => $this->boolean()->unsigned()->notNull()->comment('2-未审核，1-启用，0-禁用，-1-删除'),
            'step' => $this->boolean()->unsigned()->notNull()->comment('记录当前执行步骤'),
            'is_init' => $this->boolean()->unsigned()->notNull()->comment('是否初始化身份相关信息 身份积分设置、角色组和头衔分配等'),
            'avatar_is_init' => $this->boolean()->unsigned()->notNull()->comment('头像是否初始化'),
            'rank_is_init' => $this->boolean()->unsigned()->notNull()->comment('头衔是否初始化'),
            'profile_is_init' => $this->boolean()->unsigned()->notNull()->comment('扩展档案是否完善'),
        ], $this->tableOptions . $this->buildTableComment('用户身份关联表'));

        $this->addForeignKey('fk-viMJHk_user_identity-uid', '{{%viMJHk_user_identity}}', 'uid', '{{%viMJHk_user}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-viMJHk_user_identity-identity_id', '{{%viMJHk_user_identity}}', 'identity_id', '{{%viMJHk_identity}}', 'id', 'CASCADE');
        
        $this->setForeignKeyCheck(true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_user_identity}}');
    }

}
