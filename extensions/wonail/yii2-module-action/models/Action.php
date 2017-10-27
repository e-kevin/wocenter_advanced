<?php

namespace wocenter\backend\modules\action\models;

use wocenter\backend\modules\log\models\ActionLog;
use wocenter\behaviors\ModifyTimestampBehavior;
use wocenter\core\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%viMJHk_action}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $title
 * @property string $description
 * @property string $rule
 * @property integer $type
 * @property integer $status
 * @property integer $updated_at
 *
 * @property array $typeList 行为类型列表
 *
 * @property ActionLog[] $actionLogs
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
class Action extends ActiveRecord
{
    
    const TYPE_SYSTEM = 0;
    const TYPE_USER = 1;
    const TYPE_COMMON = 2;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_action}}';
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
            [['name', 'title', 'updated_at'], 'required'],
            ['name', 'unique', 'targetClass' => self::className()],
            [['rule'], 'string'],
            [['type', 'status', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 30],
            [['title'], 'string', 'max' => 80],
            [['description'], 'string', 'max' => 140],
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
            'id' => '主键',
            'name' => '标识',
            'title' => '名称',
            'description' => '描述',
            'rule' => '奖励规则',
            'type' => '行为类型',
            'typeValue' => '行为类型',
            'status' => '状态',
            'updated_at' => '更新时间',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'rule' => nl2br('支持变量
type:奖励类型
rule:对字段进行的具体操作，目前为需要操作的积分数值
cycle:执行周期，单位（小时），表示{cycle}小时内最多执行{max}次
max:单个周期内的最大执行次数
单个规则后可加英文符号`;`连接其他规则'),
            'status' => '禁用后将不记录该行为日志',
            'type' => '生成行为日志的类型',
        ];
    }
    
    /**
     * 获取行为类型列表
     *
     * @return array
     */
    public function getTypeList()
    {
        return [
            self::TYPE_SYSTEM => '系统',
            self::TYPE_USER => '用户',
            self::TYPE_COMMON => '公共',
        ];
    }
    
    /**
     * 获取行为类型值
     *
     * @return mixed
     */
    public function getTypeValue()
    {
        return $this->typeList[$this->type];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if (!empty($this->rule)) {
                $this->rule = serialize(explode(';', $this->rule));
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
        if (!empty($this->rule) && isset($this->rule)) {
            $this->rule = implode(';', unserialize($this->rule));
        }
    }
    
    /**
     * 获取系统行为筛选列表
     *
     * @param string $id
     * @param string $title
     *
     * @return array 行为列表 ['id' => 'title]
     */
    public function getSelectList($id = 'id', $title = 'title')
    {
        return ArrayHelper::map($this->getList(), $id, $title);
    }
    
    /**
     * 获取系统行为列表（所有数据）
     *
     * @return array 系统行为列表
     */
    public function getList()
    {
        return self::find()->select('id,name,title,status')->asArray()->all();
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActionLogs()
    {
        return $this->hasMany(ActionLog::className(), ['action_id' => 'id']);
    }
    
}
