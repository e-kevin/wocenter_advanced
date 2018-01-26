<?php

namespace wocenter\backend\modules\extension\services\sub;

use wocenter\{
    core\ModularityInfo, core\Service, Wc
};
use wocenter\backend\modules\extension\{
    models\Module, services\ExtensionService
};
use yii\{
    helpers\ArrayHelper, web\NotFoundHttpException
};
use Yii;

/**
 * 管理系统模块子服务类
 *
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
     * @return array e.g. ['wocenter/yii2-module-account' => '账户管理', 'wocenter/yii2-module-rbac' => '权限管理']
     */
    public function getInstalledSelectList()
    {
        return ArrayHelper::getColumn(
            $this->getAllConfigByApp(true),
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
        // 已经安装的扩展
        $installed = $this->service->getLoad()->getInstalled();
        // 所有模块扩展配置
        $allConfig = $this->getAllConfigByApp();
        // 获取未安装的模块扩展配置
        foreach ($allConfig as $uniqueName => $row) {
            // 剔除已安装的模块
            if (isset($installed[$uniqueName])) {
                unset($allConfig[$uniqueName]);
                continue;
            }
        }
        
        return ArrayHelper::getColumn($allConfig, 'infoInstance.uniqueId');
    }
    
    /**
     * 获取所有模块信息，并以数据库里的配置信息为准，主要用于列表
     *
     * @return array
     */
    public function getModuleList()
    {
        // 已经安装的扩展
        $installed = $this->service->getLoad()->getInstalled();
        $allConfig = $this->getAllConfigByApp();
        foreach ($allConfig as $uniqueName => &$config) {
            /** @var ModularityInfo $infoInstance */
            $infoInstance = $config['infoInstance'];
            // 添加主键
            $config['id'] = $uniqueName;
            // 数据库里存在模块信息
            if (isset($installed[$uniqueName])) {
                $exists = $installed[$uniqueName];
                // 是否为系统模块
                $infoInstance->isSystem = $infoInstance->isSystem || $exists['is_system'];
                // 系统模块不可卸载
                $infoInstance->canUninstall = !$infoInstance->isSystem;
                
                $config['status'] = $exists['status'];
                $config['run'] = $exists['run'];
            } // 数据库不存在数据则
            else {
                $infoInstance->canInstall = true;
                $config['status'] = 0; // 未安装则为禁用状态
                $config['run'] = -1; // 未安装则没有正在运行的模块
            }
        }
        
        return $allConfig;
    }
    
    /**
     * 获取单个详情，主要用于管理和安装模块
     *
     * @param string $extensionName 扩展名称
     * @param boolean $onDataBase 获取数据库数据，默认获取，一般是用于更新扩展信息
     *
     * @return Module
     * @throws NotFoundHttpException
     */
    public function getModuleInfo($extensionName, $onDataBase = true)
    {
        $allConfig = $this->getAllConfigByApp();
        
        if (!isset($allConfig[$extensionName])) {
            throw new NotFoundHttpException('模块扩展不存在');
        }
        
        /** @var ModularityInfo $infoInstance */
        $infoInstance = $allConfig[$extensionName]['infoInstance'];
        
        if ($onDataBase) {
            $model = $this->moduleModel;
            if (($model = $model::findOne(['extension_name' => $extensionName])) == null) {
                throw new NotFoundHttpException('模块扩展暂未安装');
            }
            $model->infoInstance = $infoInstance;
            // 系统模块不可卸载
            $model->infoInstance->canUninstall = !$model->infoInstance->isSystem && !$model->is_system;
        } else {
            /** @var Module $model */
            $model = Yii::createObject([
                'class' => $this->moduleModel,
                'infoInstance' => $infoInstance,
            ]);
            $model->infoInstance->canInstall = true;
            $model->id = $infoInstance->getUniqueId();
            $model->extension_name = $infoInstance->getUniqueName();
            $model->module_id = $infoInstance->id;
            $model->is_system = intval($infoInstance->isSystem);
            $model->run = isset($allModuleParts['developer'][$extensionName])
                ? $this->service::RUN_MODULE_DEVELOPER
                : $this->service::RUN_MODULE_EXTENSION;
            $model->status = 1;
        }
        
        return $model;
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
    }
    
    /**
     * 获取所有已经安装的模块菜单配置数据
     *
     * @return array
     */
    public function getMenus()
    {
        $arr = [];
        foreach ($this->getAllConfigByApp(true) as $row) {
            /* @var $infoInstance ModularityInfo */
            $infoInstance = $row['infoInstance'];
            $arr = ArrayHelper::merge($arr, Wc::$service->getMenu()->formatMenuConfig($infoInstance->getMenus()));
        }
        
        return $arr;
    }
    
    /**
     * 获取当前应用本地[所有|已安装]的模块扩展配置信息
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
        $config = $this->service->getLoad()->getAllConfigByApp($installed)['modules'] ?? [];
        unset($config['config']);
        
        return $config;
    }
    
}
