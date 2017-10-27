<?php
use wocenter\console\Migration;

class m170822_062356_create_table_invite_type extends Migration
{

    public function safeUp()
    {
        $this->setForeignKeyCheck();

        $this->createTable('{{%viMJHk_invite_type}}', [
            'id' => $this->primaryKey(11)->unsigned()->comment('ID'),
            'title' => $this->string(25)->unique()->notNull()->comment('标题'),
            'length' => $this->integer()->unsigned()->notNull()->defaultValue(11)->comment('验证码长度'),
            'expired_at' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('有效时长'),
            'expired_time_unit' => $this->boolean()->unsigned()->notNull()->defaultValue(4)->comment('有效时长单位'),
            'cycle_num' => $this->boolean()->unsigned()->notNull()->defaultValue(5)->comment('周期内可购买个数'),
            'cycle_time' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('周期时长'),
            'cycle_time_unit' => $this->boolean()->unsigned()->notNull()->defaultValue(2)->comment('周期时长单位'),
            'identities' => $this->string(50)->notNull()->comment('绑定身份'),
            'auth_groups' => $this->string(50)->notNull()->comment('允许购买的用户组'),
            'pay_score' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('每个额度消费'),
            'pay_score_type' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('每个额度消费类型'),
            'increase_score' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('每个邀请成功后获得，邀请者增加积分'),
            'increase_score_type' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('每个邀请成功后获得类型id'),
            'each_follow' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('邀请成功后是否互相关注'),
            'status' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('状态'),
            'created_at' => $this->integer()->unsigned()->notNull()->comment('创建时间'),
            'updated_at' => $this->integer()->unsigned()->notNull()->comment('更新时间'),
        ], $this->tableOptions . $this->buildTableComment('邀请注册码类型表'));
        
        $this->batchInsert('{{%viMJHk_invite_type}}', [
            'id', 'title', 'length', 'expired_at', 'expired_time_unit', 'cycle_num', 'cycle_time', 'cycle_time_unit',
            'identities', 'auth_groups', 'pay_score', 'pay_score_type', 'increase_score', 'increase_score_type',
            'each_follow', 'status', 'created_at', 'updated_at',
        ], [
            [1, '系统默认邀请码', 11, 10, 3, 5, 1, 1, '', '', 0, 1, 0, 1, 1, 1, 1472781797, 1500303162],
        ]);

        $this->setForeignKeyCheck(true);
    }

    public function safeDown()
    {
        $this->setForeignKeyCheck();

        $this->dropTable('{{%viMJHk_invite_type}}');

        $this->setForeignKeyCheck(true);
    }

}
