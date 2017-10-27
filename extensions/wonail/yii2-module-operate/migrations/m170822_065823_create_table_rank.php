<?php
use wocenter\console\Migration;

class m170822_065823_create_table_rank extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%viMJHk_rank}}', [
            'id' => $this->primaryKey(11)->unsigned()->comment('ID'),
            'name' => $this->string(50)->notNull()->comment('名称'),
            'logo' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('图标头衔'),
            'created_at' => $this->integer()->unsigned()->notNull()->defaultValue(0)->comment('创建时间'),
            'allow_user_apply' => $this->boolean()->unsigned()->notNull()->comment('允许用户申请 0-否 1-是'),
            'label' => $this->string(20)->notNull()->comment('文字头衔'),
            'label_color' => $this->string(10)->notNull()->comment('文字颜色'),
            'label_bg' => $this->string(10)->notNull()->comment('背景色'),
        ], $this->tableOptions . $this->buildTableComment('头衔表'));
        
        $this->batchInsert('{{%viMJHk_rank}}', ['id', 'name', 'logo', 'created_at', 'allow_user_apply', 'label', 'label_color', 'label_bg'], [
            [1, '一级会员', 0, 1474541663, 1, '一级会员', '', ''],
            [2, '二级会员', 0, 1474542746, 1, '二级会员', '', ''],
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_rank}}');
    }

}
