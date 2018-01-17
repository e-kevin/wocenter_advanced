<?php

namespace wocenter\backend\modules\system\models;

use backend\core\Model;
use wocenter\traits\ParseRulesTrait;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

/**
 * 配置虚拟表单模型
 */
class ConfigForm extends Model
{
    
    use ParseRulesTrait;
    
    /**
     * @var string 配置分组ID
     */
    public $categoryGroup;
    
    /**
     * @var Config[] 配置模型数据
     */
    public $models;
    
    /**
     * @var array 虚拟属性
     */
    private $_property;
    
    /**
     * @var array 验证规则
     */
    private $_rules = [];
    
    /**
     * @var array 属性标签名
     */
    private $_labels;
    
    /**
     * @var array 字段提示信息
     */
    private $_hints;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        if (is_null($this->categoryGroup)) {
            throw new InvalidConfigException('The "categoryGroup" property must be set.');
        }
        $this->models = Config::getConfigByCategory($this->categoryGroup);
        if (is_null($this->models)) {
            throw new NotFoundHttpException(Yii::t('wocenter/app', 'Page not found.'));
        }
        
        $this->initModelConfig();
    }
    
    /**
     * 初始化模型类内部属性值
     * 模型内部无法查询到则搜索本类内部成员和属性
     */
    protected function initModelConfig()
    {
        if ($this->_property == null) {
            foreach ($this->models as $model) {
                $this->_property[$model->name] = ($model->type == Config::TYPE_CHECKBOX && $model->value !== '')
                    ? explode(',', $model->value)
                    : $model->value;
                $this->_labels[$model->name] = $model->title;
                $this->_hints[$model->name] = nl2br($model->remark);
                if (!empty($model->rule)) {
                    $val = $this->parseRulesToArray($model->rule, $model->name);
                    if (!empty($val)) {
                        $this->_rules = array_merge($this->_rules, $val);
                    }
                }
            }
        }
    }
    
    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return array_keys($this->_property);
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
    public function rules()
    {
        return $this->_rules;
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
    public function __set($name, $value)
    {
        if (isset($this->_property[$name])) {
            $this->_property[$name] = $value;
        } else {
            parent::__set($name, $value);
        }
    }
    
    /**
     * 格式化保存进数据库的配置数据
     *
     * @param Config $model
     *
     * @return string
     */
    protected function formatConfigValue($model)
    {
        return is_array($this->_property[$model->name])
            ? implode(',', $this->_property[$model->name])
            : $this->_property[$model->name];
    }
    
    /**
     * 保存模型更改
     *
     * @return boolean
     */
    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        foreach ($this->models as $model) {
            $model->value = $this->formatConfigValue($model);
            if ($model->save(true, ['value', 'updated_at'])) {
                continue;
            } else {
                $this->message = $model->message;
                
                return false;
            }
        }
        
        return true;
    }
    
}
