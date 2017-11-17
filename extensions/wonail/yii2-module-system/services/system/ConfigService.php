<?php

namespace wocenter\backend\modules\system\services\system;

use wocenter\backend\modules\system\models\Config;
use wocenter\backend\modules\system\services\SystemService;
use wocenter\core\Service;
use yii\helpers\Json;

/**
 * 系统配置服务类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ConfigService extends Service
{
    
    /**
     * @var SystemService 父级服务类
     */
    public $service;
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'config';
    }
    
    /**
     * 获取系统配置信息
     *
     * @param string $key 配置键
     * @param string $defaultValue 默认值
     *
     * @return mixed
     */
    public function get($key, $defaultValue = null)
    {
        return Config::getValue($key, $defaultValue);
    }
    
    /**
     * 获取指定标识的额外配置值
     *
     * @param string $key 标识ID .e.g DOCUMENT_TYPE
     *
     * @return array
     */
    public function extra($key)
    {
        return Config::getExtra($key);
    }
    
    /**
     * 获取看板配置
     *
     * @param string $key 标识ID e.g. REGISTER_STEP
     * @param string $category 看板分类 e.g. enable|disable，默认为`enable`
     * @param string|array $defaultValue 默认值
     *
     * @return array
     */
    public function kanban($key, $category = 'enable', $defaultValue = [])
    {
        $config = $this->get($key, $defaultValue);
        if (empty($config)) {
            return [];
        }
        $res = [];
        foreach (Json::decode($config, true) as $v) {
            if ($v['group'] == $category) {
                $res = $v['items'];
                break;
            }
        }
        
        return $res;
    }
    
}
