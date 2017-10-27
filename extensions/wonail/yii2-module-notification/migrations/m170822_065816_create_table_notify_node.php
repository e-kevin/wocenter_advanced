<?php
use wocenter\console\Migration;

class m170822_065816_create_table_notify_node extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%viMJHk_notify_node}}', [
            'id' => $this->primaryKey()->unsigned()->comment('ID'),
            'node' => $this->string(50)->unique()->notNull()->comment('标识'),
            'name' => $this->string(10)->notNull()->comment('名称'),
            'description' => $this->string(50)->notNull()->comment('描述'),
            'content_key' => $this->string(50)->notNull()->comment('内容key'),
            'name_key' => $this->string(50)->notNull()->comment('名称key'),
            'email_sender' => $this->string(15)->notNull()->comment('邮件发送账号'),
            'send_message' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('发送系统消息'),
            'sender' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('发送者 1:用户发送 2:系统发送'),
        ], $this->tableOptions . $this->buildTableComment('系统通知节点表'));
        
        $this->batchInsert('{{%viMJHk_notify_node}}', ['id', 'node', 'name', 'description', 'content_key', 'name_key', 'email_sender', 'send_message', 'sender'], [
            [1, 'register_active', '激活账户', '激活账户', 'NOTIFY_REGISTER_ACTIVE_CONTENT', 'NOTIFY_REGISTER_ACTIVE_TITLE', 'account', 0, 1],
            [2, 'password_reset', '密码重置', '密码重置', 'NOTIFY_PASSWORD_RESET_CONTENT', 'NOTIFY_PASSWORD_RESET_TITLE', 'account', 0, 1],
            [3, 'password_reset_ok', '密码重置成功', '密码重置成功', 'NOTIFY_PASSWORD_SETOK_CONTENT', 'NOTIFY_PASSWORD_SETOK_TITLE', 'account', 0, 1],
            [4, 'user_lock', '帐号锁定', '帐号锁定', 'NOTIFY_USER_LOCK_CONTENT', 'NOTIFY_USER_LOCK_TITLE', 'account', 0, 1],
            [5, 'register_welcome', '注册欢迎', '注册欢迎', 'NOTIFY_REGISTER_WELCOME_CONTENT', 'NOTIFY_REGISTER_WELCOME_TITLE', 'account', 1, 1],
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_notify_node}}');
    }

}
