<?php

namespace wocenter\backend\modules\account\models;

use backend\core\Model;
use yii\base\InvalidConfigException;

/**
 * 身份配置通用表单模型
 */
class SettingForm extends Model
{
    
    /**
     * @var IdentityConfig 身份配置模型
     */
    public $configModel;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        if (is_null($this->configModel)) {
            throw new InvalidConfigException('The "configModel" property must be set.');
        }
        
        $this->initModelConfig();
    }
    
    /**
     * 初始化模型配置，此处可以配置虚拟属性，验证规则，属性标签等
     */
    protected function initModelConfig()
    {
    }
    
    /**
     * 格式化保存进数据库的配置数据
     *
     * @return string
     */
    protected function formatConfigValue()
    {
        return '';
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
        if ($this->configModel->isNewRecord) {
            $this->configModel->value = $this->formatConfigValue();
            if ($this->configModel->save()) {
                return true;
            } else {
                $this->message = $this->configModel->message;
                
                return false;
            }
        } else {
            $this->configModel->value = $this->formatConfigValue();
            if ($this->configModel->save(true, ['value', 'updated_at'])) {
                return true;
            } else {
                $this->message = $this->configModel->message;
                
                return false;
            }
        }
    }
    
}
