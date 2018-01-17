<?php

namespace command\console\controllers;

use wocenter\{
    core\FunctionInfo, core\ModularityInfo, core\ThemeInfo, helpers\FileHelper, interfaces\ExtensionInterface, Wc
};
use yii\{
    base\Controller, base\InvalidConfigException, console\Exception, console\ExitCode, db\Connection, di\Instance,
    helpers\ArrayHelper, helpers\Console
};
use Yii;
use command\core\MigrateController;

/**
 * WoCenter Installation
 */
class InstallationController extends MigrateController
{
    
    /**
     * @inheritdoc
     */
    public $defaultAction = 'start';
    
    /**
     * @var array extend the data table to save the extended information that has been installed
     */
    public $extensionTable = [
        'module' => '{{%viMJHk_module}}',
        'controller' => '{{%viMJHk_module_function}}',
        'theme' => '{{%viMJHk_theme}}',
    ];
    
    /**
     * @var array default extensions that need to be installed
     */
    public $defaultExtension = [
        // modules
        'wonail/yii2-module-extension:dev-master',
        'wonail/yii2-module-system:dev-master',
        'wonail/yii2-module-menu:dev-master',
        // controllers
        'wonail/yii2-controller-site:dev-master',
        'wonail/yii2-frontend-controller-site:dev-master',
        // themes
        'wonail/yii2-theme-adminlte:dev-master',
        'wonail/yii2-frontend-theme-basic:dev-master',
    ];
    
    /**
     * @var string install lock file
     */
    public $installLockFile = '@common/install.lock';
    
