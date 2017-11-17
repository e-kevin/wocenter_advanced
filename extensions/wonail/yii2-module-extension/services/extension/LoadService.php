<?php

namespace wocenter\backend\modules\extension\services\extension;

use wocenter\core\Service;
use wocenter\helpers\FileHelper;
use wocenter\helpers\StringHelper;
use wocenter\backend\modules\extension\services\ExtensionService;
use wocenter\Wc;
use yii\helpers\ArrayHelper;

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
    public function getConfigFiles()
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
                        $config[$name]['version'] = 'dev';
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
     * todo 只加载已安装的模块
     */
    public function loadAliases()
    {
        return Wc::getOrSet(self::CACHE_ALL_EXTENSION_ALIASES, function () {
            $config = [];
            $configFiles = $this->getConfigFiles();
            foreach ($configFiles as $name => $row) {
                $namespacePrefix = '@' . str_replace('\\', '/', rtrim($row['autoload']['psr-4'][0], '\\'));
                $config[$namespacePrefix] = $row['autoload']['psr-4'][1];
            }
            
            return $config;
        }, $this->cacheDuration, null, 'commonCache');
    }
    
}
