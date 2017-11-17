<?php
use wocenter\console\controllers\wocenter\Migration;

class m170822_065837_create_table_tag extends Migration
{

    public function safeUp()
    {
        $this->createTable('{{%viMJHk_tag}}', [
            'id' => $this->primaryKey(11)->unsigned()->comment('ID'),
            'title' => $this->string(25)->notNull()->comment('名称'),
            'status' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('状态'),
            'parent_id' => $this->integer(11)->unsigned()->notNull()->defaultValue(0)->comment('父节点'),
            'sort_order' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue(0)->comment('排序'),
        ], $this->tableOptions . $this->buildTableComment('标签分类表'));
        
        $this->batchInsert('{{%viMJHk_tag}}', ['id', 'title', 'status', 'parent_id', 'sort_order'], [
            [1, '默认', 1, 0, 0],
            [2, '开发者', 1, 1, 0],
            [3, '站长', 1, 1, 0],
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_tag}}');
    }

}
