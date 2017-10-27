<?php

namespace wocenter\backend\modules\passport\models;

use wocenter\core\Model;
use wocenter\backend\modules\system\models\Config;
use wocenter\backend\modules\account\models\ExtendFieldUser;
use wocenter\backend\modules\account\models\ExtendProfile;
use wocenter\libs\PinYin;
use wocenter\backend\modules\account\models\UserIdentity;
use wocenter\traits\ParseRulesTrait;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

/**
 * 注册流程 - 完善个人资料
 */
class FlowProfileForm extends Model
{
    
    use ParseRulesTrait;
    
    /**
     * @var integer 用户ID，数据来源在UserIdentity()->needStep()里设置
     * @see UserIdentity::needStep()
     */
    public $uid;
    
    /**
     * @var integer 身份ID，数据来源在UserIdentity()->needStep()里设置
     * @see UserIdentity::needStep()
     */
    public $identityId;
    
    /**
     * @var array 用户当前注册流程的进度数据，['step', 'nextStep']
     */
    public $userStep;
    
    /**
     * @var array 档案列表数据
     */
    public $profiles;
    
    /**
     * @var array 初始化模型类内部属性值，模型内部无法查询到则搜索本类内部成员和属性
     */
    private $_property;
    
    /**
     * @var array 档案ID集合
     */
    private $_profileIds = [];
    
    /**
     * @var array 字段ID集合
     */
    private $_fieldSettingIds = [];
    
    /**
     * @var array 属性标签名
     */
    private $_labels;
    
    /**
     * @var array 验证规则
     */
    private $_rules = [];
    
    /**
     * @var array 字段提示信息
     */
    private $_hints;
    
    /**
     * @var ExtendFieldUser[] 用户已经设置的字段配置数据
     */
    private $_userFieldSettings;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        if (empty($this->uid) || empty($this->identityId)) {
            throw new InvalidConfigException('身份验证信息已过期，请重新登录');
        }
        if (empty($this->userStep)) {
            throw new InvalidConfigException('缺少必要参数 {userStep}');
        }
        /** todo 目前是获取所有档案列表数据，改为只获取注册配置里只开放的数据? */
        $this->profiles = ExtendProfile::find()
            ->select(['id', 'profile_name'])
            ->where(['status' => 1])
            ->with(['extendFieldSettings' => function (ActiveQuery $query) {
                return $query->select([
                    'id', 'profile_id', 'field_name', 'rule', 'form_type', 'form_data', 'default_value', 'hint',
                ])->where(['status' => 1, 'visible' => 1]);
            }])
            ->asArray()->all();
        // 没有有效档案则跳过本流程
        if (is_null($this->profiles)) {
            (new UserIdentity())->updateStep($this->uid, $this->identityId, $this->userStep);
            
            return null;
        }
        foreach ($this->profiles as &$profile) {
            foreach ($profile['extendFieldSettings'] as $k => $field) {
                $this->_profileIds[$field['profile_id']] = $field['profile_id'];
                $this->_fieldSettingIds[$field['id']] = $field['id'];
                // 自定义唯一标识名，因为可能存在多个档案配置相同名字的字段
                $field['name'] = PinYin::Pinyin($field['field_name']) . '_' . $field['profile_id'] . '_' . $field['id'];
                $profile['extendFieldSettings'][$field['name']] = $field;
                // 生成标签
                $this->_labels[$field['name']] = $field['field_name'];
                // 生成验证规则
                $rule = $this->parseRulesToArray($field['rule'], $field['name']);
                if (!empty($rule)) {
                    $this->_rules = array_merge($this->_rules, $rule);
                }
                $this->_hints[$field['name']] = $field['hint'];
                unset($profile['extendFieldSettings'][$k]);
            }
        }
        $this->_initProperty();
    }
    
    private function _initProperty()
    {
        // 获取用户已经设置的字段设置
        $this->_userFieldSettings = ExtendFieldUser::find()
            ->select(['id', 'profile_id', 'field_setting_id', 'field_data'])
            ->where([
                'profile_id' => $this->_profileIds,
                'uid' => $this->uid,
                'field_setting_id' => $this->_fieldSettingIds,
            ])
            ->indexBy(function ($row) {
                return $row['profile_id'] . '_' . $row['field_setting_id'];
            })->all() ?: [];
        // 生成模型内部属性
        foreach ($this->profiles as &$profile) {
            foreach ($profile['extendFieldSettings'] as $k => $field) {
                $key = $field['profile_id'] . '_' . $field['id'];
                $value = key_exists($key, $this->_userFieldSettings) ?
                    $this->_userFieldSettings[$key]['field_data'] :
                    $field['default_value'];
                switch ($field['form_type']) {
                    case Config::TYPE_CHECKBOX:
                        $value = $value !== '' ? explode(',', $value) : $value;
                        break;
                }
                $this->_property[$field['name']] = $value;
            }
        }
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return $this->_labels;
    }
    
    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return $this->_hints;
    }
    
    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        return isset($this->_property[$name]) ? $this->_property[$name] : parent::__get($name);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return $this->_rules;
    }
    
    /**
     * 保存用户-档案关联信息，包括更新已有，新建未有
     *
     * @param array $data
     * @param boolean $canSkip 步骤是否可以跳过
     *
     * @return boolean
     */
    public function save($data, $canSkip = false)
    {
        // 可跳过当前步骤则更新当前步骤
        if ($canSkip) {
            return (new UserIdentity())->updateStep($this->uid, $this->identityId, $this->userStep);
        }
        // 加载数据，用于验证数据合法性
        foreach ($data[$this->formName()] as $key => $value) {
            $this->_property[$key] = is_array($value) ? implode(',', $value) : $value;
        }
        if (!$this->validate()) {
            return false;
        }
        $add = [];
        foreach ($data[$this->formName()] as $key => $value) {
            list($field, $profileId, $fieldSettingId) = explode('_', $key);
            $value = is_array($value) ? implode(',', $value) : $value;
            // 存在档案-用户关联数据
            $unique = $profileId . '_' . $fieldSettingId;
            if ($this->_userFieldSettings && key_exists($unique, $this->_userFieldSettings)) {
                // 存在相应字段数据则判断是否需要更新数据
                if ($this->_userFieldSettings[$unique]->field_data != $value) {
                    $this->_userFieldSettings[$unique]->updateAttributes([
                        'field_data' => $value,
                        'updated_at' => time(),
                    ]);
                }
            } else {
                $add[] = [
                    'profile_id' => $profileId,
                    'uid' => $this->uid,
                    'field_setting_id' => $fieldSettingId,
                    'field_data' => $value,
                    'created_at' => time(),
                ];
            }
        }
        if (!empty($add)) {
            $res = Yii::$app->getDb()->createCommand()->batchInsert(ExtendFieldUser::tableName(), array_keys($add[0]), $add)
                ->execute();
        } else {
            $res = true;
        }
        
        // 操作成功后更新当前步骤
        if ($res) {
            (new UserIdentity())->updateStep($this->uid, $this->identityId, $this->userStep);
        }
        
        return $res;
    }
    
}
