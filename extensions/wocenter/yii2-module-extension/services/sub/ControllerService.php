<?php

namespace wocenter\backend\modules\extension\services\sub;

use wocenter\{
    core\FunctionInfo, core\Service, Wc
};
use wocenter\backend\modules\extension\{
    models\ModuleFunction, services\ExtensionService
};
use yii\{
    helpers\ArrayHelper, web\NotFoundHttpException
};
use Yii;

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
    public function getControllerList()
    {
        // 已经安装的扩展
        $installed = $this->service->getLoad()->getInstalled();
        $allConfig = $this->getAllConfigByApp();
        foreach ($allConfig as $uniqueName => &$config) {
            /** @var FunctionInfo $infoInstance */
            $infoInstance = $config['infoInstance'];
            // 添加主键
            $config['id'] = $uniqueName;
            // 数据库里存在功能扩展信息则标识功能扩展已安装
            if (isset($installed[$uniqueName])) {
                $exists = $installed[$uniqueName];
                // 是否为系统模块
                $infoInstance->isSystem = $infoInstance->isSystem || $exists['is_system'];
                // 系统模块不可卸载
                $infoInstance->canUninstall = !$infoInstance->isSystem;
                
                $config['status'] = $exists['status'];
                $config['run'] = $exists['run'];
            } else {
                // 数据库不存在数据则可以进行安装
                $infoInstance->canInstall = true;
                $config['status'] = 0; // 未安装则为禁用状态
                $config['run'] = -1; // 未安装则没有正在运行的模块
            }
        }
        
        return $allConfig;
    }
    
    /**
     * 获取单个扩展详情，主要用于管理和安装
     *
     * @param string $extensionName 扩展名称
     * @param boolean $onDataBase 获取数据库数据，默认获取，一般是用于更新扩展信息
     *
     * @return ModuleFunction
     * @throws NotFoundHttpException
     */
    public function getControllerInfo($extensionName, $onDataBase = true)
    {
        $allConfig = $this->getAllConfigByApp();
        
        if (!isset($allConfig[$extensionName])) {
            throw new NotFoundHttpException('功能扩展不存在');
        }
        
        /** @var FunctionInfo $infoInstance */
        $infoInstance = $allConfig[$extensionName]['infoInstance'];
        
        if ($onDataBase) {
            $model = $this->moduleFunctionModel;
            if (($model = $model::findOne(['extension_name' => $extensionName])) == null) {
                throw new NotFoundHttpException('功能扩展暂未安装');
            }
            $model->infoInstance = $infoInstance;
            // 系统模块不可卸载
            $model->infoInstance->canUninstall = !$model->infoInstance->isSystem && !$model->is_system;
        } else {
            /** @var ModuleFunction $model */
            $model = Yii::createObject([
                'class' => $this->moduleFunctionModel,
                'infoInstance' => $infoInstance,
            ]);
            $model->infoInstance->canInstall = true;
            $model->id = $infoInstance->getUniqueId();
            $model->extension_name = $infoInstance->getUniqueName();
            $model->module_id = $model->infoInstance->getModuleId();
            $model->controller_id = $model->infoInstance->id;
            $model->is_system = intval($infoInstance->isSystem);
            $model->status = 1;
            
        }
        
        return $model;
    }
    
    /**
     * 删除缓存
     */
    public function clearCache()
    {
        $this->service->getLoad()->clearCache();
    }
    
    /**
     * 获取当前应用本地[所有|已安装]的功能扩展配置信息
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
        $config = $this->service->getLoad()->getAllConfigByApp($installed)['controllers'] ?? [];
        unset($config['config']);
        
        return $config;
    }
    
    /**
     * 获取功能扩展菜单配置数据
     *
     * @return array
     */
    public function getMenus()
    {
        $arr = [];
        foreach ($this->getAllConfigByApp(true) as $uniqueName => $row) {
            /* @var $infoInstance FunctionInfo */
            $infoInstance = $row['infoInstance'];
            $arr = ArrayHelper::merge($arr, Wc::$service->getMenu()->formatMenuConfig($infoInstance->getMenus()));
        }
        
        return $arr;
    }
    
}
