<?php

namespace wocenter\backend\modules\account\models;

use wocenter\behaviors\ModifyTimestampBehavior;
use wocenter\core\ActiveRecord;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%viMJHk_identity}}".
 *
 * @property integer $id
 * @property integer $identity_group
 * @property string $name
 * @property string $title
 * @property string $description
 * @property integer $is_invite
 * @property integer $is_audit
 * @property integer $sort_order
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property array $profileId 关联的档案ID
 *
 * @property IdentityGroup $identityGroup
 * @property IdentityConfig[] $identityConfigs
 * @property IdentityProfile[] $identityProfiles
 * @property ExtendProfile[] $profiles
 * @property UserIdentity[] $userIdentities
 * @property UserProfile[] $userProfiles
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
class Identity extends ActiveRecord
{
    
    /**
     * @var array 关联的档案ID
     */
    protected $profile_id;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_identity}}';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => ModifyTimestampBehavior::className(),
            ],
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['name', 'title', 'is_invite', 'status', 'created_at', 'updated_at'], 'required'],
            ['name', 'unique', 'targetClass' => self::className()],
            ['name', 'match', 'pattern' => '/^[A-Za-z]+\w+$/',
                'message' => Yii::t(
                    'wocenter/app',
                    'The {attribute} must begin with a letter, and only in English, figures and underscores.',
                    ['attribute' => $this->getAttributeLabel('name')]
                ),
            ],
            ['name', 'unique', 'targetClass' => self::className()],
            ['title', 'unique', 'targetClass' => self::className()],
            [['identity_group', 'is_invite', 'is_audit', 'sort_order', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name', 'title'], 'string', 'max' => 25],
            [['description'], 'string', 'max' => 500],
            [['identity_group'], 'exist', 'skipOnError' => true, 'targetClass' => IdentityGroup::className(), 'targetAttribute' => ['identity_group' => 'id']],
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
    
    
    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_INSERT | self::OP_UPDATE,
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'identity_group' => '身份分组',
            'name' => '标识',
            'title' => '名称',
            'description' => '描述',
            'is_invite' => '开启邀请注册',
            'is_audit' => '开启审核',
            'sort_order' => '排序',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'profileId' => '绑定档案',
        ];
    }
    
    /**
     * 获取身份分组
     *
     * @return \yii\db\ActiveQuery
     */
    public function getIdentityGroup()
    {
        return $this->hasOne(IdentityGroup::className(), ['id' => 'identity_group']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdentityConfigs()
    {
        return $this->hasMany(IdentityConfig::className(), ['identity_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProfiles()
    {
        return $this->hasMany(ExtendProfile::className(), ['id' => 'profile_id'])->viaTable('{{%viMJHk_identity_profile}}', ['identity_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserIdentities()
    {
        return $this->hasMany(UserIdentity::className(), ['identity_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdentityProfiles()
    {
        return $this->hasMany(IdentityProfile::className(), ['identity_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfiles()
    {
        return $this->hasMany(UserProfile::className(), ['last_login_identity' => 'id']);
    }
    
    /**
     * 获取指定查询条件的身份筛选列表
     *
     * @param array $condition 查询条件
     * @param string $key 待返回的数组键名，默认为id，可选值为 ['id', 'name']
     * @param string $value 待返回的数组值字段名，默认为title
     *
     * @return array 身份列表 [$key => $value [,...]]
     */
    public function getSelectList($condition = [], $key = 'id', $value = 'title')
    {
        return ArrayHelper::map($this->getList($condition), $key, $value);
    }
    
    /**
     * 获取指定查询条件的身份列表
     * 包含字段：id, name, title, status, is_invite
     *
     * @param array $condition 查询条件
     *
     * @return array 身份列表
     */
    public function getList($condition = [])
    {
        return self::find()->select('id,name,title,status,is_invite')->where($condition)->asArray()->all();
    }
    
    /**
     * 获取关联的档案ID
     *
     * @return array
     */
    public function getProfileId()
    {
        if ($this->profile_id === null && !empty($this->id)) {
            $this->profile_id = $this->getIdentityProfiles()->select('profile_id')->column();
        }
        
        return $this->profile_id;
    }
    
    /**
     * 设置关联的档案ID
     *
     * @param array $profile_id
     */
    public function setProfileId($profile_id)
    {
        $this->profile_id = $profile_id;
    }
    
}
