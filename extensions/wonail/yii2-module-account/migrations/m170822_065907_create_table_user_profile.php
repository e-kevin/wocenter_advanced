<?php
use wocenter\console\Migration;

class m170822_065907_create_table_user_profile extends Migration
{
    
    public function safeUp()
    {
        $this->setForeignKeyCheck();
        
        $this->createTable('{{%viMJHk_user_profile}}', [
            'uid' => $this->primaryKey(11)->unsigned()->comment('用户ID'),
            'nickname' => $this->char(16)->notNull()->defaultValue('')->comment('昵称'),
            'realname' => $this->char(20)->null()->comment('真实姓名'),
            'gender' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('0：保密 1：男 2：女'),
            'birthday' => $this->date()->notNull()->defaultValue('1900-01-01')->comment('生日'),
            'question' => $this->char(50)->notNull()->defaultValue('')->comment('问题'),
            'answer' => $this->char(50)->notNull()->defaultValue('')->comment('答案'),
            'signature' => $this->char(50)->notNull()->defaultValue('')->comment('签名'),
            'login_count' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('登录次数'),
            'reg_ip' => $this->char(15)->notNull()->comment('注册IP'),
            'reg_time' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('注册时间'),
            'last_login_ip' => $this->char(15)->notNull()->defaultValue('')->comment('最后登录IP'),
            'last_login_time' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('最后登录时间'),
            'last_location' => $this->char(20)->notNull()->defaultValue('')->comment('最后登录位置'),
            'last_login_identity' => $this->integer(11)->unsigned()->null()->comment('最后登录的身份'),
            'default_identity' => $this->integer(11)->unsigned()->null()->comment('默认身份'),
            'status' => $this->boolean()->notNull()->defaultValue(0)->comment('会员状态'),
            'score_1' => $this->double()->unsigned()->null()->defaultValue(0)->comment('积分'),
            'score_2' => $this->double()->unsigned()->null()->defaultValue(0)->comment('威望'),
            'score_3' => $this->double()->unsigned()->null()->defaultValue(0)->comment('贡献'),
            'score_4' => $this->double()->unsigned()->null()->defaultValue(0)->comment('余额'),
        ], $this->tableOptions . $this->buildTableComment('用户信息表'));
        
        $this->batchInsert('{{%viMJHk_user_profile}}', [
            'uid', 'nickname', 'realname', 'gender', 'birthday', 'question', 'answer', 'signature', 'login_count',
            'reg_ip', 'reg_time', 'last_login_ip', 'last_login_time', 'last_location', 'last_login_identity',
            'default_identity', 'status', 'score_1', 'score_2', 'score_3', 'score_4',
        ], [
            [1, '系统', '系统', 0, '1900-01-01', '', '', '', 4, '127.0.0.1', 1492590233, '127.0.0.1', 1503283442, '本机地址', NULL, 1, 1, 0, 0, 0, 0],
        ]);
        
        $this->createIndex('idx-viMJHk_user_profile-default_identity', '{{%viMJHk_user_profile}}', 'default_identity');
        
        $this->addForeignKey('fk-viMJHk_user_profile-uid', '{{%viMJHk_user_profile}}', 'uid', '{{%viMJHk_user}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-viMJHk_user_profile-last_login_identity', '{{%viMJHk_user_profile}}', 'last_login_identity', '{{%viMJHk_identity}}', 'id', 'SET NULL');
        
        $this->setForeignKeyCheck(true);
    }
    
    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_user_profile}}');
    }
    
}
