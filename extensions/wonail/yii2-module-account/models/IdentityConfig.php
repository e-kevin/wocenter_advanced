<?php

namespace wocenter\backend\modules\account\models;

use wocenter\behaviors\ModifyTimestampBehavior;
use wocenter\core\ActiveRecord;
use wocenter\backend\modules\data\models\UserScoreType;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%viMJHk_identity_config}}".
 *
 * @property integer $id
 * @property integer $identity_id
 * @property string $name
 * @property string $category
 * @property string $value
 * @property integer $updated_at
 *
 * @property Identity $identity
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
class IdentityConfig extends ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_identity_config}}';
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
            [['identity_id', 'name', 'value', 'updated_at'], 'required'],
            [['identity_id', 'updated_at'], 'integer'],
            [['value'], 'string'],
            [['name', 'category'], 'string', 'max' => 25],
            [['identity_id'], 'exist', 'skipOnError' => true, 'targetClass' => Identity::className(), 'targetAttribute' => ['identity_id' => 'id']],
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
            'identity_id' => '身份类型',
            'name' => '标识',
            'category' => '归类标识',
            'value' => '配置值',
            'updated_at' => '更新时间',
        ];
    }
    
    /**
     * 获取指定字段的配置信息 [score, rank, avatar, tag, pfo]
     *
     * @param integer $identityId 身份ID
     * @param array|string $fields 查询字段，当该值为字符串时返回一维数组，为数组时返回以查询字段为键名的二维数组
     * @param boolean $returnObj 是否返回对象结果，默认为否，返回数组格式
     * @param boolean $parseScore 是否解析积分配置数据。
     * 当该值为`true`时，返回的积分配置数据格式为['scoreFormat', 'score', 'operator']
     *
     * @see UserScoreType::parseScore()
     * @see UserIdentity::_initScoreConfig()
     *
     * @return array|IdentityConfig 一维数组：[name, value]，二维数组：['name' => [name, value], [...]]
     */
    public function getConfig($identityId = 0, $fields = '', $returnObj = false, $parseScore = false)
    {
        if (empty($fields)) {
            return [];
        }
        $query = self::find();
        $query->select('id, name, value')->where([
            'identity_id' => $identityId,
            'name' => $fields,
        ]);
        if (!$returnObj) {
            $query->asArray();
        }
        $isArray = is_array($fields);
        $infos = $isArray ? $query->all() : $query->one();
        if (!$infos) {
            return [];
        }
        
        if ($isArray) {
            foreach ($infos as &$info) {
                $this->parseConfig($info, $parseScore);
            }
        } else {
            $this->parseConfig($infos, $parseScore);
        }
        
        return $isArray ? ArrayHelper::index($infos, 'name') : $infos;
    }
    
    /**
     * 解析查询结果
     *
     * @param array|IdentityConfig $data
     * @param boolean $parseScore 是否解析积分配置数据。
     * 当该值为`true`时，返回的积分配置数据格式为['scoreFormat', 'score', 'operator']
     *
     * @see UserScoreType::parseScore()
     * @see UserIdentity::_initScoreConfig()
     */
    public function parseConfig(&$data, $parseScore = false)
    {
        if (!empty($data['value'])) {
            switch ($data['name']) {
                case 'score':
                    $data['value'] = json_decode($data['value'], true);
                    foreach ($data['value'] as $name => $value) {
                        // 修正数值上限。一般情况下，数据录入之前已做好相关验证工作，但仍然可能存在其他途径的恶意修改行为。
                        // 积分操作属于敏感操作，故多一层防范也是必要。
                        $value = UserScoreType::parseScore($value);
                        if (!$parseScore) {
                            $value = $value['score'];
                        }
                        $data['value'] = array_merge($data['value'], [
                            $name => $value,
                        ]);
                    }
                    break;
                case 'rank':
                    $data['value'] = json_decode($data['value'], true);
                    break;
                case 'tag':
                case 'profile':
                case 'signup':
                    $data['value'] = explode(',', $data['value']);
                    break;
            }
        }
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdentity()
    {
        return $this->hasOne(Identity::className(), ['id' => 'identity_id']);
    }
    
}
