<?php
use wocenter\console\controllers\wocenter\Migration;

class m170822_043346_create_table_extend_field_user extends Migration
{

    public function safeUp()
    {
        $this->setForeignKeyCheck();

        $this->createTable('{{%viMJHk_extend_field_user}}', [
            'id' => $this->primaryKey(11)->unsigned()->comment('ID'),
            'profile_id' => $this->integer(11)->unsigned()->notNull()->comment('所属扩展档案'),
            'uid' =>$this->integer()->unsigned()->notNull()->comment('所属用户'),
            'field_setting_id' => $this->integer(11)->unsigned()->notNull()->comment('扩展字段ID'),
            'field_data' => $this->string(1000)->notNull()->comment('字段数据'),
            'created_at' => $this->integer()->unsigned()->notNull()->comment('创建时间'),
            'updated_at' => $this->integer()->unsigned()->notNull()->comment('更新时间'),
        ], $this->tableOptions.$this->buildTableComment('扩展字段-用户关联表'));

        $this->addForeignKey('fk-viMJHk_extend_field_user-profile_id', '{{%viMJHk_extend_field_user}}', 'profile_id', '{{%viMJHk_extend_profile}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-viMJHk_extend_field_user-uid', '{{%viMJHk_extend_field_user}}', 'uid', '{{%viMJHk_user}}', 'id', 'CASCADE');
        $this->addForeignKey('fk-viMJHk_extend_field_user-field_setting_id', '{{%viMJHk_extend_field_user}}', 'field_setting_id', '{{%viMJHk_extend_field_setting}}', 'id', 'CASCADE');

        $this->setForeignKeyCheck(true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_extend_field_user}}');
    }

}