    /**
     * @var array Components that WoCenter needs to use when running
     */
    public $mustBeSetComponents = ['db', 'extensionService', 'commonCache'];
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        // 检查组件是否满足
        foreach ($this->mustBeSetComponents as $component) {
            if (!Yii::$app->has($component)) {
                throw new InvalidConfigException("The '{$component}' component for the Installation is required.");
            }
        }
        // 加载扩展别名，确保能够正确加载系统扩展
        foreach (Wc::$service->getExtension()->getLoad()->loadAliases() as $namespacePrefix => $realPath) {
            Yii::setAlias($namespacePrefix, $realPath);
        }
        $this->checkLocalExtensionConfig();
        $this->getExtensionMigrationPath();
    }
    
    /**
     * UnInstall the wocenter project.
     *
     * @throws Exception if the number of the steps specified is less than 1.
     *
     * @return int the status of the action execution. 0 means normal, other values mean abnormal.
     */
    public function actionUninstall()
    {
        if ($this->confirm(
            "Are you sure you want to uninstall the WoCenter project?\nAll data will be lost irreversibly!")) {
            $this->truncateDatabase();
            // 删除锁定文件
            $this->stdout("Delete the install lock file.\n", Console::FG_GREEN);
            @unlink(Yii::getAlias($this->installLockFile));
            // 删除缓存
            $this->stdout("Delete caching.\n", Console::FG_GREEN);
            Wc::$service->getExtension()->getLoad()->clearCache();
            $this->stdout("\n====== Uninstall is successful. ======\n", Console::FG_YELLOW);
        } else {
            $this->stdout('Action was cancelled by user. Nothing has been performed.');
        }
        $this->stdout("\n");
    }
    
    /**
     * Start the installation of the wocenter project.
     *
     * @return int
     */
    public function actionStart()
    {
        $installLockFile = Yii::getAlias($this->installLockFile);
        // 检查是否已经安装
        if (is_file($installLockFile)) {
            // 安装成功，请不要重复安装
            $this->stdout("====== The installation is successful. Please do not repeat the installation. ======\n", Console::FG_YELLOW);
            
            return ExitCode::OK;
        }
        
        // 安装扩展数据库迁移
        if ($this->updateMigration() == ExitCode::UNSPECIFIED_ERROR) {
            return ExitCode::UNSPECIFIED_ERROR;
        }
        
        // 把已安装的扩展写入数据库
        $this->installExtensionInDb();
        
        // 同步菜单
        $this->syncMenus();
        
        // 安装成功，欢迎使用 WoCenter
        $this->stdout("====== Installation is successful. Welcome to use WoCenter. ======\n\n", Console::FG_YELLOW);
        
        // 创建安装锁定文件
        FileHelper::createFile($installLockFile, 'lock');
        
        return ExitCode::OK;
    }
    
    /**
     * Update the migrations
     *
     * @return int
     */
    protected function updateMigration()
    {
        // 更新数据库
        $this->stdout("====== Run migration ======\n\n", Console::FG_YELLOW);
        
        $migrations = $this->getNewMigrations();
        if (empty($migrations)) {
            $this->stdout("No new migrations found. Your system is up-to-date.\n", Console::FG_GREEN);
            
            return ExitCode::OK;
        }
        
        $n = count($migrations);
        $this->stdout("Total $n new " . ($n === 1 ? 'migration' : 'migrations') . " to be applied:\n", Console::FG_YELLOW);
        
        foreach ($migrations as $migration) {
            $this->stdout("\t$migration\n");
        }
        $this->stdout("\n");
        
        $applied = 0;
        foreach ($migrations as $migration) {
            if (!$this->migrateUp($migration)) {
                $this->stdout("\n$applied from $n " . ($applied === 1 ? 'migration was' : 'migrations were') . " applied.\n", Console::FG_RED);
                $this->stdout("\nMigration failed. The rest of the migrations are canceled.\n", Console::FG_RED);
                
                return ExitCode::UNSPECIFIED_ERROR;
            }
            $applied++;
        }
        
        $this->stdout("\n$n " . ($n === 1 ? 'migration was' : 'migrations were') . " applied.\n", Console::FG_GREEN);
        $this->stdout("\nMigrated up successfully.\n", Console::FG_GREEN);
        
        return ExitCode::OK;
    }
    
    /**
     * Synchronize menu data.
     */
    protected function syncMenus()
    {
        // 同步菜单数据
        $this->stdout("\n====== Synchronize menu data ======\n\n", Console::FG_YELLOW);
        
        if (Wc::$service->getMenu()->syncMenus()) {
            $this->stdout("Menu synchronization complete.\n\n", Console::FG_GREEN);
        } else {
            $this->stdout("Menu synchronization failed.\n\n", Console::FG_RED);
        }
    }
    
    /**
     * @var array Extended configuration information that you need to install.
     */
    private $_extensionConfig = [];
    
    /**
     * Getting the extended configuration information
     *
     * @param array $extensionConfig
     * @param array $arr ['download', 'conflict']
     * @param string $loadDependency Whether or not the extension dependencies are loaded recursively,
     * the value is the current extension name.
     *
     * @throws Exception on failure
     */
    private function getExtension($extensionConfig, &$arr, $loadDependency = '')
    {
        foreach ($extensionConfig as $extension) {
            list($uniqueName, $version) = explode(':', $extension);
            if (in_array($extension, $arr['download'])) {
                continue;
            }
            // 存在扩展则检测扩展是否通过依赖
            if (isset($this->_allLocalConfig[$uniqueName])) {
                if (!isset($this->_extensionConfig[$uniqueName])) {
                    /** @var ExtensionInterface $infoInstance */
                    $infoInstance = $this->_allLocalConfig[$uniqueName]['infoInstance'];
                    // 版本不符合则提示需要解决版本冲突
                    if (!Wc::$service->getExtension()->getDependent()->validateVersion($version, $infoInstance->getVersion())) {
                        $arr['conflict'][$uniqueName]['currentVersion'] = $infoInstance->getVersion();
                        $arr['conflict'][$uniqueName][$loadDependency ?: 'installation'] = $version;
                    } // 版本一致，设置默认需要安装的扩展配置
                    else {
                        if (!empty($loadDependency)) {
                            $this->_extensionConfig[$uniqueName] = false;
                        }
                        $this->getExtension($infoInstance->getDepends(), $arr, $uniqueName);
                        unset($this->_extensionConfig[$uniqueName]);
                        $this->_extensionConfig[$uniqueName] = $this->_allLocalConfig[$uniqueName];
                    }
                } elseif ($this->_extensionConfig[$uniqueName] === false) {
                    throw new Exception("A circular dependency is detected for extension '{$uniqueName}': " . $this->composeCircularDependencyTrace($uniqueName) . '.');
                }
            } // 不存在扩展则提示需要下载该扩展
            else {
                $arr['download'][] = $extension;
            }
        }
    }
    
    /**
     * @var array local all extensions
     */
    private $_allLocalConfig;
    
    /**
     * Whether the local extended configuration information is valid.
     *
     * @throws InvalidConfigException
     */
    protected function checkLocalExtensionConfig()
    {
        $this->_allLocalConfig = Wc::$service->getExtension()->getLoad()->getSimpleLocalConfig();
        if (empty($this->_allLocalConfig)) {
            $this->stderr("The following extension is necessary:\n\n", Console::FG_RED, Console::UNDERLINE);
            
            foreach ($this->defaultExtension as $key => $extension) {
                $this->stdout($extension . "\n", Console::FG_YELLOW);
            }
            $this->stdout("\n");
            throw new InvalidConfigException('Empty extension, please download the required extension first.');
        } else {
            $arr = [
                'download' => [], // 提示下载扩展
                'conflict' => [], // 提示扩展版本冲突
            ];
            $this->getExtension($this->defaultExtension, $arr);
            
            // 提示下载扩展
            if (!empty($arr['download'])) {
                $this->stderr("The following extension is necessary:\n\n", Console::FG_RED, Console::UNDERLINE);
                foreach ($arr['download'] as $extension) {
                    $this->stdout($extension . "\n", Console::FG_YELLOW);
                }
                $this->stdout("\n");
                throw new InvalidConfigException('Lack of necessary expansion, please download the required extension first.');
            }
            
            // 提示扩展版本冲突
            if (!empty($arr['conflict'])) {
                $this->stderr("Please solve the following extended version dependency:\n\n", Console::FG_RED, Console::UNDERLINE);
                foreach ($arr['conflict'] as $uniqueName => $item) {
                    $currentVersion = ArrayHelper::remove($item, 'currentVersion');
                    $this->stdout("The current version of the '{$uniqueName}' extension is {$currentVersion} " .
                        "and the following extensions exist in the version conflict.\n");
                    foreach ($item as $uName => $needVersion) {
                        $this->stdout(" - '" . $uName . "' need '{$needVersion}' version.\n", Console::FG_YELLOW);
                    }
                }
                $this->stdout("\n");
                throw new InvalidConfigException('Version conflict, please solve the conflict problem first.');
            }
        }
    }
    
    /**
     * Get the migration path
     */
    protected function getExtensionMigrationPath()
    {
        foreach ($this->_extensionConfig as $uniqueName => $config) {
            /** @var FunctionInfo|ModularityInfo $infoInstance */
            $infoInstance = $config['infoInstance'];
            if (
                is_subclass_of($infoInstance, FunctionInfo::className()) ||
                is_subclass_of($infoInstance, ModularityInfo::className())
            ) {
                $this->migrationPath[] = $infoInstance->getMigrationPath();
            }
        }
    }
    
    /**
     * @inheritdoc
     * @since 2.0.13
     */
    protected function truncateDatabase()
    {
        $db = $this->db;
        $schemas = $db->schema->getTableSchemas();
        
        // First drop all foreign keys,
        foreach ($schemas as $schema) {
            if ($schema->foreignKeys) {
                foreach ($schema->foreignKeys as $name => $foreignKey) {
                    $db->createCommand()->dropForeignKey($name, $schema->name)->execute();
                    $this->stdout("Foreign key $name dropped.\n");
                }
            }
        }
        
        // Then drop the tables:
        foreach ($schemas as $schema) {
            $db->createCommand()->dropTable($schema->name)->execute();
            $this->stdout("Table {$schema->name} dropped.\n");
        }
    }
    
    /**
     * Composes trace info for extension circular dependency.
     *
     * @param string $circularDependencyName name of the extension, which have circular dependency
     *
     * @return string extension circular dependency trace string.
     */
    private function composeCircularDependencyTrace($circularDependencyName)
    {
        $dependencyTrace = [];
        $startFound = false;
        foreach ($this->_extensionConfig as $uniqueName => $value) {
            if ($uniqueName === $circularDependencyName) {
                $startFound = true;
            }
            if ($startFound && $value === false) {
                $dependencyTrace[] = $uniqueName;
            }
        }
        $dependencyTrace[] = $circularDependencyName;
        
        return implode(' -> ', $dependencyTrace);
    }
    
    /**
     * This method is invoked right before an action is to be executed (after all possible filters.)
     * It checks the existence of the [[migrationPath]].
     *
     * @param \yii\base\Action $action the action to be executed.
     *
     * @throws InvalidConfigException if directory specified in migrationPath doesn't exist and action isn't "create".
     * @return bool whether the action should continue to be executed.
     */
    public function beforeAction($action)
    {
        if (Controller::beforeAction($action)) {
            if (empty($this->migrationNamespaces) && empty($this->migrationPath)) {
                throw new InvalidConfigException('At least one of `migrationPath` or `migrationNamespaces` should be specified.');
            }
            
            foreach ($this->migrationNamespaces as $key => $value) {
                $this->migrationNamespaces[$key] = trim($value, '\\');
            }
            
            if (is_array($this->migrationPath)) {
                foreach ($this->migrationPath as $i => $path) {
                    $this->migrationPath[$i] = Yii::getAlias($path);
                }
            } elseif ($this->migrationPath !== null) {
                $path = Yii::getAlias($this->migrationPath);
                if (!is_dir($path)) {
                    if ($action->id !== 'create') {
                        throw new InvalidConfigException("Migration failed. Directory specified in migrationPath doesn't exist: {$this->migrationPath}");
                    }
                    FileHelper::createDirectory($path);
                }
                $this->migrationPath = $path;
            }
            
            $version = Wc::getVersion();
            $this->stdout("Installation Tool (based on WoCenter v{$version})\n\n");
            $this->db = Instance::ensure($this->db, Connection::className());
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Install extensions into the database.
     */
    protected function installExtensionInDb()
    {
        // 构建待写入数据库里的扩展配置数据
        $data = [];
        foreach ($this->_extensionConfig as $uniqueName => $config) {
            /** @var FunctionInfo|ModularityInfo|ThemeInfo $infoInstance */
            $infoInstance = $config['infoInstance'];
            switch (true) {
                case is_subclass_of($infoInstance, FunctionInfo::className()):
                    $data['controller'][] = [
                        'id' => $infoInstance->getUniqueId(),
                        'extension_name' => $infoInstance->getUniqueName(),
                        'module_id' => $infoInstance->getModuleId(),
                        'controller_id' => $infoInstance->id,
                        'is_system' => 1, // 默认安装的扩展标记为系统扩展
                        'status' => 1,
                    ];
                    break;
                case is_subclass_of($infoInstance, ModularityInfo::className()):
                    $data['module'][] = [
                        'id' => $infoInstance->getUniqueId(),
                        'extension_name' => $infoInstance->getUniqueName(),
                        'module_id' => $infoInstance->id,
                        'is_system' => 1, // 默认安装的扩展标记为系统扩展
                        'status' => 1,
                        'run' => 0,
                    ];
                    break;
                case is_subclass_of($infoInstance, ThemeInfo::className()):
                    $data['theme'][] = [
                        'id' => $infoInstance->getUniqueId(),
                        'extension_name' => $infoInstance->getUniqueName(),
                        'is_system' => 1, // 默认安装的扩展标记为系统扩展
                        'status' => 1,
                    ];
                    break;
            }
        }
        foreach ($data as $table => $row) {
            $this->db->createCommand()
                ->batchInsert($this->extensionTable[$table], array_keys($row[0]), $data[$table])
                ->execute();
        }
        // 执行扩展内安装方法
        $this->runExtensionInstall();
    }
    
    /**
     * Execution extension internal installation method.
     */
    private function runExtensionInstall()
    {
        foreach ($this->_extensionConfig as $uniqueName => $config) {
            /** @var ExtensionInterface $infoInstance */
            $infoInstance = $config['infoInstance'];
            $infoInstance->install();
        }
        // 删除扩展缓存数据，确保缓存数据最新
        Wc::$service->getExtension()->getLoad()->clearCache();
    }
    
}
