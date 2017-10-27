<?php

namespace wocenter\backend\modules\account\models;

use wocenter\backend\modules\data\models\UserScoreType;
use wocenter\helpers\ArrayHelper;

/**
 * 积分配置表单模型
 */
class SettingScoreForm extends SettingForm
{
    
    /**
     * @var array 虚拟属性
     */
    private $_property;
    
    /**
     * @var array 属性标签名
     */
    private $_labels;
    
    /**
     * @var array 验证规则
     */
    private $_rules;
    
    /**
     * @var array 字段提示信息
     */
    private $_hints;
    
    /**
     * @var array 获取积分类型
     */
    private $_scoreList;
    
    /**
     * @inheritdoc
     */
    protected function initModelConfig()
    {
        $this->_scoreList = $this->getScoreList();
        if (!empty($this->_scoreList)) {
            // 构建模型数据
            foreach ($this->_scoreList as $v) {
                $field = "score_{$v['id']}";
                $this->_property[$field] = ArrayHelper::getValue($this->configModel, "value.{$field}", 0);
                $this->_labels[$field] = $v['name'];
                $this->_hints[$field] = $v['unit'];
                $this->_rules[] = [$field, 'required'];
                $this->_rules[] = [$field, 'integer', 'max' => 10, 'min' => 0];   // 限定积分上限
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
    public function attributeHints()
    {
        return $this->_hints;
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
     * 获取激活状态的积分类型
     *
     * @return array
     */
    public function getScoreList()
    {
        if ($this->_scoreList === null) {
            $userScoreType = new UserScoreType();
            $this->_scoreList = ArrayHelper::listSearch($userScoreType->getList(), ['status' => 1]);
        }
        
        return $this->_scoreList;
    }
    
    protected function formatConfigValue()
    {
        return json_encode($this->_property);
    }
    
}
