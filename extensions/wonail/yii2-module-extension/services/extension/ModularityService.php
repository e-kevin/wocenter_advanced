<?php

namespace wocenter\backend\modules\extension\services\extension;

use wocenter\core\ModularityInfo;
use wocenter\core\Service;
use wocenter\backend\modules\extension\models\Module;
use wocenter\interfaces\ModularityInfoInterface;
use wocenter\backend\modules\extension\services\ExtensionService;
use wocenter\Wc;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * 管理系统模块子服务类
 *
 * @property string $coreModuleNamespace 系统核心模块命名空间
 * @property string $developerModuleNamespace 开发者模块命名空间
 * @property array $appModuleNamespace 各应用默认的模块命名空间
 * @property array $coreModules 当前应用的核心模块
 * @property array $urlRules 获取模块路由规则
 * @property array $menus 获取模块菜单配置数据
 * @property array $migrationPath 获取模块数据库迁移目录
 * @property array $bootstraps 获取需要执行bootstrap的模块
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ModularityService extends Service
{
    
    /**
     * @var ExtensionService 父级服务类
     */
    public $service;
    
    /**
     * 缓存已经安装的模块信息
     */
    const CACHE_INSTALLED_MODULES = 'installedModules';
    
    /**
     * 缓存未安装的模块信息
     */
    const CACHE_UNINSTALL_MODULES = 'uninstallModules';
    
    /**
     * 缓存所有已经安装的模块路由规则
     */
    const CACHE_MODULE_URL_RULE = 'allModuleUrlRule';
    
    /**
     * @var integer|false 缓存时间间隔。当为`false`时，则删除缓存数据，默认缓存`一天`
     */
    public $cacheDuration = 86400;
    
    /**
     * @var string|array|callable|Module 模块类
     */
    public $moduleModel = '\wocenter\backend\modules\extension\models\Module';
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'modularity';
    }
    
    /**
     * 获取系统已经安装的模块名称，主要用于列表筛选
     *
     * todo:添加缓存
     *
     * @return array e.g. ['account' => '账户管理', 'rbac' => '权限管理']
     */
    public function getInstalledSelectList()
    {
        return ArrayHelper::getColumn(
            $this->getInstalledWithConfig(),
            'infoInstance.name'
        );
    }
    
    /**
     * 获取当前应用未安装的模块ID
     *
     * @return array 未安装的模块ID
     */
    public function getUninstalledModuleId()
    {
        return $this->getInternal('uninstall');
    }
    
    /**
     * 获取所有模块信息，并以数据库里的配置信息为准，主要用于列表
     *
     * @return array
     */
    public function getModuleList()
    {
        /** @var Module $model */
        $model = Yii::createObject($this->moduleModel);
        $dbModules = $model->getInstalledModules();
        $allConfig = $this->getAllConfigByApp();
        foreach ($allConfig as $name => &$v) {
            /** @var ModularityInfo $infoInstance */
            $infoInstance = $v['infoInstance'];
            // 添加主键
            $v['id'] = $name;
            // 数据库里存在模块信息则标识模块已安装
            if (isset($dbModules[$infoInstance->getUniqueId()])) {
                $existModule = $dbModules[$infoInstance->getUniqueId()];
                // 是否为系统模块
                $infoInstance->isSystem =
                    $infoInstance->isSystem
                    || $existModule['is_system'];
                // 系统模块不可卸载
                $infoInstance->canUninstall = !$infoInstance->isSystem;
                $v['status'] = $existModule['status'];
                $v['run'] = $existModule['run'];
            } else {
                // 数据库不存在数据则可以进行安装
                $infoInstance->canInstall = true;
                $v['status'] = 0; // 未安装则为禁用状态
                $v['run'] = -1; // 未安装则没有正在运行的模块
            }
            // 开发者模块
            $v['developer_module'] = isset($allModuleParts['developer'][$v['id']]);
            // 核心模块
            $v['core_module'] = isset($allModuleParts['core'][$v['id']]);
        }
        
        return $allConfig;
    }
    
    /**
     * 获取单个模块详情，主要用于管理和安装模块
     *
     * @param string $id 模块ID
     * @param boolean $onDataBase 获取数据库数据，默认获取，一般是用于更新模块信息
     *
     * @return Module
     * @throws NotFoundHttpException
     */
    public function getModuleInfo($id, $onDataBase = true)
    {
        $allConfig = $this->getAllConfigByApp();
        
        if (!isset($allConfig[$id])) {
            throw new NotFoundHttpException('模块不存在');
        }
        
        /** @var ModularityInfo $infoInstance */
        $infoInstance = $allConfig[$id]['infoInstance'];
        
        if ($onDataBase) {
            /** @var Module $module */
            $module = $this->moduleModel;
            if (($module = $module::find()->where(['id' => $infoInstance->getUniqueId(), 'app' => Yii::$app->id])->one()) == null) {
                throw new NotFoundHttpException('模块暂未安装');
            }
            $module->infoInstance = $infoInstance;
            // 系统模块及必须安装的模块不可卸载
            $module->infoInstance->canUninstall = !$module->infoInstance->isSystem && !$module->is_system;
        } else {
            /** @var Module $module */
            $module = new $this->moduleModel();
            $module->infoInstance = $infoInstance;
            $module->infoInstance->canInstall = true;
            $module->id = $infoInstance->getUniqueId();
            $module->app = Yii::$app->id;
            $module->module_id = $infoInstance->id;
            $module->is_system = $module->infoInstance->isSystem ? 1 : 0;
            $module->run = isset($allModuleParts['developer'][$id])
                ? $module::RUN_MODULE_DEVELOPER
                : $module::RUN_MODULE_CORE;
            $module->status = 1;
        }
        // 有效的运行模块列表
        $validRunModuleList = [];
        $runModuleList = $module->getRunList();
        // 开发者模块
        if (isset($allModuleParts['developer'][$id])) {
            $validRunModuleList[$module::RUN_MODULE_DEVELOPER] = $runModuleList[$module::RUN_MODULE_DEVELOPER];
        }
        // 核心模块
        if (isset($allModuleParts['core'][$id])) {
            $validRunModuleList[$module::RUN_MODULE_CORE] = $runModuleList[$module::RUN_MODULE_CORE];
        }
        $module->setValidRunList($validRunModuleList);
        
        return $module;
    }
    
    /**
     * 删除缓存
     * - 删除当前应用模块缓存
     * - 删除已安装模块缓存
     * - 删除已安装模块的路由规则缓存
     * - 删除未安装模块缓存
     */
    public function clearCache()
    {
        $this->service->getLoad()->clearCache();
        $appId = Yii::$app->id;
        Wc::cache()->delete([$appId, self::CACHE_INSTALLED_MODULES]);
        Wc::cache()->delete([$appId, self::CACHE_UNINSTALL_MODULES]);
        Wc::cache()->delete([$appId, self::CACHE_MODULE_URL_RULE]);
    }
    
    /**
     * 获取模块路由规则
     *
     * @return array
     */
    public function getUrlRules()
    {
        return Wc::getOrSet([
            Yii::$app->id,
            self::CACHE_MODULE_URL_RULE,
        ], function () {
            $arr = [];
            // 获取所有已经安装的模块配置文件
            foreach ($this->getInstalledWithConfig() as $row) {
                /* @var $infoInterface ModularityInfoInterface */
                $infoInterface = $row['infoInstance'];
                $arr = ArrayHelper::merge($arr, $infoInterface->getUrlRules());
            }
            
            return $arr;
        }, $this->cacheDuration, null, 'commonCache');
    }
    
    /**
     * 获取模块菜单配置数据
     *
     * @return array
     */
    public function getMenus()
    {
        $arr = [];
        // 获取所有已经安装的模块配置文件
        foreach ($this->getInstalledWithConfig() as $row) {
            /* @var $infoInstance ModularityInfoInterface */
            $infoInstance = $row['infoInstance'];
            $arr = ArrayHelper::merge($arr, Wc::$service->getMenu()->formatMenuConfig($infoInstance->getMenus()));
        }
        
        return $arr;
    }
    
    /**
     * 获取模块数据库迁移目录
     *
     * @param bool $installed 是否只获取已安装的模块信息，默认为`true`
     *
     * @return array
     */
    public function getMigrationPath($installed = true)
    {
        return ArrayHelper::getColumn($installed
            ? $this->getInstalledWithConfig()
            : $this->getAllConfigByApp()
            , 'infoInstance.migrationPath'
        );
    }
    
    /**
     * 获取需要执行bootstrap的模块
     *
     * @return array
     */
    public function getBootstraps()
    {
        $bootstrap = [];
        // 获取所有已经安装的模块配置文件
        foreach ($this->getInstalledWithConfig() as $row) {
            /** @var ModularityInfo $infoInstance */
            $infoInstance = $row['infoInstance'];
            if ($infoInstance->bootstrap) {
                $bootstrap[] = $infoInstance->id;
            }
        }
        
        return $bootstrap;
    }
    
    /**
     * 获取本地所有模块配置信息
     * todo 添加缓存
     *
     * @return array
     * [
     *  {app} => [
     *      {name} => [
     *          'class' => {class},
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
            if (is_subclass_of($infoClass, ModularityInfo::className())) {
                $class = $namespacePrefix . 'Module';
                if (!class_exists($class)) {
                    continue;
                }
                // 初始化扩展详情类
                /** @var ModularityInfo $infoInstance */
                $infoInstance = Yii::createObject([
                    'class' => $infoClass,
                    'version' => $row['version'],
                    'migrationPath' => $realPath . DIRECTORY_SEPARATOR . 'migrations',
                ], [
                    $row['id'],
                ]);
                $infoInstance->name = $infoInstance->name ?: $infoInstance->id;
                
                $config[$infoInstance->app][$name] = [
                    'class' => $class,
                    'infoInstance' => $infoInstance,
                ];
            } else {
                continue;
            }
        }
        
        return $config;
    }
    
    /**
     * 获取当前应用本地所有的模块配置信息
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
     * 获取系统已经安装的模块信息，包含模块Info详情
     *
     * @return array
     * [
     *  {name} => [
     *      'class' => {class},
     *      'infoInstance' => {infoInstance},
     *  ]
     * ]
     * @throws \yii\base\InvalidConfigException
     */
    public function getInstalledWithConfig()
    {
        /** @var Module $model */
        $model = Yii::createObject($this->moduleModel);
        // 已经安装的模块
        $installed = $model->getInstalledModules();
        // 所有模块扩展配置
        $allConfig = $this->getAllConfigByApp();
        // 以数据库配置信息为准，读取用户自定义参数值：module_id
        foreach ($allConfig as $name => $row) {
            /** @var ModularityInfo $infoInstance */
            $infoInstance = $row['infoInstance'];
            // 剔除未安装的模块
            if (!isset($installed[$infoInstance->getUniqueId()])) {
                unset($allConfig[$name]);
                continue;
            }
            // 自定义参数赋值
            // todo 更改模块ID后同步更新模块功能扩展对应的模块ID
            $infoInstance->id = $installed[$infoInstance->getUniqueId()]['module_id'];
        }
        
        return $allConfig;
    }
    
    /**
     * 获取已安装的模块配置信息，用于设置系统模块Yii::$app->setModule()
     *
     * @return array [
     * [
     *  {moduleId} => [
     *      'class' => {moduleClass},
     *      {controllerMap} => [
     *          {controllerID} => [
     *              'class' => {controllerClass}
     *          ]
     *      ]
     *  ],
     * ]
     */
    public function getConfig()
    {
        return $this->getInternal('install');
    }
    
    /**
     * 根据$type类型获取应用相关模块
     *
     * @param string $type 操作类型
     *
     * @return array
     */
    protected function getInternal($type)
    {
        $appId = Yii::$app->id;
        switch ($type) {
            case 'install':
                return Wc::getOrSet([
                    $appId,
                    self::CACHE_INSTALLED_MODULES,
                ], function () {
                    $config = [];
                    $installedWithConfig = $this->getInstalledWithConfig();
                    foreach ($installedWithConfig as $uniqueId => $row) {
                        /** @var ModularityInfo $infoInstance */
                        $infoInstance = $row['infoInstance'];
                        $config[$infoInstance->id] = [
                            'class' => $row['class'],
                        ];
                    }
                    
                    return $config;
                }, $this->cacheDuration, null, 'commonCache');
                break;
            case 'uninstall':
                return Wc::getOrSet([
                    $appId,
                    self::CACHE_UNINSTALL_MODULES,
                ], function () {
                    /** @var Module $moduleModel */
                    $moduleModel = Yii::createObject($this->moduleModel);
                    // 已经安装的模块ID数组
                    $installedModuleIds = $moduleModel->getInstalledModuleId();
                    // 系统存在的模块ID数组
                    $existModuleIds = ArrayHelper::getColumn($this->getAllConfigByApp(), 'infoInstance.uniqueId');
                    
                    // 未安装的模块ID数组
                    return array_diff($existModuleIds, $installedModuleIds);
                }, $this->cacheDuration, null, 'commonCache');
                break;
            default:
                throw new InvalidParamException('The "type" param must be set.');
        }
    }
    
}
