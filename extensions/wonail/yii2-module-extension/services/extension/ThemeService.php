<?php

namespace wocenter\backend\modules\extension\services\extension;

use wocenter\backend\modules\extension\models\Theme;
use wocenter\core\Service;
use wocenter\core\ThemeInfo;
use wocenter\backend\modules\extension\services\ExtensionService;
use Yii;
use yii\helpers\ArrayHelper;

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
    }
    
    /**
     * 获取本地所有主题扩展配置信息
     * todo 添加缓存
     *
     * @return array
     * [
     *  {app} => [
     *      {name} => [
     *          'infoInstance' => {infoInstance},
     *      ]
     *  ]
     * ]
     */
    public function getAllConfig()
    {
        $config = [];
        foreach ($this->service->getLoad()->getConfigFiles() as $name => $row) {
            $namespacePrefix = $row['autoload']['psr-4'][0];
            $realPath = $row['autoload']['psr-4'][1];
            // 扩展详情类
            $infoClass = $namespacePrefix . 'Info';
            if (is_subclass_of($infoClass, ThemeInfo::className())) {
                // 初始化扩展详情类
                /** @var ThemeInfo $infoInstance */
                $infoInstance = Yii::createObject([
                    'class' => $infoClass,
                    'version' => $row['version'],
                    'viewPath' => $realPath . DIRECTORY_SEPARATOR . 'views',
                ], [
                    $row['id'],
                ]);
            
                $config[$infoInstance->app][$name] = [
                    'infoInstance' => $infoInstance,
                ];
            } else {
                continue;
            }
        }
    
        return $config;
    }
    
    /**
     * 获取当前应用本地所有的主题配置信息
     *
     * @return array
     * [
     *  {name} => [
     *      'class' => {class},
     *      'infoInstance' => {infoInstance},
     *  ]
     * ]
     */
    public function getAllConfigByApp()
    {
        return ArrayHelper::getValue($this->getAllConfig(), Yii::$app->id, []);
    }
    
    /**
     * 获取当前系统主题详情信息
     *
     * @return ThemeInfo|null
     */
    public function getCurrentTheme()
    {
        /** @var Theme $model */
        $model = Yii::createObject($this->themeModel);
        // 已经安装的主题扩展
        $installed = $model->getInstalledThemes();
        // 所有主题扩展配置
        $allConfig = $this->getAllConfigByApp();
        /** @var \wocenter\core\View $view */
        $view = Yii::$app->getView();
        foreach ($allConfig as $name => $row) {
            /** @var ThemeInfo $infoInstance */
            $infoInstance = $row['infoInstance'];
            // 剔除未激活的主题扩展
            if (!isset($installed[$infoInstance->getUniqueId()])
                || $infoInstance->id != $view->getThemeName()
            ) {
                continue;
            }
            
            return $infoInstance;
        }
        
        return null;
    }
    
}
