<?php

namespace wocenter\backend\modules\account\models;

use wocenter\behaviors\ModifyTimestampBehavior;
use wocenter\core\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%viMJHk_extend_field_setting}}".
 *
 * @property integer $id
 * @property string $field_name
 * @property integer $profile_id
 * @property integer $visible
 * @property integer $sort_order
 * @property integer $form_type
 * @property string $form_data
 * @property string $default_value
 * @property string $rule
 * @property integer $status
 * @property integer $created_at
 * @property string $hint
 *
 * @property ExtendProfile $profile
 * @property ExtendFieldUser[] $extendFieldUsers
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
class ExtendFieldSetting extends ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_extend_field_setting}}';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => ModifyTimestampBehavior::className(),
                'updatedAtAttribute' => false,
            ],
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['field_name', 'profile_id', 'form_type', 'created_at', 'rule'], 'required'],
            [['profile_id', 'visible', 'sort_order', 'form_type', 'status', 'created_at'], 'integer'],
            [['rule'], 'string'],
            [['field_name'], 'string', 'max' => 25],
            [['form_data'], 'string', 'max' => 200],
            [['default_value'], 'string', 'max' => 20],
            [['hint'], 'string', 'max' => 100],
            [['profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendProfile::className(), 'targetAttribute' => ['profile_id' => 'id']],
        ];
        
        return $this->createRules($rules);
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios[self::SCENARIO_DEFAULT] = [
            'field_name', 'profile_id', 'sort_order', 'form_type', 'form_data', 'default_value',
            'rule', 'visible', 'status', 'hint',
        ];
        
        return $this->createScenarios($scenarios);
    }
    
    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'form_data' => "【下拉框、单选框、多选框】类型需要配置该项</br>多个可用英文符号 , ; 或换行分隔，如：</br>key:value,key1:value1;key2:value2",
            'rule' => '多条规则用英文符号 ; 或换行分隔，如：</br>required;string,max:10,min:4;string,length:1-3',
            'hint' => '提示用户如何输入该字段信息',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'field_name' => '字段名称',
            'profile_id' => '所属档案',
            'visible' => '是否公开',
            'sort_order' => '排序',
            'form_type' => '类型',
            'form_data' => '额外数据',
            'default_value' => '默认值',
            'rule' => '验证规则',
            'status' => '状态',
            'created_at' => '创建时间',
            'hint' => '输入提示',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfile()
    {
        return $this->hasOne(ExtendProfile::className(), ['id' => 'profile_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtendFieldUsers()
    {
        return $this->hasMany(ExtendFieldUser::className(), ['field_setting_id' => 'id']);
    }
    
}
