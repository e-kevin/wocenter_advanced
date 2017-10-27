<?php
use wocenter\console\Migration;

class m170822_065827_create_table_rank_user extends Migration
{

    public function safeUp()
    {
        $this->setForeignKeyCheck();

        $this->createTable('{{%viMJHk_rank_user}}', [
            'id' => $this->primaryKey(11)->unsigned()->comment('ID'),
            'uid' => $this->integer(11)->unsigned()->notNull()->comment('UID'),
            'rank_id' => $this->integer(11)->unsigned()->notNull()->comment('头衔ID'),
            'reason' => $this->string(255)->notNull()->comment('申请理由'),
            'is_show' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('是否显示在昵称右侧（必须有图片才可）'),
            'status' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('状态'),
            'created_at' => $this->integer(11)->unsigned()->notNull()->defaultValue(0)->comment('创建时间'),
        ], $this->tableOptions . $this->buildTableComment('用户-头衔关联表'));

        $this->addForeignKey('fk-viMJHk_rank_user-uid', '{{%viMJHk_rank_user}}', 'uid', '{{%viMJHk_user}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-viMJHk_rank_user-rank_id', '{{%viMJHk_rank_user}}', 'rank_id', '{{%viMJHk_rank}}', 'id', 'CASCADE');

        $this->setForeignKeyCheck(true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_rank_user}}');
    }

}
