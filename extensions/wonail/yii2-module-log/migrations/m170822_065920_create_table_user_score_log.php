<?php
use wocenter\console\Migration;

class m170822_065920_create_table_user_score_log extends Migration
{

    public function safeUp()
    {
        $this->setForeignKeyCheck();

        $this->createTable('{{%viMJHk_user_score_log}}', [
            'id' => $this->primaryKey()->unsigned()->comment('ID'),
            'uid' => $this->integer(11)->unsigned()->notNull()->comment('执行用户'),
            'type' => $this->integer()->unsigned()->notNull()->comment('奖励类型'),
            'ip' => $this->bigInteger(20)->notNull()->comment('操作IP'),
            'action' => $this->boolean()->unsigned()->notNull()->comment('调整类型 0:加 1:减'),
            'value' => $this->double()->unsigned()->notNull()->comment('积分变动'),
            'finally_value' => $this->double()->notNull()->comment('积分最终值'),
            'created_at' => $this->integer()->notNull()->unsigned()->comment('创建时间'),
            'remark' => $this->string(255)->notNull()->comment('变动描述'),
            'model' => $this->string(20)->notNull()->comment('触发模型'),
            'record_id' => $this->integer()->unsigned()->notNull()->comment('触发记录ID'),
            'request_url' => $this->string(512)->notNull()->comment('请求地址'),
        ], $this->tableOptions . $this->buildTableComment('用户积分日志表'));

        $this->createIndex('idx-viMJHk_user_score_log-created_at', '{{%viMJHk_user_score_log}}', 'created_at');
        $this->createIndex('idx-viMJHk_user_score_log-model', '{{%viMJHk_user_score_log}}', 'model');

        $this->addForeignKey('fk-viMJHk_user_score_log-uid', '{{%viMJHk_user_score_log}}', 'uid', '{{%viMJHk_user}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-viMJHk_user_score_log-type', '{{%viMJHk_user_score_log}}', 'type', '{{%viMJHk_user_score_type}}', 'id', 'CASCADE');

        $this->setForeignKeyCheck(true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_user_score_log}}');
    }

}
