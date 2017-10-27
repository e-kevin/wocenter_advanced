<?php

namespace wocenter\backend\modules\action\models;

use wocenter\behaviors\ModifyTimestampBehavior;
use wocenter\core\ActiveRecord;
use wocenter\helpers\DateTimeHelper;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%viMJHk_action_limit}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $title
 * @property integer $frequency
 * @property integer $timestamp
 * @property integer $time_unit
 * @property string $punish
 * @property integer $send_notification
 * @property string $warning_message
 * @property string $remind_message
 * @property string $send_message
 * @property string $finish_message
 * @property integer $status
 * @property integer $updated_at
 * @property integer $check_ip
 * @property string $action
 *
 * @property string $fullCycle 完整的执行周期信息
 *
 * 行为方法属性
 * @property boolean $modifyUpdatedAt
 * @property boolean $modifyCreatedAt
 * @property string $createdAtAttribute
 * @property string $updatedAtAttribute
 * @method ModifyTimestampBehavior createRules($rules)
 * @see ModifyTimestampBehavior::createRules()
 * @method ModifyTimestampBehavior createScenarios($scenarios)
 * @see ModifyTimestampBehavior::createScenarios()
 */
class ActionLimit extends ActiveRecord
{
    
    const CF_LOGOUT = 'logout';
    const CF_LOCK_ACCOUNT = 'lockAccount';
    const CF_LOCK_IP = 'lockIp';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_action_limit}}';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors[] = [
            'class' => ModifyTimestampBehavior::className(),
            'createdAtAttribute' => false,
        ];
        
        return $behaviors;
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['name', 'title', 'frequency', 'timestamp', 'time_unit', 'updated_at', 'action'], 'required'],
            [['frequency', 'timestamp', 'time_unit', 'send_notification', 'status', 'updated_at', 'check_ip'], 'integer'],
            [['punish', 'warning_message', 'remind_message', 'send_message', 'finish_message'], 'string'],
            [['name'], 'string', 'max' => 50],
            ['name', 'unique', 'targetClass' => self::className()],
            [['title'], 'string', 'max' => 100],
            [['action'], 'string', 'max' => 80],
        ];
        
        return $this->createRules($rules);
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return $this->createScenarios(parent::scenarios());
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '标识',
            'title' => '名称',
            'frequency' => '频率',
            'timestamp' => '周期',
            'fullTimeUnitValue' => '间隔',
            'time_unit' => '时间单位',
            'punish' => '处罚',
            'punishValue' => '处罚',
            'send_notification' => '发送系统通知',
            'warning_message' => '警告提示语',
            'remind_message' => '提醒提示语',
            'finish_message' => '结束提示语',
            'send_message' => '通知内容',
            'status' => '状态',
            'updated_at' => '更新时间',
            'check_ip' => '检测IP',
            'fullCycle' => '周期',
            'action' => '检测行为日志',
        ];
    }
    
    public function attributeHints()
    {
        $variable = nl2br('支持变量
间隔：{timestamp}
时间单位：{time_unit}
剩余可操作次数：{surplus_number}
当前已格式化后的时间：{time}
当前操作请求地址：{url}
下次操作时间：{next_action_time}
锁定时间：{lock_time}
锁定时间单位：{lock_time_unit}
自动解锁时间：{lock_expire_time}
');
        
        return [
            'punish' => '频次结束时执行',
            'check_ip' => '开启检测IP，则会判断当前操作者IP是否通过当前行为限制',
            'warning_message' => '频次结束后的提示语</br>' . $variable,
            'remind_message' => '频次未结束时的提示语</br>' . $variable,
            'finish_message' => '频次结束时的提示语。仅在返回给客户端的信息是从行为限制里获取时才显示</br>' . $variable,
            'frequency' => '可以执行的次数',
            'timestamp' => '多久一个执行周期',
            'action' => '执行行为限制时需要检测的行为日志数据',
            'send_message' => '发送系统通知的消息内容',
        ];
    }
    
    /**
     * 获取完整时间单位
     *
     * @return mixed
     */
    public function getFullTimeUnitValue()
    {
        return $this->timestamp . ' ' . DateTimeHelper::getTimeUnitValue($this->time_unit);
    }
    
    /**
     * 获取完整执行周期
     *
     * @return string
     */
    public function getFullCycle()
    {
        return $this->getFullTimeUnitValue() . ' 内可以执行 ' . $this->frequency . ' 次';
    }
    
    /**
     * @var array 惩罚列表，ActionService调用时会按此顺序执行
     */
    public static $punishList = [
        self::CF_LOCK_ACCOUNT => '锁定账户',
        self::CF_LOCK_IP => '封锁IP',
        self::CF_LOGOUT => '强制登出',
    ];
    
    /**
     * 获取处罚值
     *
     * @return string
     */
    public function getPunishValue()
    {
        $tmp = [];
        if (!is_array($this->punish)) {
            $this->punish = explode(',', $this->punish);
        }
        foreach ($this->punish as $punish) {
            if (isset(self::$punishList[$punish])) {
                $tmp[] = ArrayHelper::getValue(self::$punishList, $punish);
            }
        }
        
        return implode('，', $tmp);
    }
    
    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if (!empty($this->punish)) {
                $this->punish = implode(',', $this->punish);
            }
            
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->punish = explode(',', $this->punish);
    }
    
}
