<?php

namespace wocenter\backend\modules\account\models;

use wocenter\behaviors\ModifyTimestampBehavior;
use wocenter\core\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%viMJHk_extend_profile}}".
 *
 * @property integer $id
 * @property string $profile_name
 * @property integer $status
 * @property integer $created_at
 * @property integer $sort_order
 * @property integer $visible
 *
 * @property ExtendFieldSetting[] $extendFieldSettings
 * @property ExtendFieldUser[] $extendFieldUsers
 * @property IdentityProfile[] $identityProfiles
 * @property Identity[] $identities
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
class ExtendProfile extends ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_extend_profile}}';
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
            [['profile_name', 'created_at'], 'required'],
            ['profile_name', 'unique'],
            [['status', 'visible', 'sort_order', 'created_at'], 'integer'],
            [['profile_name'], 'string', 'max' => 25],
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
            'profile_name' => '名称',
            'status' => '状态',
            'created_at' => '创建时间',
            'sort_order' => '排序',
            'visible' => '是否公开',
        ];
    }
    
    /**
     * 获取指定查询条件的档案筛选列表
     *
     * @param string $key 待返回的数组键名，默认为id，可选值为 ['id', 'profile_name']
     * @param string $value 待返回的数组值字段名，默认为`profile_name`
     * @param array $condition 查询条件
     *
     * @return array 档案列表 [$key => $value [,...]]
     */
    public function getSelectList($key = 'id', $value = 'profile_name', $condition = [])
    {
        return ArrayHelper::map($this->getList($condition), $key, $value);
    }
    
    /**
     * 获取指定查询条件的档案列表
     * 包含字段：id,profile_name,status,visible
     *
     * @param array $condition 查询条件
     *
     * @return array 档案列表
     */
    public function getList($condition = [])
    {
        return self::find()->select('id,profile_name,status,visible')->where($condition)->asArray()->all();
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtendFieldSettings()
    {
        return $this->hasMany(ExtendFieldSetting::className(), ['profile_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtendFieldUsers()
    {
        return $this->hasMany(ExtendFieldUser::className(), ['profile_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdentityProfiles()
    {
        return $this->hasMany(IdentityProfile::className(), ['profile_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdentities()
    {
        return $this->hasMany(Identity::className(), ['id' => 'identity_id'])->viaTable('{{%viMJHk_identity_profile}}', ['profile_id' => 'id']);
    }
    
}
