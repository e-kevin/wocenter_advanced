<?php
use wocenter\console\controllers\wocenter\Migration;

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
            [1, 'WEB_SITE_ICP', 1, '网站备案号', 1, '', '网站备案号，如：沪ICP备12345678号-9', 1378900335, 1499482978, 1, '', 4, 'string'],
            [2, 'WEB_SITE_TITLE', 1, '网站名称', 1, '', '网站名称', 1378898976, 1499841577, 1, 'WC后台管理系统', 0, "required\r\nstring,max:15"],
            [3, 'WEB_SITE_DESCRIPTION', 2, '网站简介', 1, '', '搜索引擎描述', 1378898976, 1490085074, 1, '', 1, 'string,max:60'],
            [4, 'WEB_SITE_KEYWORD', 2, '网站关键词', 1, '', '搜索引擎关键词', 1378898976, 1490085086, 1, 'wocenter', 3, 'string,max:20'],
            [5, 'WEB_SITE_CLOSE', 5, '关闭站点', 1, '0:关闭,1:开启', '站点关闭后其他用户不能访问，管理员可以正常访问', 1378898976, 1499840776, 0, '1', 0, "required\r\nboolean"],
            [6, 'WEB_SITE_CLOSE_TIPS', 1, '站点关闭提示语', 1, '', '站点关闭后显示的提示信息', 1378898977, 1500198286, 0, '网站正在更新维护，请稍候再试～', 0, "required\r\nstring"],
            [7, 'BACKEND_THEME', 3, '后台主题', 1, 'adminlte:AdminLTE2', '后台主题风格', 1379122533, 1499840813, 1, 'adminlte', 5, "required\r\nstring"],

            [8, 'CONFIG_TYPE_LIST', 3, '配置类型列表', 0, "1:字符\r\n2:文本\r\n3:下拉框\r\n4:多选框\r\n5:单选框\r\n6:看板\r\n7:日期+时间\r\n8:日期\r\n9:时间", '主要用于数据解析和页面表单的生成', 1378898976, 1499838313, 1, '', 0, 'required'],
            [9, 'CONFIG_GROUP_LIST', 3, '配置分组', 0, "0:不分组\r\n1:基础\r\n2:内容\r\n3:注册\r\n4:系统\r\n5:安全", '配置分组', 1379228036, 1499676696, 1, '', 0, 'required'],
            [10, 'EMAIL_SENDER', 3, '邮箱发送账号', 0, "system:系统\r\naccount:用户中心\r\nadmin:管理员\r\nsupport:技术支持", "系统通知发送邮件时使用到的邮箱账号\r\n该邮箱地址具体设置数据位于params.php文件内，具体格式如下：\r\n'accountEmail' => 'xxx＠wocenter.com'或'accountEmail' => ['xxx＠wocenter.com' => '用户中心']\r\n键名由额外数据的`键名`+`Email`组成，键值为发送账号", 1499519244, 1500037824, 1, '', 0, 'string'],

            [11, 'SEND_REGISTER_WELCOME', 5, '发送欢迎消息', 3, "0:关闭\r\n1:开启【建设中】", '新用户注册后系统发送欢迎邮件、通知等', 1403949241, 1499855860, 1, '0', 1, "required\r\nboolean"],
            [12, 'REGISTER_TYPE', 4, '注册方式', 3, "0:普通注册\r\n1:邀请注册\r\n2:系统生成", '全不选即为关闭注册', 1403949129, 1499933977, 1, '0,1,2', 3, 'safe'],
            [13, 'REGISTER_SWITCH', 4, '注册类型', 3, "0:用户名\r\n1:邮箱\r\n2:手机", '全不选即为关闭注册', 1460108321, 1499855627, 1, '0,1,2', 2, 'safe'],
            [14, 'EMAIL_VERIFY_TYPE', 5, '邮箱验证类型', 3, "0:不验证,\r\n1:注册前发送验证邮件,\r\n2:注册后发送激活邮件", '邮箱验证的类型', 1455462452, 1499840854, 1, '1', 5, "required\r\ninteger"],
            [15, 'MOBILE_VERIFY_TYPE', 5, '手机验证类型', 3, "0:不验证\r\n1:注册前发送验证短信【建设中】", '手机验证的类型', 1422617055, 1501904074, 1, '0', 6, "required\r\ninteger"],
            [16, 'EMAIL_SUFFIX', 2, '邮箱地址后缀过滤', 3, '', "不允许注册的邮件地址后缀，格式：@xx.com,@yy.com\r\n多个用英文','逗号分隔", 1403781161, 1499854235, 1, '', 11, 'string'],
            [17, 'INVITE_RULE', 5, '邀请制度', 3, "0:宽松\r\n1:严谨", "宽松：不论是否开通【邀请注册】，只要注册页面存在有效邀请码，则会按照此邀请码相关信息进行注册\r\n严谨：关闭【邀请注册】后则彻底禁用邀请码进行注册", 1498801928, 1499843483, 1, '0', 4, "required\r\nboolean"],
            [18, 'REGISTER_STEP', 6, '注册步骤', 3, '', '注册后需要进行的步骤', 1475910323, 1499842466, 1, '[{"group":"disable","title":"禁用","items":[]},{"group":"enable","title":"启用","items":[{"title":"修改头像","name":"change_avatar","id":1},{"title":"选择个人标签","name":"set_tag","id":3},{"title":"填写扩展资料","name":"expand_info","id":2}]}]', 7, 'string'],
            [19, 'REGISTER_STEP_CAN_SKIP', 4, '注册步骤是否可跳过', 3, "1:修改头像\r\n2:填写扩展档案\r\n3:选择个人标签", '勾选为可跳过', 1475910868, 1498802174, 1, '1', 8, 'safe'],
            [20, 'ACTIVE_USER', 4, '激活方式', 3, "1:完成注册流程后自动激活\r\n2:管理员激活【建设中】", "默认情况下，除了开启【注册前验证邮箱和手机】两种情况外，其他方式注册的新用户均处于未激活状态。\r\n此设置可根据不同情况激活用户账号", 1499853760, 1499916245, 1, '1,2', 9, 'safe'],
            [21, 'REGISTER_AGREEMENT', 2, '用户注册协议', 3, '', '用户注册协议', 1455632330, 1499854232, 1, "当您申请用户时，表示您已经同意遵守本规章。\r\n欢迎您加入本站点参与交流和讨论，本站点为社区，为维护网上公共秩序和社会稳定，请您自觉遵守以下条款：\r\n\r\n一、不得利用本站危害国家安全、泄露国家秘密，不得侵犯国家社会集体的和公民的合法权益，不得利用本站制作、复制和传播下列信息：\r\n　（一）煽动抗拒、破坏宪法和法律、行政法规实施的；\r\n　（二）煽动颠覆国家政权，推翻社会主义制度的；\r\n　（三）煽动分裂国家、破坏国家统一的；\r\n　（四）煽动民族仇恨、民族歧视，破坏民族团结的；\r\n　（五）捏造或者歪曲事实，散布谣言，扰乱社会秩序的；\r\n　（六）宣扬封建迷信、淫秽、色情、赌博、暴力、凶杀、恐怖、教唆犯罪的；\r\n　（七）公然侮辱他人或者捏造事实诽谤他人的，或者进行其他恶意攻击的；\r\n　（八）损害国家机关信誉的；\r\n　（九）其他违反宪法和法律行政法规的；\r\n　（十）进行商业广告行为的。\r\n\r\n二、互相尊重，对自己的言论和行为负责。\r\n三、禁止在申请用户时使用相关本站的词汇，或是带有侮辱、毁谤、造谣类的或是有其含义的各种语言进行注册用户，否则我们会将其删除。\r\n四、禁止以任何方式对本站进行各种破坏行为。\r\n五、如果您有违反国家相关法律法规的行为，本站概不负责，您的登录信息均被记录无疑，必要时，我们会向相关的国家管理部门提供此类信息。", 10, 'string'],

            [22, 'LIST_ROWS', 1, 'GridView每页记录数', 2, '', 'GridView小部件每页显示记录数', 1379503896, 1500217993, 1, '15', 5, "required\r\ninteger"],

            [23, 'LOGIN_REMIND', 5, '登录提醒', 4, "0:关闭\r\n1:开启", '登录超时后，是否提醒用户进行登录或直接跳转至登录页面', 1499562564, 1500646489, 1, '1', 0, 'boolean'],
            [24, 'UNREAD_FLUSH_INTERVAL', 1, '通知消息刷新间隔时间', 1, '', '单位: 秒', 1455632771, 1499840802, 0, '100', 0, "required\r\ninteger"],

            [25, 'FILTER_NICKNAME', 2, '站点预留昵称', 5, '', '默认不被允许使用的用户昵称', 1403948373, 1499482809, 1, '管理员,系统管理员,超级系统管理员,超级管理员,wocenter,admin,administrator,administrators,test,10086,10000,官方,官网,客服,测试,垃圾', 0, 'string'],
            [26, 'FILTER_SENSITIVE_WORD_OPEN', 5, '过滤敏感词', 5, "0:关闭\r\n1:开启", '是否开启敏感词过滤', 1403948484, 1499840877, 0, '1', 0, "required\r\nboolean"],
            [27, 'FILTER_SENSITIVE_WORD', 2, '敏感词', 5, '', '被过滤的词语', 1403948585, 1499480636, 0, '共产党,法轮功,习近平,操你妈', 0, 'safe'],
            [28, 'FILTER_SENSITIVE_WORD_REPLACE', 1, '敏感词替换为', 5, '', '敏感词被替换为', 1403948647, 1499480646, 0, '**', 0, 'string'],
            [29, 'PASSWORD_RESET_TOKEN_EXPIRE', 1, '重置密码令牌失效时间', 5, '', '单位：秒', 1459695870, 1499840886, 1, '3600', 0, "required\r\ninteger"],
            [30, 'VERIFY_OPEN', 4, '显示验证码', 5, "login:登陆,\r\nsignup:注册,invite-signup:邀请注册,\r\nfind-password:找回密码,reset-password:重置密码,change-password:更改密码,\r\nactivate-account:激活账户", '在哪个场景下显示验证码', 1404038209, 1501225088, 1, 'signup,invite-signup,find-password,reset-password,change-password,activate-account', 0, 'safe'],
            [31, 'ADMIN_ALLOW_IP', 2, '后台允许访问IP', 5, '', '多个用逗号分隔，如果不配置表示不限制IP访问', 1404038297, 1499480600, 0, '', 0, 'string'],
            [32, 'NEED_ACTIVE', 5, '登录许可', 5, "0:不允许\r\n1:允许", '是否允许未激活用户登录', 1403668677, 1499855961, 1, '0', 2, "required\r\nboolean"],
            [33, 'USER_LOCK_OPEN', 5, '账号锁定', 5, "0:关闭\r\n1:开启", "密码输入错误指定次数后是否锁定账号\r\n锁定时间由 [lock_user] 行为限制决定\r\n错误次数由 [error_password]行为限制决定", 1404538197, 1499840909, 1, '1', 0, "required\r\nboolean"],
        ]);
    }
    
    public function safeDown()
    {
        $this->dropTable('{{%viMJHk_config}}');
    }
    
}
