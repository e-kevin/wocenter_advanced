<?php
use wocenter\console\Migration;

class m170822_065851_create_table_user extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%viMJHk_user}}', [
            'id' => $this->primaryKey(11)->unsigned()->comment('UID'),
            'username' => $this->string(255)->unique()->null(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string(255)->notNull(),
            'password_reset_token' => $this->string(255)->unique()->null(),
            'email' => $this->string(255)->unique()->null(),
            'mobile' => $this->char(15)->unique()->null(),
            'status' => $this->boolean()->notNull()->defaultValue(1),
            'created_at' => $this->integer()->unsigned()->notNull(),
            'updated_at' => $this->integer()->unsigned()->notNull(),
            'is_audit' => $this->boolean()->notNull()->unsigned()->defaultValue(0)->comment('通过审核'),
            'is_active' => $this->boolean()->notNull()->unsigned()->defaultValue(0)->comment('是否激活'),
            'validate_email' => $this->boolean()->notNull()->unsigned()->defaultValue(0)->comment('邮箱已经验证'),
            'validate_mobile' => $this->boolean()->notNull()->unsigned()->defaultValue(0)->comment('手机已经验证'),
            'created_by' => $this->boolean()->notNull()->unsigned()->defaultValue(0)->comment('注册方式　0:普通注册 1:邀请注册 2:系统自动生成'),
        ], $this->tableOptions . $this->buildTableComment('用户表') . ' AUTO_INCREMENT=10000');

        $this->batchInsert('{{%viMJHk_user}}', [
            'id', 'username', 'auth_key', 'password_hash', 'password_reset_token', 'email', 'mobile', 'status',
            'created_at', 'updated_at', 'is_audit', 'is_active', 'validate_email', 'validate_mobile', 'created_by',
        ], [
            [1, 'admin', '5ZVrrsG2mjkE60S7IDcjNfWcP61b5S22', '$2y$13$9BEDq0lQQX6N8LOJ2XpbUOVMxKTFaDh/pj2fhhcuXb5PNA3eFxc9i', NULL, NULL, NULL, 1, 1492590233, 1501222279, 1, 1, 1, 1, 1],
        ]);
    }

    public function safeDown()
    {
        $this->setForeignKeyCheck();

        $this->dropTable('{{%viMJHk_user}}');

        $this->setForeignKeyCheck(true);
    }

}
