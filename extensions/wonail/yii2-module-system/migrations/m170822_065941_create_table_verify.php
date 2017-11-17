<?php
use wocenter\console\controllers\wocenter\Migration;

class m170822_065941_create_table_verify extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%viMJHk_verify}}', [
            'id' => $this->primaryKey()->unsigned()->comment('ID'),
            'identity' => $this->string(255)->notNull()->comment('用户标识'),
            'type' => $this->boolean()->notNull()->defaultValue(0)->comment('验证类型 0:邮箱 1:手机'),
            'code' => $this->string(50)->notNull()->comment('验证码'),
            'created_at' => $this->integer()->notNull()->unsigned()->comment('创建时间'),
        ], "ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='验证码表'");

        $this->createIndex('idx-viMJHk_verify-type', '{{%viMJHk_verify}}', 'type');
        $this->createIndex('idx-viMJHk_verify-code', '{{%viMJHk_verify}}', 'code');
    }

    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_verify}}');
    }

}
