<?php

namespace wocenter\backend\modules\extension\services\sub;

use wocenter\{
    backend\modules\extension\models\Theme, backend\modules\extension\services\ExtensionService,
    core\Service, core\ThemeInfo, Wc
};
use yii\base\InvalidConfigException;

/**
 * 主题管理子服务类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ThemeService extends Service
{
    
    /**
     * @var ExtensionService 父级服务类
     */
    public $service;
    
    /**
     * @var string|array|callable|Theme 模块功能扩展类
     */
    public $themeModel = '\wocenter\backend\modules\extension\models\Theme';
    
    /**
     * @var string 默认主题扩展名
     */
    public $defaultTheme = 'wonail/yii2-theme-adminlte';
    
    /**
     * @var string 主题配置键名
     */
    public $themeConfigKey = 'BACKEND_THEME';
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'theme';
    }
    
    /**
     * 删除缓存
     */
    public function clearCache()
    {
        $this->service->getLoad()->clearCache();
    }
    
    /**
     * 获取当前应用本地[所有|已安装]的主题配置信息
     *
     * @param bool $installed 是否获取已安装的扩展，默认为`false`，不获取
     *
     * @return array
     * [
     *  {uniqueName} => [
     *      'class' => {class},
     *      'infoInstance' => {infoInstance},
     *  ]
     * ]
     */
    public function getAllConfigByApp($installed = false)
    {
        $config = $this->service->getLoad()->getAllConfigByApp($installed)['themes'] ?? [];
        unset($config['config']);
        
        return $config;
    }
    
    /**
     * 获取当前系统主题详情信息
     *
     * @return ThemeInfo
     * @throws InvalidConfigException
     */
    public function getCurrentTheme()
    {
        $allExtension = $this->getAllConfigByApp(true);
        $currentTheme = Wc::$service->getSystem()->getConfig()->get(
            $this->themeConfigKey,
            $this->defaultTheme
        );
        if (isset($allExtension[$currentTheme])) {
            return $allExtension[$currentTheme]['infoInstance'];
        } else {
            throw new InvalidConfigException("{$currentTheme} is not found.");
        }
    }
    
}
