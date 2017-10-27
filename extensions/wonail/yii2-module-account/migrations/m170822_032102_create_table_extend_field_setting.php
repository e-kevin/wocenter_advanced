<?php
use wocenter\console\Migration;

class m170822_032102_create_table_extend_field_setting extends Migration
{

    public function safeUp()
    {
        $this->setForeignKeyCheck();

        $this->createTable('{{%viMJHk_extend_field_setting}}', [
            'id' => $this->primaryKey(11)->unsigned()->comment('ID'),
            'field_name' => $this->string(25)->notNull()->comment('字段名称'),
            'profile_id' => $this->integer(11)->unsigned()->notNull()->comment('所属档案'),
            'visible' => $this->boolean()->notNull()->defaultValue(1)->comment('是否公开'),
            'sort_order' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue(0)->comment('排序'),
            'form_type' => $this->boolean()->unsigned()->notNull()->comment('表单类型'),
            'form_data' => $this->string(200)->notNull()->comment('表单数据'),
            'default_value' => $this->char(20)->notNull()->comment('表单默认值'),
            'rule' => $this->text()->notNull()->comment('表单验证规则'),
            'status' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('状态'),
            'created_at' => $this->integer()->unsigned()->notNull()->comment('创建时间'),
            'hint' => $this->string(100)->notNull()->comment('输入提示'),
        ], $this->tableOptions . $this->buildTableComment('扩展字段设置'));
        
        $this->batchInsert('{{%viMJHk_extend_field_setting}}', [
            'id', 'field_name', 'profile_id', 'visible', 'sort_order', 'form_type',
            'form_data', 'default_value', 'rule', 'status', 'created_at', 'hint',
        ], [
            [1, '昵称', 1, 1, 0, 1, '', '', "required\r\nstring", 1, 1409045825, ''],
            [2, '性别', 1, 1, 0, 5, "0:保密\r\n1:男\r\n2:女", '0', "required\r\ninteger", 1, 1423537409, ''],
            [3, '生日', 1, 1, 0, 8, '', '1901-12-14', 'string', 1, 1423537693, ''],
            [4, '签名', 1, 1, 0, 2, '', '这家伙很懒,什么都没有留下', 'string,max:140', 0, 1423537733, ''],
        ]);

        $this->addForeignKey('fk-viMJHk_extend_field_setting-profile_id', '{{%viMJHk_extend_field_setting}}', 'profile_id', '{{%viMJHk_extend_profile}}', 'id', 'CASCADE');

        $this->setForeignKeyCheck(true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_extend_field_setting}}');
    }

}
