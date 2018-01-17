<?php

use wocenter\backend\modules\system\models\Config;
use wocenter\db\Migration;

class m170822_023219_create_table_config extends Migration
{
    
    public function safeUp()
    {
        $this->createTable('{{%viMJHk_config}}', [
            'id' => $this->primaryKey()->unsigned()->comment('配置ID'),
            'name' => $this->string(30)->unique()->notNull()->comment('标识'),
            'type' => $this->boolean()->unsigned()->notNull()->defaultValue(1)->comment('配置类型 1字符,2文本,3下拉框,4多选框,5单选框,6看板,7日期时间,8日期,9时间'),
            'title' => $this->string(50)->notNull()->comment('配置说明'),
            'category_group' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('配置分组'),
            'extra' => $this->string(255)->notNull()->defaultValue('')->comment('配置数据'),
            'remark' => $this->string(255)->notNull()->defaultValue('')->comment('配置说明'),
            'created_at' => $this->integer()->unsigned()->notNull()->comment('创建时间'),
            'updated_at' => $this->integer()->unsigned()->notNull()->comment('更新时间'),
            'status' => $this->boolean()->unsigned()->notNull()->defaultValue(0)->comment('状态'),
            'value' => $this->text()->notNull()->comment('默认值'),
            'sort_order' => $this->smallInteger(3)->unsigned()->notNull()->defaultValue(0)->comment('排序'),
            'rule' => $this->text()->notNull()->comment('验证规则'),
        ], $this->tableOptions . $this->buildTableComment('系统配置表') . ' AUTO_INCREMENT=1000');
        
        $this->batchInsert('{{%viMJHk_config}}', [
            'id', 'name', 'type', 'title', 'category_group', 'extra', 'remark', 'created_at', 'updated_at', 'status',
            'value', 'sort_order', 'rule',
        ], [
            [1, 'WEB_SITE_ICP', Config::TYPE_STRING, '网站备案号', 1, '', '网站备案号，如：沪ICP备12345678号-9', 1378900335, 1499482978, 1, '', 4, 'string'],
            [2, 'WEB_SITE_TITLE', Config::TYPE_STRING, '网站名称', 1, '', '网站名称', 1378898976, 1499841577, 1, 'WC后台管理系统', 0, "required\r\nstring,max:15"],
            [3, 'WEB_SITE_DESCRIPTION', Config::TYPE_TEXT, '网站简介', 1, '', '搜索引擎描述', 1378898976, 1490085074, 1, '', 1, 'string,max:60'],
            [4, 'WEB_SITE_KEYWORD', Config::TYPE_TEXT, '网站关键词', 1, '', '搜索引擎关键词', 1378898976, 1490085086, 1, 'wocenter', 3, 'string,max:20'],
            [5, 'WEB_SITE_CLOSE', Config::TYPE_RADIO, '关闭站点', 1, '0:关闭,1:开启', '站点关闭后其他用户不能访问，管理员可以正常访问', 1378898976, 1499840776, 0, '1', 0, "required\r\nboolean"],
            [6, 'WEB_SITE_CLOSE_TIPS', Config::TYPE_STRING, '站点关闭提示语', 1, '', '站点关闭后显示的提示信息', 1378898977, 1500198286, 0, '网站正在更新维护，请稍候再试～', 0, "required\r\nstring"],
            [7, 'BACKEND_THEME', Config::TYPE_SELECT, '后台主题', 1, 'wocenter/yii2-theme-adminlte:wocenter/yii2-theme-adminlte', '后台主题风格', 1379122533, 1499840813, 1, 'wocenter/yii2-theme-adminlte', 5, "required\r\nstring"],
            [8, 'FRONTEND_THEME', Config::TYPE_SELECT, '前台主题', 1, 'wocenter/yii2-frontend-theme-basic:wocenter/yii2-frontend-theme-basic', '前台主题风格', 1379122799, 1499840799, 1, 'wocenter/yii2-frontend-theme-basic', 5, "required\r\nstring"],

            [9, 'CONFIG_TYPE_LIST', Config::TYPE_SELECT, '配置类型列表', 0, "1:字符\r\n2:文本\r\n3:下拉框\r\n4:多选框\r\n5:单选框\r\n6:看板\r\n7:日期+时间\r\n8:日期\r\n9:时间", '主要用于数据解析和页面表单的生成', 1378898976, 1499838313, 1, '', 0, 'required'],
            [10, 'CONFIG_GROUP_LIST', Config::TYPE_SELECT, '配置分组', 0, "0:不分组\r\n1:基础\r\n2:内容\r\n3:注册\r\n4:系统\r\n5:安全", '配置分组', 1379228036, 1499676696, 1, '', 0, 'required'],

            [11, 'FILTER_NICKNAME', Config::TYPE_TEXT, '站点预留昵称', 5, '', '默认不被允许使用的用户昵称', 1403948373, 1499482809, 1, '管理员,系统管理员,超级系统管理员,超级管理员,wocenter,admin,administrator,administrators,test,10086,10000,官方,官网,客服,测试,垃圾', 0, 'string'],
            [12, 'FILTER_SENSITIVE_WORD_OPEN', Config::TYPE_RADIO, '过滤敏感词', 5, "0:关闭\r\n1:开启", '是否开启敏感词过滤', 1403948484, 1499840877, 1, '1', 0, "required\r\nboolean"],
            [13, 'FILTER_SENSITIVE_WORD', Config::TYPE_TEXT, '敏感词', 5, '', '被过滤的词语', 1403948585, 1499480636, 1, '共产党,法轮功,习近平,操你妈', 0, 'safe'],
            [14, 'FILTER_SENSITIVE_WORD_REPLACE', Config::TYPE_STRING, '敏感词替换为', 5, '', '敏感词被替换为', 1403948647, 1499480646, 1, '**', 0, 'string'],
    
            [15, 'LIST_ROWS', Config::TYPE_STRING, 'GridView每页记录数', 2, '', 'GridView小部件每页显示记录数', 1379503896, 1500217993, 1, '15', 5, "required\r\ninteger"],
        ]);
    }
    
    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_config}}');
    }
    
}
