<?php

namespace wocenter\backend\modules\account\models;

use wocenter\behaviors\ModifyTimestampBehavior;
use wocenter\core\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%viMJHk_identity_group}}".
 *
 * @property integer $id
 * @property string $title
 * @property integer $updated_at
 *
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
class IdentityGroup extends ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_identity_group}}';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => ModifyTimestampBehavior::className(),
                'createdAtAttribute' => false,
            ],
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['title', 'updated_at'], 'required'],
            ['title', 'unique'],
            [['updated_at'], 'integer'],
            [['title'], 'string', 'max' => 25],
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
            'title' => '名称',
            'updated_at' => '更新时间',
        ];
    }
    
    /**
     * 获取身份分组筛选列表
     *
     * @return array 身份列表 ['id' => 'title' [,...]]
     */
    public function getSelectList()
    {
        $list = $this->getList();
        if (empty($list)) {
            return [];
        }
        
        return ArrayHelper::map($list, 'id', 'title');
    }
    
    /**
     * 获取身份分组列表
     *
     * @return array 身份列表
     */
    public function getList()
    {
        return self::find()->select('id,title')->asArray()->all();
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdentities()
    {
        return $this->hasMany(Identity::className(), ['identity_group' => 'id']);
    }
    
}
