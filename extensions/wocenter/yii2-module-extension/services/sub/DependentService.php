<?php

namespace wocenter\backend\modules\extension\services\sub;

use wocenter\{
    backend\modules\extension\services\ExtensionService, core\FunctionInfo, core\ModularityInfo, core\Service,
    core\ThemeInfo, interfaces\ExtensionInterface
};
use yii\base\InvalidConfigException;

/**
 * 扩展依赖服务类
 *
 * @property array $definitions 扩展依赖定义
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class DependentService extends Service
{
    
    /**
     * @var ExtensionService 父级服务类
     */
    public $service;
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'dependent';
    }
    
    /**
     * @var array 扩展依赖定义，用于储存扩展依赖数据，为依赖检测提供数据支持
     */
    private $_definitions;
    
    /**
     * 获取本地所有扩展依赖定义
     *
     * @return array
     */
    public function getDefinitions(): array
    {
        if ($this->_definitions === null) {
            $allConfig = $this->service->getLoad()->getSimpleLocalConfig();
            foreach ($allConfig as $uniqueName => $config) {
                /** @var ModularityInfo|FunctionInfo|ThemeInfo $infoInstance */
                $infoInstance = $config['infoInstance'];
                $this->_definitions[$uniqueName] = [
                    'id' => $infoInstance->getUniqueId(),
                    'version' => $infoInstance->getVersion(),
                    'description' => $infoInstance->description,
                    'name' => $infoInstance->name,
                    'depends' => $infoInstance->getDepends(),
                ];
                foreach ($this->_definitions[$uniqueName]['depends'] as $key => $depend) {
                    list($name, $version) = explode(':', $depend);
                    $this->_definitions[$uniqueName]['depends'][$name] = [
                        'downloaded' => isset($allConfig[$name]),
                        'needVersion' => $version,
                    ];
                    if (isset($allConfig[$name])) {
                        /** @var ModularityInfo|FunctionInfo|ThemeInfo $infoInstance */
                        $infoInstance = $allConfig[$name]['infoInstance'];
                        $this->_definitions[$uniqueName]['depends'][$name]['currentVersion'] = $infoInstance->getVersion();
                        $this->_definitions[$uniqueName]['depends'][$name]['description'] = $infoInstance->description;
                        $this->_definitions[$uniqueName]['depends'][$name]['name'] = $infoInstance->name;
                    } else {
                        $this->_definitions[$uniqueName]['depends'][$name]['currentVersion'] = $version;
                        $this->_definitions[$uniqueName]['depends'][$name]['description'] = 'N/A';
                        $this->_definitions[$uniqueName]['depends'][$name]['name'] = 'N/A';
                    }
                    unset($this->_definitions[$uniqueName]['depends'][$key]);
                }
            }
        }
        
        return $this->_definitions;
    }
    
    /**
     * 获取指定扩展的依赖关系列表
     *
     * @param string $extension 扩展名称
     *
     * @return array
     */
    public function getList(string $extension): array
    {
        $arr = $this->getDefinitions()[$extension]['depends'];
        foreach ($arr as $uniqueName => &$row) {
            $row['installed'] = isset($this->service->getLoad()->getInstalled()[$uniqueName]);
        }
        
        return $arr;
    }
    
    /**
     * 验证版本是否满足
     *
     * @param string $version1
     * @param string $version2
     *
     * @return bool
     */
    public function validateVersion($version1, $version2): bool
    {
        return $version1 == $version2;
    }
    
    /**
     * 检测指定扩展是否满足所需的依赖关系
     *
     * @param string $extension 待检测的扩展名称
     *
     * @return bool
     * @throws InvalidConfigException
     */
    public function checkDependencies($extension): bool
    {
        $allConfig = $this->service->getLoad()->getSimpleLocalConfig();
        if (!isset($allConfig[$extension])) {
            throw new InvalidConfigException("扩展 {$extension} 不存在");
        }
        /** @var ExtensionInterface $infoInstance */
        $infoInstance = $allConfig[$extension]['infoInstance'];
        $this->_data = [
            'download' => [], // 提示下载扩展
            'conflict' => [], // 提示扩展版本冲突
            'install' => [], // 提示需要安装的扩展
            'exists' => [], // 通过依赖检测的扩展
        ];
        $this->loadDependencies($infoInstance->getDepends(), $allConfig, $extension);
        
        if (!empty($this->_data['download']) || !empty($this->_data['conflict']) || !empty($this->_data['install'])) {
            $this->_info = '请先满足扩展依赖关系再执行当前操作。';
            
            return $this->_status;
        } elseif (!empty($this->_info)) {
            return $this->_status;
        } else {
            return $this->_status = true;
        }
    }
    
    /**
     *  加载扩展依赖关系
     *
     * @param array $extensionConfig
     * @param array $list 本地所有已经下载的扩展列表数据
     * @param string $parent 上级扩展名称
     * @param bool $loadDependency 是否递归加载扩展依赖关系
     *
     * @return bool
     */
    protected function loadDependencies(array $extensionConfig, array $list, string $parent, $loadDependency = false)
    {
        foreach ($extensionConfig as $extension) {
            list($uniqueName, $version) = explode(':', $extension);
            if (in_array($extension, $this->_data['download'])) {
                continue;
            }
            // 存在扩展则检测扩展是否通过依赖
            if (isset($list[$uniqueName])) {
                if (!isset($this->_data['exists'][$uniqueName])) {
                    /** @var ExtensionInterface $infoInstance */
                    $infoInstance = $list[$uniqueName]['infoInstance'];
                    // 版本不符合则提示需要解决版本冲突
                    if (!$this->validateVersion($version, $infoInstance->getVersion())) {
                        $this->_data['conflict'][$uniqueName]['currentVersion'] = $infoInstance->getVersion();
                        $this->_data['conflict'][$uniqueName][$parent] = $version;
                    } // 版本一致
                    else {
                        if (!$loadDependency) {
                            $this->_data['exists'][$parent] = false;
                        }
                        $this->_data['exists'][$uniqueName] = false;
                        $this->loadDependencies($infoInstance->getDepends(), $list, $uniqueName, true);
                        unset($this->_data['exists'][$uniqueName]); // 确保被依赖的扩展在前
                        $this->_data['exists'][$uniqueName] = $list[$uniqueName];
                        if (!$loadDependency) {
                            unset($this->_data['exists'][$parent]);
                        }
                    }
                } elseif ($this->_data['exists'][$uniqueName] === false) {
                    $this->_info = "A circular dependency is detected for extension '{$uniqueName}': " . $this->composeCircularDependencyTrace($uniqueName) . '.';
                    
                    return $this->_status = false;
                }
                // 只获取待检测扩展所依赖的扩展是否已经安装，不递归检测更深层次的依赖关系
                if (!$loadDependency && !isset($this->_data['install'][$uniqueName]) && !isset($this->service->getLoad()->getInstalled()[$uniqueName])) {
                    $this->_data['install'][$uniqueName] = false;
                }
            } // 不存在扩展则提示需要下载该扩展
            else {
                $this->_data['download'][] = $extension;
            }
        }
    }
    
    /**
     * 组成扩展依赖关系内循环跟踪信息
     *
     * @param string $circularDependencyName 内循环扩展名称
     *
     * @return string
     */
    private function composeCircularDependencyTrace($circularDependencyName)
    {
        $dependencyTrace = [];
        $startFound = false;
        foreach ($this->_data['exists'] as $uniqueName => $value) {
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
    
}
