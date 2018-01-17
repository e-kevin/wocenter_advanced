<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace command\core;

use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\console\Controller;
use yii\db\MigrationInterface;
use yii\helpers\Console;
use yii\helpers\FileHelper;

/**
 * BaseMigrateController is the base class for migrate controllers.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
abstract class BaseMigrateController extends Controller
{
    /**
     * The name of the dummy migration that marks the beginning of the whole migration history.
     */
    const BASE_MIGRATION = 'm000000_000000_base';
    
    /**
     * @var string the default command action.
     */
    public $defaultAction = 'up';
    /**
     * @var string|array the directory containing the migration classes. This can be either
     * a [path alias](guide:concept-aliases) or a directory path.
     *
     * Migration classes located at this path should be declared without a namespace.
     * Use [[migrationNamespaces]] property in case you are using namespaced migrations.
     *
     * If you have set up [[migrationNamespaces]], you may set this field to `null` in order
     * to disable usage of migrations that are not namespaced.
     *
     * Since version 2.0.12 you may also specify an array of migration paths that should be searched for
     * migrations to load. This is mainly useful to support old extensions that provide migrations
     * without namespace and to adopt the new feature of namespaced migrations while keeping existing migrations.
     *
     * In general, to load migrations from different locations, [[migrationNamespaces]] is the preferable solution
     * as the migration name contains the origin of the migration in the history, which is not the case when
     * using multiple migration paths.
     *
     * @see $migrationNamespaces
     */
    public $migrationPath = [];
    /**
     * @var array list of namespaces containing the migration classes.
     *
     * Migration namespaces should be resolvable as a [path alias](guide:concept-aliases) if prefixed with `@`, e.g. if you specify
     * the namespace `app\migrations`, the code `Yii::getAlias('@app/migrations')` should be able to return
     * the file path to the directory this namespace refers to.
     * This corresponds with the [autoloading conventions](guide:concept-autoloading) of Yii.
     *
     * For example:
     *
     * ```php
     * [
     *     'app\migrations',
     *     'some\extension\migrations',
     * ]
     * ```
     *
     * @since 2.0.10
     * @see $migrationPath
     */
    public $migrationNamespaces = [];
    /**
     * @var string the template file for generating new migrations.
     * This can be either a [path alias](guide:concept-aliases) (e.g. "@app/migrations/template.php")
     * or a file path.
     */
    public $templateFile;
    /**
     * @var bool indicates whether the console output should be compacted.
     * If this is set to true, the individual commands ran within the migration will not be output to the console.
     * Default is false, in other words the output is fully verbose by default.
     * @since 2.0.13
     */
    public $compact = false;
    
    
    /**
     * @inheritdoc
     */
    public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            ['migrationPath', 'migrationNamespaces', 'compact'], // global for all actions
            $actionID === 'create' ? ['templateFile'] : [] // action create
        );
    }
    
    /**
     * This method is invoked right before an action is to be executed (after all possible filters.)
     * It checks the existence of the [[migrationPath]].
     * @param \yii\base\Action $action the action to be executed.
     * @throws InvalidConfigException if directory specified in migrationPath doesn't exist and action isn't "create".
     * @return bool whether the action should continue to be executed.
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
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
        
            $version = Yii::getVersion();
            $this->stdout("Yii Migration Tool (based on Yii v{$version})\n\n");
        
            return true;
        }
    
        return false;
    }
    
    /**
     * Returns the file path matching the give namespace.
     * @param string $namespace namespace.
     * @return string file path.
     * @since 2.0.10
     */
    private function getNamespacePath($namespace)
    {
        return str_replace('/', DIRECTORY_SEPARATOR, Yii::getAlias('@' . str_replace('\\', '/', $namespace)));
    }
    
    /**
     * Upgrades with the specified migration class.
     * @param string $class the migration class name
     * @return bool whether the migration is successful
     */
    protected function migrateUp($class)
    {
        if ($class === self::BASE_MIGRATION) {
            return true;
        }
        
        $this->stdout("*** applying $class\n", Console::FG_YELLOW);
        $start = microtime(true);
        $migration = $this->createMigration($class);
        if ($migration->up() !== false) {
            $this->addMigrationHistory($class);
            $time = microtime(true) - $start;
            $this->stdout("*** applied $class (time: " . sprintf('%.3f', $time) . "s)\n\n", Console::FG_GREEN);
            
            return true;
        }
        
        $time = microtime(true) - $start;
        $this->stdout("*** failed to apply $class (time: " . sprintf('%.3f', $time) . "s)\n\n", Console::FG_RED);
        
        return false;
    }
    
    /**
     * Downgrades with the specified migration class.
     * @param string $class the migration class name
     * @return bool whether the migration is successful
     */
    protected function migrateDown($class)
    {
        if ($class === self::BASE_MIGRATION) {
            return true;
        }
        
        $this->stdout("*** reverting $class\n", Console::FG_YELLOW);
        $start = microtime(true);
        $migration = $this->createMigration($class);
        if ($migration->down() !== false) {
            $this->removeMigrationHistory($class);
            $time = microtime(true) - $start;
            $this->stdout("*** reverted $class (time: " . sprintf('%.3f', $time) . "s)\n\n", Console::FG_GREEN);
            
            return true;
        }
        
        $time = microtime(true) - $start;
        $this->stdout("*** failed to revert $class (time: " . sprintf('%.3f', $time) . "s)\n\n", Console::FG_RED);
        
        return false;
    }
    
    /**
     * Creates a new migration instance.
     * @param string $class the migration class name
     * @return \yii\db\MigrationInterface the migration instance
     */
    protected function createMigration($class)
    {
        $this->includeMigrationFile($class);
        
        /** @var MigrationInterface $migration */
        $migration = Yii::createObject($class);
        if ($migration instanceof BaseObject && $migration->canSetProperty('compact')) {
            $migration->compact = $this->compact;
        }
        
        return $migration;
    }
    
    /**
     * Includes the migration file for a given migration class name.
     *
     * This function will do nothing on namespaced migrations, which are loaded by
     * autoloading automatically. It will include the migration file, by searching
     * [[migrationPath]] for classes without namespace.
     * @param string $class the migration class name.
     * @since 2.0.12
     */
    protected function includeMigrationFile($class)
    {
        $class = trim($class, '\\');
        if (strpos($class, '\\') === false) {
            if (is_array($this->migrationPath)) {
                foreach ($this->migrationPath as $path) {
                    $file = $path . DIRECTORY_SEPARATOR . $class . '.php';
                    if (is_file($file)) {
                        require_once $file;
                        break;
                    }
                }
            } else {
                $file = $this->migrationPath . DIRECTORY_SEPARATOR . $class . '.php';
                require_once $file;
            }
        }
    }
    
    /**
     * Returns the migrations that are not applied.
     * @return array list of new migrations
     */
    protected function getNewMigrations()
    {
        $applied = [];
        foreach ($this->getMigrationHistory(null) as $class => $time) {
            $applied[trim($class, '\\')] = true;
        }
        
        $migrationPaths = [];
        if (is_array($this->migrationPath)) {
            foreach ($this->migrationPath as $path) {
                $migrationPaths[] = [$path, ''];
            }
        } elseif (!empty($this->migrationPath)) {
            $migrationPaths[] = [$this->migrationPath, ''];
        }
        foreach ($this->migrationNamespaces as $namespace) {
            $migrationPaths[] = [$this->getNamespacePath($namespace), $namespace];
        }
        
        $migrations = [];
        foreach ($migrationPaths as $item) {
            list($migrationPath, $namespace) = $item;
            if (!file_exists($migrationPath)) {
                continue;
            }
            $handle = opendir($migrationPath);
            while (($file = readdir($handle)) !== false) {
                if ($file === '.' || $file === '..') {
                    continue;
                }
                $path = $migrationPath . DIRECTORY_SEPARATOR . $file;
                if (preg_match('/^(m(\d{6}_?\d{6})\D.*?)\.php$/is', $file, $matches) && is_file($path)) {
                    $class = $matches[1];
                    if (!empty($namespace)) {
                        $class = $namespace . '\\' . $class;
                    }
                    $time = str_replace('_', '', $matches[2]);
                    if (!isset($applied[$class])) {
                        $migrations[$time . '\\' . $class] = $class;
                    }
                }
            }
            closedir($handle);
        }
        ksort($migrations);
        
        return array_values($migrations);
    }
    
    /**
     * Generates new migration source PHP code.
     * Child class may override this method, adding extra logic or variation to the process.
     * @param array $params generation parameters, usually following parameters are present:
     *
     *  - name: string migration base name
     *  - className: string migration class name
     *
     * @return string generated PHP code.
     * @since 2.0.8
     */
    protected function generateMigrationSourceCode($params)
    {
        return $this->renderFile(Yii::getAlias($this->templateFile), $params);
    }
    
    /**
     * Truncates the database.
     * This method should be overwritten in subclasses to implement the task of clearing the database.
     * @throws NotSupportedException if not overridden
     * @since 2.0.13
     */
    protected function truncateDatabase()
    {
        throw new NotSupportedException('This command is not implemented in ' . get_class($this));
    }
    
    /**
     * Return the maximum name length for a migration.
     *
     * Subclasses may override this method to define a limit.
     * @return int|null the maximum name length for a migration or `null` if no limit applies.
     * @since 2.0.13
     */
    protected function getMigrationNameLimit()
    {
        return null;
    }
    
    /**
     * Returns the migration history.
     * @param int $limit the maximum number of records in the history to be returned. `null` for "no limit".
     * @return array the migration history
     */
    abstract protected function getMigrationHistory($limit);
    
    /**
     * Adds new migration entry to the history.
     * @param string $version migration version name.
     */
    abstract protected function addMigrationHistory($version);
    
    /**
     * Removes existing migration from the history.
     * @param string $version migration version name.
     */
    abstract protected function removeMigrationHistory($version);
}
