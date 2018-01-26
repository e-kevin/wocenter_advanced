<?php

namespace wocenter\backend\modules\extension\services\sub;

use wocenter\{
    backend\modules\extension\models\Module, backend\modules\extension\models\ModuleFunction,
    backend\modules\extension\models\Theme, core\FunctionInfo, core\ModularityInfo, core\Service,
    core\ThemeInfo, helpers\FileHelper, helpers\StringHelper, backend\modules\extension\services\ExtensionService, Wc
};
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * 加载扩展子服务类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class LoadService extends Service
{
    
    /**
     * @var ExtensionService 父级服务类
     */
    public $service;
    
    /**
     * @var string 缓存所有扩展别名
     */
    const CACHE_ALL_EXTENSION_ALIASES = 'all_extension_aliases';
    
    /**
     * @var string 缓存所有扩展文件配置信息
     */
    const CACHE_ALL_CONFIG_FILE = 'all_extension_file_config';
    
    /**
     * @var string 缓存扩展配置信息
     */
    const CACHE_ALL_CONFIG_PREFIX = 'all_extension_config_';
    
    /**
     * @var string 缓存所有已经安装的扩展信息
     */
    const CACHE_ALL_INSTALLED_EXTENSIONS = 'all_installed_extensions';
    
    /**
     * @var integer|false 缓存时间间隔。当为`false`时，则删除缓存数据，默认缓存`一天`
     */
    public $cacheDuration = 86400;
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'load';
    }
    
    /**
     * 删除缓存
     */
    public function clearCache()
    {
        Wc::cache()->delete(self::CACHE_ALL_CONFIG_FILE);
        Wc::cache()->delete(self::CACHE_ALL_EXTENSION_ALIASES);
        Wc::cache()->delete(self::CACHE_ALL_INSTALLED_EXTENSIONS);
        Wc::cache()->delete([
            self::CACHE_ALL_CONFIG_PREFIX,
            true,
        ]);
        Wc::cache()->delete([
            self::CACHE_ALL_CONFIG_PREFIX,
            false,
        ]);
        Wc::cache()->delete([
            self::CACHE_ALL_CONFIG_PREFIX,
            'local',
        ]);
        $this->_allLocalConfig = $this->_allInstalledConfig = $this->_allInstalled = $this->_allConfig = $this->_allConfig = null;
    }
    
    /**
     * @var array 所有扩展文件配置信息
     */
    protected $_configFiles;
    
    /**
     * 搜索本地目录，获取所有扩展文件配置信息
     *
     * @return array
     */
    public function getConfigFiles(): array
    {
        if ($this->_configFiles === null) {
            $this->_configFiles = Wc::getOrSet(self::CACHE_ALL_CONFIG_FILE, function () {
                $files = FileHelper::findFiles(StringHelper::ns2Path('extensions'), [
                    'only' => ['config.php'],
                ]);
                if (empty($files)) {
                    return [];
                }
                $config = [];
                foreach ($files as $file) {
                    $file = require "{$file}";
                    $name = ArrayHelper::remove($file, 'name');
                    $psr4 = ArrayHelper::remove($file['autoload'], 'psr-4');
                    $config[$name] = $file;
                    if (!isset($config[$name]['version'])) {
                        $config[$name]['version'] = 'dev-master';
                    }
                    $config[$name]['autoload']['psr-4'] = [
                        array_keys($psr4)[0],
                        array_shift($psr4),
                    ];
                }
                
                return $config;
            }, $this->cacheDuration, null, 'commonCache');
        }
        
        return $this->_configFiles;
    }
    
    /**
     * 加载扩展别名
     */
    public function loadAliases()
    {
        return Wc::getOrSet(self::CACHE_ALL_EXTENSION_ALIASES, function () {
            $config = [];
            foreach ($this->getConfigFiles() as $uniqueName => $row) {
                $namespace = '@' . str_replace('\\', '/', rtrim($row['autoload']['psr-4'][0], '\\'));
                $config[$namespace] = $row['autoload']['psr-4'][1];
            }
            
            return $config;
        }, $this->cacheDuration, null, 'commonCache');
    }
    
    /**
     * @var array 获取本地[所有]扩展配置信息，以数据库信息为准
     */
    protected $_allConfig;
    
    /**
     * @var array 获取本地[已安装]的扩展配置信息，以数据库信息为准
     */
    protected $_allInstalledConfig;
    
    /**
     * 获取本地[所有|已安装]的扩展配置信息，以数据库信息为准
     *
     * @param bool $installed 是否获取已安装的扩展，默认为`false`，不获取
     *
     * @return array
     * [
     *  {app} => [
     *      'controllers' => [
     *          {uniqueName} => [
     *              'class' => {class},
     *              'infoInstance' => {infoInstance},
     *          ],
     *          'config' => [],
     *      ],
     *      'modules' => [
     *          {uniqueName} => [
     *              'class' => {class},
     *              'infoInstance' => {infoInstance},
     *          ],
     *          'config' => [],
     *      ],
     *      'themes' => [
     *          {uniqueName} => [
     *              'infoInstance' => {infoInstance},
     *          ],
     *      ],
     *  ]
     * ]
     */
    public function getAllConfig($installed = false): array
    {
        if ($installed) {
            if ($this->_allInstalledConfig === null) {
                $this->_allInstalledConfig = Wc::getOrSet([
                    self::CACHE_ALL_CONFIG_PREFIX,
                    $installed,
                ], function () {
                    $allConfig = $this->getAllConfig(false);
                    foreach ($allConfig as $app => $item) {
                        foreach ($item as $type => $row) {
                            if (in_array($type, ['controllers', 'modules', 'themes'])) {
                                unset($row['config']);
                                foreach ($row as $uniqueName => $config) {
                                    if (!isset($this->getInstalled()[$uniqueName])) {
                                        unset($allConfig[$app][$type][$uniqueName]);
                                    }
                                }
                            }
                        }
                    }
                    
                    return $allConfig;
                }, $this->cacheDuration, null, 'commonCache');
            }
            
            return $this->_allInstalledConfig;
        } else {
            if ($this->_allConfig === null) {
                $this->_allConfig = Wc::getOrSet([
                    self::CACHE_ALL_CONFIG_PREFIX,
                    $installed,
                ], function () {
                    $allConfig = $this->getAllLocalConfig();
                    foreach ($allConfig as $app => &$item) {
                        foreach ($item as $type => $row) {
                            if (in_array($type, ['controllers', 'modules', 'themes'])) {
                                unset($row['config']);
                                foreach ($row as $uniqueName => $config) {
                                    /** @var FunctionInfo|ModularityInfo|ThemeInfo $infoInstance */
                                    $infoInstance = $config['infoInstance'];
                                    switch (true) {
                                        case is_subclass_of($infoInstance, FunctionInfo::className()):
                                            if (isset($this->getInstalled()[$uniqueName])) {
                                                // 根据数据库数据自定义参数赋值
                                                $infoInstance->id = $this->getInstalled()[$uniqueName]['controller_id'];
                                                $infoInstance->setModuleId($this->getInstalled()[$uniqueName]['module_id']);
                                            }
                                            break;
                                        case is_subclass_of($infoInstance, ModularityInfo::className()):
                                            if (isset($this->getInstalled()[$uniqueName])) {
                                                // 根据数据库数据自定义参数赋值
                                                $infoInstance->id = $this->getInstalled()[$uniqueName]['module_id'];
                                                // todo 自定义bootstrap
                                            }
                                            break;
                                        case is_subclass_of($infoInstance, ThemeInfo::className()):
                                            break;
                                    }
                                }
                            }
                        }
                    }
                    
                    return $allConfig;
                }, $this->cacheDuration, null, 'commonCache');
            }
            
            return $this->_allConfig;
        }
    }
    
    /**
     * 获取当前应用本地[所有|已安装]的扩展配置信息，以数据库信息为准
     *
     * @param bool $installed 是否获取已安装的扩展，默认为`false`，不获取
     *
     * @return array
     * [
     *  'controllers' => [
     *      {uniqueName} => [
     *          'class' => {class},
     *          'infoInstance' => {infoInstance},
     *      ],
     *      'config' => [],
     *  ],
     *  'modules' => [
     *      {uniqueName} => [
     *          'class' => {class},
     *          'infoInstance' => {infoInstance},
     *      ],
     *      'config' => [],
     *  ],
     *  'themes' => [
     *      {uniqueName} => [
     *          'infoInstance' => {infoInstance},
     *      ],
     *  ],
     * ]
     */
    public function getAllConfigByApp($installed = false): array
    {
        return ArrayHelper::getValue($this->getAllConfig($installed), Yii::$app->id, []);
    }
    
    /**
     * @var array 获取本地所有扩展配置信息
     */
    protected $_allLocalConfig;
    
    /**
     * 获取本地所有扩展配置信息
     *
     * @return array
     * [
     *  {app} => [
     *      'controllers' => [
     *          {uniqueName} => [
     *              'class' => {class},
     *              'infoInstance' => {infoInstance},
     *          ],
     *          'config' => [],
     *      ],
     *      'modules' => [
     *          {uniqueName} => [
     *              'class' => {class},
     *              'infoInstance' => {infoInstance},
     *          ],
     *          'config' => [],
     *      ],
     *      'themes' => [
     *          {uniqueName} => [
     *              'infoInstance' => {infoInstance},
     *          ],
     *      ],
     *  ]
     * ]
     */
    public function getAllLocalConfig(): array
    {
        if ($this->_allLocalConfig === null) {
            $this->_allLocalConfig = Wc::getOrSet([
                self::CACHE_ALL_CONFIG_PREFIX,
                'local',
            ], function () {
                $config = [];
                foreach ($this->getConfigFiles() as $uniqueName => $row) {
                    $namespace = $row['autoload']['psr-4'][0];
                    $realPath = $row['autoload']['psr-4'][1];
                    // 扩展详情类
                    $infoClass = $namespace . 'Info';
                    if (is_subclass_of($infoClass, FunctionInfo::className())) {
                        $files = FileHelper::findFiles($realPath . DIRECTORY_SEPARATOR . 'controllers', [
                            'only' => ['*Controller.php'],
                        ]);
                        if (empty($files)) {
                            continue;
                        }
                        $controllerFile = $files[0];
                        $controllerName = substr($controllerFile, strrpos($controllerFile, DIRECTORY_SEPARATOR) + 1, -4);
                        $class = $namespace . 'controllers\\' . $controllerName;
                        $controllerId = Inflector::camel2id(substr($controllerName, 0, -10));
                        if (!class_exists($class)) {
                            continue;
                        }
                        // 初始化扩展详情类
                        /** @var FunctionInfo $infoInstance */
                        $infoInstance = Yii::createObject([
                            'class' => $infoClass,
                            'id' => $controllerId,
                            'migrationPath' => $realPath . DIRECTORY_SEPARATOR . 'migrations',
                        ], [
                            $row['id'],
                            $uniqueName,
                            $row['version'],
                        ]);
                        $infoInstance->name = $infoInstance->name ?:
                            ($infoInstance->getModuleId() ? "/{$infoInstance->getModuleId()}/{$infoInstance->id}" : $infoInstance->id);
                        
                        $config[$infoInstance->app]['controllers'][$uniqueName] = [
                            'class' => $class,
                            'infoInstance' => $infoInstance,
                        ];
                        // 扩展配置信息
                        $config[$infoInstance->app]['controllers']['config'] = ArrayHelper::merge(
                            $config[$infoInstance->app]['controllers']['config'] ?? [],
                            $infoInstance->getConfig()
                        );
                        // 剔除扩展配置无效键名
                        foreach ($infoInstance->getConfigKey() as $key) {
                            if (!isset($config[$infoInstance->app]['controllers']['config'][$key])) {
                                unset($config[$infoInstance->app]['controllers']['config'][$key]);
                            }
                        }
                    } else if (is_subclass_of($infoClass, ModularityInfo::className())) {
                        $class = $namespace . 'Module';
                        if (!class_exists($class)) {
                            continue;
                        }
                        // 初始化扩展详情类
                        /** @var ModularityInfo $infoInstance */
                        $infoInstance = Yii::createObject([
                            'class' => $infoClass,
                            'migrationPath' => $realPath . DIRECTORY_SEPARATOR . 'migrations',
                        ], [
                            $row['id'],
                            $uniqueName,
                            $row['version'],
                        ]);
                        $infoInstance->name = $infoInstance->name ?: $infoInstance->id;
                        
                        $config[$infoInstance->app]['modules'][$uniqueName] = [
                            'class' => $class,
                            'infoInstance' => $infoInstance,
                        ];
                        // 扩展配置信息
                        $config[$infoInstance->app]['modules']['config'] = ArrayHelper::merge(
                            $config[$infoInstance->app]['modules']['config'] ?? [],
                            $infoInstance->getConfig()
                        );
                        // 剔除扩展配置无效键名
                        foreach ($infoInstance->getConfigKey() as $key) {
                            if (!isset($config[$infoInstance->app]['modules']['config'][$key])) {
                                unset($config[$infoInstance->app]['modules']['config'][$key]);
                            }
                        }
                    } elseif (is_subclass_of($infoClass, ThemeInfo::className())) {
                        // 初始化扩展详情类
                        /** @var ThemeInfo $infoInstance */
                        $infoInstance = Yii::createObject([
                            'class' => $infoClass,
                            'viewPath' => $realPath . DIRECTORY_SEPARATOR . 'views',
                        ], [
                            $row['id'],
                            $uniqueName,
                            $row['version'],
                        ]);
                        
                        $config[$infoInstance->app]['themes'][$uniqueName] = [
                            'infoInstance' => $infoInstance,
                        ];
                    } else {
                        continue;
                    }
                }
                
                return $config;
            }, $this->cacheDuration, null, 'commonCache');
        }
        
        return $this->_allLocalConfig;
    }
    
    /**
     * 获取本地所有扩展配置信息的一维数组
     *
     * @return array
     */
    public function getSimpleLocalConfig(): array
    {
        $all = [];
        foreach ($this->getAllLocalConfig() as $app => $item) {
            foreach ($item as $type => $row) {
                if (in_array($type, ['controllers', 'modules', 'themes'])) {
                    unset($row['config']);
                    foreach ($row as $uniqueName => $config) {
                        $all[$uniqueName] = $config;
                    }
                }
            }
        }
        
        return $all;
    }
    
    /**
     * @var array|null 已经安装的扩展数据，包括功能、模块和主题扩展
     */
    protected $_allInstalled;
    
    /**
     * 获取已经安装的扩展数据，包括功能、模块和主题扩展
     *
     * @return array|null
     * [
     *  {uniqueName} => [],
     * ]
     */
    public function getInstalled(): array
    {
        if ($this->_allInstalled === null) {
            $this->_allInstalled = Wc::getOrSet(self::CACHE_ALL_INSTALLED_EXTENSIONS, function () {
                /** @var ModuleFunction $moduleFunctionModel */
                $moduleFunctionModel = Yii::createObject($this->service->getController()->moduleFunctionModel);
                /** @var Module $moduleModel */
                $moduleModel = Yii::createObject($this->service->getModularity()->moduleModel);
                /** @var Theme $themeModel */
                $themeModel = Yii::createObject($this->service->getTheme()->themeModel);
                
                return array_merge(
                    $moduleFunctionModel->getInstalled(), // 已经安装的功能扩展
                    $moduleModel->getInstalled(), // 已经安装的模块扩展
                    $themeModel->getInstalled() // 已经安装的主题扩展
                );
            }, $this->cacheDuration, null, 'commonCache');
        }
        
        return $this->_allInstalled;
    }
    
    /**
     * 生成已安装扩展的系统配置信息
     */
    public function generateConfig()
    {
        // 加载扩展别名，确保能够正确加载系统扩展
        foreach ($this->loadAliases() as $namespace => $realPath) {
            Yii::setAlias($namespace, $realPath);
        }
        // 所有已经安装的扩展
        $allInstalledConfig = $this->getAllConfigByApp(true);
        // 合并扩展配置信息
        $config = ArrayHelper::merge(
            ArrayHelper::remove($allInstalledConfig['modules'], 'config', []),
            ArrayHelper::remove($allInstalledConfig['controllers'], 'config', [])
        );
        // 启动模块配置信息
        $config['bootstrap'] = [];
        // 创建模块配置
        if ($allInstalledConfig['modules']) {
            foreach ($allInstalledConfig['modules'] as $uniqueName => $row) {
                /** @var ModularityInfo $infoInstance */
                $infoInstance = ArrayHelper::remove($row, 'infoInstance');
                $config['modules'][$infoInstance->id] = $row;
                // 加载启动模块
                if ($infoInstance->bootstrap) {
                    array_push($config['bootstrap'], $infoInstance->id);
                }
            }
        }
        // 创建控制器配置
        if ($allInstalledConfig['controllers']) {
            foreach ($allInstalledConfig['controllers'] as $uniqueName => $row) {
                /** @var FunctionInfo $infoInstance */
                $infoInstance = ArrayHelper::remove($row, 'infoInstance');
                // 存在模块ID且模块存在则为模块功能扩展
                if (isset($config['modules'][$infoInstance->getModuleId()])) {
                    $config['modules'][$infoInstance->getModuleId()]['controllerMap'][$infoInstance->id] = $row;
                } // 不存在模块ID则为应用控制器扩展
                else if (empty($infoInstance->getModuleId())) {
                    $config['controllerMap'][$infoInstance->id] = $row;
                }
            }
        }
        
        return $config;
    }
    
    /**
     * 根据命名空间获取扩展名称
     *
     * @param string $namespace 命名空间
     *
     * @return string
     */
    public function getExtensionNameByNamespace($namespace)
    {
        $namespace = str_replace('/', '\\', $namespace);
        $has = '';
        foreach ($this->getConfigFiles() as $uniqueName => $config) {
            if (strpos($namespace, $config['autoload']['psr-4'][0]) !== false) {
                $has = $uniqueName;
                break;
            }
        }
        
        return $has;
    }
    
    /**
     * 根据扩展命名空间获取扩展实际目录路径
     *
     * @param string $namespace 命名空间
     *
     * @return string
     */
    public function getExtensionPathByNamespace($namespace)
    {
        $path = $namespace = str_replace('/', '\\', $namespace);
        foreach ($this->getConfigFiles() as $uniqueName => $config) {
            if (strpos($namespace, $config['autoload']['psr-4'][0]) !== false) {
                $path = StringHelper::replace($namespace, $config['autoload']['psr-4'][0], $config['autoload']['psr-4'][1] . '/');
                $path = str_replace('\\', '/', $path);
                break;
            }
        }
        
        return $path;
    }
    
}
