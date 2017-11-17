<?php
use wocenter\console\controllers\wocenter\Migration;

class m170822_052401_create_table_identity_config extends Migration
{

    public function safeUp()
    {
        $this->setForeignKeyCheck();

        $this->createTable('{{%viMJHk_identity_config}}', [
            'id' => $this->primaryKey(11)->unsigned()->comment('ID'),
            'identity_id' => $this->integer(11)->unsigned()->notNull()->comment('身份类型'),
            'name' => $this->string(25)->notNull()->comment('标识'),
            'category' => $this->string(25)->notNull()->comment('归类标识'),
            'value' => $this->text()->notNull()->comment('配置值'),
            'updated_at' => $this->integer()->unsigned()->notNull()->comment('更新时间'),
        ], $this->tableOptions . $this->buildTableComment('身份配置表'));
        
        $this->batchInsert('{{%viMJHk_identity_config}}', ['id', 'identity_id', 'name', 'category', 'value', 'updated_at'], [
            [1, 1, 'profile', '', '1,2,3,4', 1499840417],
            [2, 1, 'signup', '', '1,2,3,4', 1499840426],
            [3, 1, 'score', '', '{"score_1":"0","score_2":"0","score_3":"0","score_4":"0"}', 1500171452],
        ]);

        $this->addForeignKey('fk-viMJHk_identity_config-identity_id', '{{%viMJHk_identity_config}}', 'identity_id', '{{%viMJHk_identity}}', 'id', 'CASCADE');
        
        $this->setForeignKeyCheck(true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_identity_config}}');
    }

}
