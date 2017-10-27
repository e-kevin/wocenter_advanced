<?php
use wocenter\console\Migration;

class m170821_083420_create_table_action extends Migration
{
    
    public function safeUp()
    {
        $this->createTable('{{%viMJHk_action}}', [
            'id' => $this->primaryKey()->unsigned()->comment('主键'),
            'name' => $this->char(30)->notNull()->unique()->comment('标识'),
            'title' => $this->char(80)->notNull()->comment('名称'),
            'description' => $this->char(140)->notNull()->comment('描述'),
            'rule' => $this->text()->notNull()->comment('奖励规则'),
            'type' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('类型 0 - 系统 1 - 用户 2 - 公共'),
            'status' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('状态'),
            'updated_at' => $this->integer()->notNull()->unsigned()->comment('更新时间'),
        ], $this->tableOptions . $this->buildTableComment('系统行为表'));

        $this->batchInsert('{{%viMJHk_action}}', ['id', 'name', 'title', 'description', 'rule', 'type', 'status', 'updated_at'], [
            [1, 'register', '注册用户', '注册用户', '', 1, 1, 1500103147],
            [2, 'login', '登陆账号', '积分+1，每24小时一次', 'a:1:{i:0;s:28:"type:1,rule:1,cycle:24,max:1";}', 1, 1, 1500262410],
            [3, 'validate_password', '验证密码', '验证密码', '', 1, 1, 1499781577],
            [4, 'error_password', '密码验证失败', '密码验证失败', '', 1, 1, 1490087790],
            [5, 'lock_user', '锁定账户', '锁定账户', '', 1, 1, 1499565116],
            [6, 'send_email_verify', '发送邮箱验证', '发送邮箱验证', '', 1, 1, 1499565136],
            [7, 'send_sms_verify', '发送短信验证', '发送短信验证', '', 1, 1, 1499565564],
            [8, 'send_active_email', '发送激活邮件', '发送激活邮件', '', 1, 1, 1500104172],
            [9, 'find_password', '找回密码', '找回密码', '', 1, 1, 1499565170],
            [10, 'init_password', '重置用户密码', '重置用户密码', '', 0, 1, 1490088019],
        ]);

        $this->createIndex('idx-viMJHk_action-type', '{{%viMJHk_action}}', 'type');
    }

    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_action}}');
    }

}
