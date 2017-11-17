<?php
use wocenter\console\controllers\wocenter\Migration;

class m170821_094433_create_table_action_limit extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%viMJHk_action_limit}}', [
            'id' => $this->primaryKey()->unsigned()->comment('ID'),
            'name' => $this->char(50)->unique()->notNull()->comment('标识'),
            'title' => $this->string(100)->notNull()->comment('名称'),
            'frequency' => $this->smallInteger(5)->notNull()->defaultValue(1)->comment('频率'),
            'timestamp' => $this->smallInteger(4)->notNull()->defaultValue(1)->comment('间隔'),
            'time_unit' => $this->boolean()->unsigned()->notNull()->defaultValue(2)->comment('时间单位 0-时 1-分 2-秒 3-年 4-月 5-日 6-周'),
            'punish' => $this->text()->notNull()->comment('处罚'),
            'send_notification' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('发送系统通知'),
            'warning_message' => $this->text()->notNull()->comment('警告提示语'),
            'remind_message' => $this->text()->notNull()->comment('提醒提示语'),
            'send_message' => $this->text()->notNull()->comment('通知提示内容'),
            'finish_message' => $this->text()->notNull()->comment('结束提示语'),
            'status' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('状态'),
            'updated_at' => $this->integer()->unsigned()->notNull()->comment('更新时间'),
            'check_ip' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('检测IP'),
            'action' => $this->char(80)->notNull()->comment('检测行为日志'),
        ], $this->tableOptions . $this->buildTableComment('系统限制行为表'));

        $this->batchInsert('{{%viMJHk_action_limit}}', [
            'id', 'name', 'title', 'frequency', 'timestamp', 'time_unit', 'punish', 'send_notification', 'warning_message',
            'remind_message', 'send_message', 'finish_message', 'status', 'updated_at', 'check_ip', 'action',
        ], [
            [1, 'register', '注册限制', 1, 1, 1, '', 0, '抱歉，您的注册行为过于频繁，请在 {timestamp}{time_unit}后再进行注册！感谢您对我们的支持！^_^', '', '', '', 1, 1500194140, 1, 'register'],
            [2, 'lock_user', '锁定账户', 1, 15, 1, '', 0, '你的帐号已经被锁定，请在 {lock_expire_time} 后再执行该操作或联系管理员解锁。', '', '', '', 1, 1499667645, 0, 'lock_user'],
            [3, 'send_email_verify', '发送邮件验证', 1, 1, 1, '', 0, '', '', '', '', 1, 1500108007, 0, 'send_email_verify'],
            [4, 'send_sms_verify', '发送短信验证', 1, 1, 1, '', 0, '', '', '', '', 1, 1499667663, 1, 'send_sms_verify'],
            [5, 'send_active_email', '发送激活邮件', 2, 1, 1, '', 0, '操作频繁，请在 {timestamp}{time_unit} 后再执行该操作。', '', '', '', 1, 1500108042, 0, 'send_active_email'],
            [6, 'find_password', '找回密码', 1, 1, 1, '', 0, '', '', '', '', 1, 1500026441, 0, 'find_password'],
            [7, 'error_password', '密码验证失败', 6, 24, 0, 'lockAccount', 0, '密码输入错误次数过多，请在 {next_action_time} 后再执行该操作。', '密码输入错误，在 {next_action_time} 内你还可以操作 {surplus_number} 次！', '', '密码输入错误', 1, 1500025955, 0, 'error_password'],
            [8, 'init_password', '重置用户密码', 2, 1, 1, '', 0, '', '密码重置成功，新密码为 123456，在 {next_action_time} 内你还可以操作 {surplus_number} 次！', '', '密码重置成功，新密码为 123456', 1, 1500365686, 0, 'init_password'],
        ]);

        $this->createIndex('idx-viMJHk_action_limit-status', '{{%viMJHk_action_limit}}', 'status');
    }

    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_action_limit}}');
    }

}
