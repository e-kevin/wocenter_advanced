<?php

namespace wocenter\backend\modules\extension\services\extension;

use wocenter\backend\modules\extension\models\ModuleFunction;
use wocenter\core\FunctionInfo;
use wocenter\core\Service;
use wocenter\backend\modules\extension\services\ExtensionService;
use wocenter\Wc;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Inflector;
use yii\web\NotFoundHttpException;

/**
 * 功能扩展子服务类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ControllerService extends Service
{
    
    /**
     * @var ExtensionService 父级服务类
     */
    public $service;
    
    /**
     * 缓存所有已经安装的功能扩展信息
     */
    const CACHE_INSTALLED_CONTROLLERS = 'installedControllers';
    
    /**
     * @var integer|false 缓存时间间隔。当为`false`时，则删除缓存数据，默认缓存`一天`
     */
    public $cacheDuration = 86400;
    
    /**
     * @var string|array|callable|ModuleFunction 模块功能扩展类
     */
    public $moduleFunctionModel = '\wocenter\backend\modules\extension\models\ModuleFunction';
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'controller';
    }
    
    /**
     * 获取所有功能扩展信息，并以数据库里的配置信息为准，主要用于列表
     *
     * @return array
     */
    public function getFunctionList()
    {
        /** @var ModuleFunction $moduleFunctionModel */
        $moduleFunctionModel = Yii::createObject($this->moduleFunctionModel);
        $dbFunction = $moduleFunctionModel->getInstalledControllers();
        $allConfig = $this->getAllConfigByApp();
        foreach ($allConfig as $name => &$v) {
            /** @var FunctionInfo $infoInstance */
            $infoInstance = $v['infoInstance'];
            // 添加主键
            $v['id'] = $name;
            // 数据库里存在功能扩展信息则标识功能扩展已安装
            if (isset($dbFunction[$infoInstance->getUniqueId()])) {
                $existFunction = $dbFunction[$infoInstance->getUniqueId()];
                // 是否为系统功能扩展
                $infoInstance->isSystem =
                    $infoInstance->isSystem
                    || $existFunction['is_system'];
                // 系统功能扩展不可卸载
                $infoInstance->canUninstall = !$infoInstance->isSystem;
                $v['status'] = $existFunction['status'];
                $infoInstance->moduleId = $existFunction['module_id'];
                $infoInstance->id = $existFunction['controller_id'];
            } else {
                // 数据库不存在数据则可以进行安装
                $infoInstance->canInstall = true;
                $v['status'] = 0; // 未安装则为禁用状态
            }
        }
        
        return $allConfig;
    }
    
    /**
     * 获取单个功能扩展详情，主要用于管理和安装
     *
     * @param string $id 功能扩展ID
     * @param boolean $onDataBase 获取数据库数据，默认获取，一般是用于更新模块信息
     *
     * @return ModuleFunction
     * @throws NotFoundHttpException
     */
    public function getControllerInfo($id, $onDataBase = true)
    {
        $allConfig = $this->getAllConfigByApp();
        
        if (!isset($allConfig[$id])) {
            throw new NotFoundHttpException('功能扩展不存在');
        }
        
        /** @var FunctionInfo $infoInstance */
        $infoInstance = $allConfig[$id]['infoInstance'];
        
        if ($onDataBase) {
            /** @var ModuleFunction $moduleFunctionModel */
            $moduleFunctionModel = $this->moduleFunctionModel;
            if (($moduleFunctionModel = $moduleFunctionModel::find()->where([
                    'id' => $infoInstance->getUniqueId(),
                    'app' => Yii::$app->id,
                ])->one()) == null) {
                throw new NotFoundHttpException('功能扩展暂未安装');
            }
            $moduleFunctionModel->infoInstance = $infoInstance;
            // 系统模块及必须安装的模块不可卸载
            $moduleFunctionModel->infoInstance->canUninstall =
                !$moduleFunctionModel->infoInstance->isSystem
                && !$moduleFunctionModel->is_system;
        } else {
            /** @var ModuleFunction $moduleFunctionModel */
            $moduleFunctionModel = new $this->moduleFunctionModel();
            $moduleFunctionModel->infoInstance = $infoInstance;
            $moduleFunctionModel->infoInstance->canInstall = true;
            $moduleFunctionModel->id = $infoInstance->getUniqueId();
            $moduleFunctionModel->app = Yii::$app->id;
            $moduleFunctionModel->module_id = $moduleFunctionModel->infoInstance->getModuleId();
            $moduleFunctionModel->controller_id = $moduleFunctionModel->infoInstance->id;
            $moduleFunctionModel->is_system = $moduleFunctionModel->infoInstance->isSystem ? 1 : 0;
            $moduleFunctionModel->status = 1;
        }
        
        return $moduleFunctionModel;
    }
    
    /**
     * 删除缓存
     */
    public function clearCache()
    {
        $appId = Yii::$app->id;
        Wc::cache()->delete([$appId, self::CACHE_INSTALLED_CONTROLLERS]);
    }
    
    /**
     * 获取本地所有功能扩展配置信息
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
            if (is_subclass_of($infoClass, FunctionInfo::className())) {
                $files = FileHelper::findFiles($realPath . DIRECTORY_SEPARATOR . 'controllers', [
                    'only' => ['*Controller.php'],
                ]);
                if (empty($files)) {
                    continue;
                }
                $controllerFile = $files[0];
                $controllerName = substr($controllerFile, strrpos($controllerFile, DIRECTORY_SEPARATOR) + 1, -4);
                $class = $namespacePrefix . 'controllers\\' . $controllerName;
                $controllerId = Inflector::camel2id(substr($controllerName, 0, -10));
                if (!class_exists($class)) {
                    continue;
                }
                // 初始化扩展详情类
                /** @var FunctionInfo $infoInstance */
                $infoInstance = Yii::createObject([
                    'class' => $infoClass,
                    'id' => $controllerId,
                    'version' => $row['version'],
                    'migrationPath' => $realPath . DIRECTORY_SEPARATOR . 'migrations',
                ], [
                    $row['id'],
                ]);
                $infoInstance->name = $infoInstance->name ?:
                    ($infoInstance->moduleId ? "/{$infoInstance->moduleId}/{$infoInstance->id}" : $infoInstance->id);
                
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
     * 获取当前应用本地所有的功能扩展配置信息
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
     * 获取系统已经安装的功能扩展信息，包含功能扩展Info详情
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
        /** @var ModuleFunction $model */
        $model = Yii::createObject($this->moduleFunctionModel);
        // 已经安装的功能扩展
        $installed = $model->getInstalledControllers();
        // 所有功能扩展配置
        $allConfig = $this->getAllConfigByApp();
        // 以数据库配置信息为准，读取用户自定义参数值：module_id, controller_id
        foreach ($allConfig as $name => $row) {
            /** @var FunctionInfo $infoInstance */
            $infoInstance = $row['infoInstance'];
            // 剔除未安装的功能扩展
            if (!isset($installed[$infoInstance->getUniqueId()])) {
                unset($allConfig[$name]);
                continue;
            }
            // 自定义参数赋值
            $infoInstance->id = $installed[$infoInstance->getUniqueId()]['controller_id'];
            $infoInstance->setModuleId($installed[$infoInstance->getUniqueId()]['module_id']);
        }
        
        return $allConfig;
    }
    
    /**
     * 获取已安装的功能扩展配置信息，用于为模块或应用添加所需的功能扩展
     *
     * @return array [
     * [
     *  'app' => [
     *      {controllerId} => [
     *          'class' => {class}
     *      ]
     *  ],
     *  'modules' => [
     *      {moduleId} => [
     *          {controllerId} => [
     *              'class' => {class}
     *          ]
     *      ],
     *  ],
     * ]
     */
    public function getConfig()
    {
        return $this->getInternal('install');
    }
    
    /**
     * 根据$type类型获取应用相关功能扩展
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
                    self::CACHE_INSTALLED_CONTROLLERS,
                ], function () {
                    $config = [];
                    $installedWithConfig = $this->getInstalledWithConfig();
                    foreach ($installedWithConfig as $uniqueId => $row) {
                        /** @var FunctionInfo $infoInstance */
                        $infoInstance = $row['infoInstance'];
                        // 存在模块ID则为模块功能扩展
                        if ($infoInstance->getModuleId()) {
                            $config['modules'][$infoInstance->getModuleId()][$infoInstance->id] = [
                                'class' => $row['class'],
                            ];
                        } // 不存在模块ID则为应用控制器扩展
                        else {
                            $config['app'][$infoInstance->id] = [
                                'class' => $row['class'],
                            ];
                        }
                    }
                    
                    return $config;
                }, $this->cacheDuration, null, 'commonCache');
                break;
            case 'uninstall':
                break;
            default:
                throw new InvalidParamException('The "type" param must be set.');
        }
    }
    
    /**
     * 获取功能扩展菜单配置数据
     *
     * @return array
     */
    public function getMenus()
    {
        $arr = [];
        foreach ($this->getInstalledWithConfig() as $uniqueId => $row) {
            /* @var $infoInstance FunctionInfo */
            $infoInstance = $row['infoInstance'];
            $arr = ArrayHelper::merge($arr, Wc::$service->getMenu()->formatMenuConfig($infoInstance->getMenus()));
        }
        
        return $arr;
    }
    
}
