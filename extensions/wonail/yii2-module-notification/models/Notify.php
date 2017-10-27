<?php

namespace wocenter\backend\modules\notification\models;

use wocenter\core\ActiveRecord;
use wocenter\Wc;
use Yii;

/**
 * This is the model class for table "{{%viMJHk_notify_node}}".
 *
 * @property integer $id
 * @property string $node
 * @property string $description
 * @property string $name
 * @property string $content_key
 * @property string $name_key
 * @property integer $email_sender
 * @property integer $send_message
 * @property integer $sender 1:用户发送 2:系统发送
 *
 * @property array $senderList 发送者列表
 */
class Notify extends ActiveRecord
{
    
    const CACHE_ALL_NOTIFY = 'notify_all';
    const SENDER_BY_SYSTEM = 2;
    const SENDER_BY_USER = 1;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_notify_node}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['node'], 'unique'],
            ['node', 'match', 'pattern' => '/^[A-Za-z]+\w+$/',
                'message' => Yii::t(
                    'wocenter/app',
                    'The {attribute} must begin with a letter, and only in English, figures and underscores.',
                    ['attribute' => Yii::t('wocenter/app', 'node')]
                ),
            ],
            [['node', 'name', 'description', 'content_key', 'name_key'], 'required'],
            [['send_message', 'sender'], 'integer'],
            [['node', 'description', 'content_key', 'name_key'], 'string', 'max' => 50],
            [['name'], 'string', 'max' => 10],
            [['email_sender'], 'string', 'max' => 15],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'node' => '标识',
            'name' => '名称',
            'description' => '描述',
            'content_key' => '内容key',
            'name_key' => '名称key',
            'email_sender' => '邮件发送账号',
            'send_message' => '发送系统消息',
            'sender' => '发送者',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'node' => '',
            'content_key' => "翻译操作的标识名：Yii::t('wocenter/app', {内容key})",
            'name_key' => "翻译操作的标识名：Yii::t('wocenter/app', {名称key})",
            'email_sender' => '为空表示不发送邮件通知',
        ];
    }
    
    /**
     * 获取所有配置项
     *
     * @param boolean|integer $duration 缓存周期，默认缓存`60`秒
     *
     * @return array
     */
    public function getAll($duration = 60)
    {
        return Wc::getOrSet(self::CACHE_ALL_NOTIFY, function () {
            return Notify::find()->indexBy('node')->asArray()->all() ?: [];
        }, $duration);
    }
    
    /**
     * 获取指定标识的配置值
     *
     * @param string $key 标识ID
     * @param boolean|integer $duration 缓存周期，默认缓存`60`秒
     *
     * @return array|string
     */
    public function getValue($key, $duration = 60)
    {
        $arr = $this->getAll($duration);
        if ($arr[$key] === null) {
            $this->message = "不存在系统通知节点: {{$key}}}";
        }
        
        return $arr[$key];
    }
    
    /**
     * 获取发送者列表
     *
     * @return array
     */
    public function getSenderList()
    {
        return [
            self::SENDER_BY_SYSTEM => '系统',
            self::SENDER_BY_USER => '用户',
        ];
    }
    
    /**
     * 获取发送者值
     *
     * @return mixed
     */
    public function getSenderValue()
    {
        return $this->senderList[$this->sender];
    }
    
    /**
     * @inheritdoc
     */
    public function clearCache()
    {
        Yii::$app->getCache()->delete(self::CACHE_ALL_NOTIFY);
    }
    
}
