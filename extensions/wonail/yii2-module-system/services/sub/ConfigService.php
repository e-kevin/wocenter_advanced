<?php

namespace wocenter\backend\modules\system\services\sub;

use wocenter\{
    backend\modules\system\models\Config, backend\modules\system\services\SystemService, core\Service, helpers\StringHelper
};
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
     * 获取指定标识的配置值
     *
     * @param string $key 标识ID e.g. WEB_SITE_TITLE
     * @param mixed $defaultValue 默认值
     *
     * @return mixed
     */
    public static function get($key, $defaultValue = null)
    {
        $arr = Config::getAllConfig();
        
        return !isset($arr[$key]) ? $defaultValue : $arr[$key]['value'];
    }
    
    /**
     * 获取指定标识的额外配置值
     *
     * @param string $key 标识ID .e.g BACKEND_THEME
     * @param mixed $defaultValue 默认值
     *
     * @return mixed
     */
    public function extra($key, $defaultValue = null)
    {
        $arr = Config::getAllConfig();
        
        return !isset($arr[$key]) ? $defaultValue : StringHelper::parseString($arr[$key]['extra']);
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
