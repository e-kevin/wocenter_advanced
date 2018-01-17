<?php

namespace wocenter\backend\modules\extension\behaviors;

use wocenter\{
    backend\modules\extension\models\Module, backend\modules\extension\models\ModuleFunction,
    db\ActiveRecord, interfaces\ExtensionInterface, Wc
};
use yii\base\Behavior;
use yii\base\ModelEvent;

/**
 * 扩展行为类
 *
 * @property Module|ModuleFunction $owner
 * @author E-Kevin <e-kevin@qq.com>
 */
class ExtensionBehavior extends Behavior
{
    
    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
        ];
    }
    
    /**
     * @param ModelEvent $event
     */
    public function beforeValidate(ModelEvent $event)
    {
        // 检查是否满足扩展依赖关系
        if (!Wc::$service->getExtension()->getDependent()->checkDependencies($this->owner->extension_name)) {
            $this->owner->message = Wc::$service->getExtension()->getDependent()->getInfo();
            $event->isValid = false;
        }
    }
    
    public function afterInsert()
    {
        $this->owner->infoInstance->install(); // 调用扩展内置安装方法
        $this->_syncExtensionData();
    }
    
    /**
     * @param ModelEvent $event
     */
    public function beforeDelete(ModelEvent $event)
    {
        if (!$this->owner->infoInstance->canUninstall) {
            $this->owner->message = $this->owner->extension_name . '扩展属于系统扩展，暂不支持卸载。';
            $event->isValid = false;
        } else {
            $localConfig = Wc::$service->getExtension()->getLoad()->getSimpleLocalConfig(); // 获取本地所有扩展
            $arr = [];
            $i = 1;
            // 获取已经安装的扩展，检测当前扩展是否存在依赖关系
            foreach (Wc::$service->getExtension()->getLoad()->getInstalled() as $uniqueName => $row) {
                if ($uniqueName == $this->owner->extension_name) {
                    continue;
                }
                /** @var ExtensionInterface $infoInstance */
                $infoInstance = $localConfig[$uniqueName]['infoInstance'];
                // 获取依赖关系
                foreach ($infoInstance->getDepends() as $extension) {
                    list($name, $version) = explode(':', $extension);
                    if ($name == $this->owner->extension_name) {
                        $arr[] = $i++ . ') ' . $uniqueName;
                    }
                }
            }
            
            if ($arr) {
                $this->owner->message = "请先解除以下扩展依赖关系再执行当前操作：\n" . implode("\n", $arr);
                $event->isValid = false;
            }
        }
    }
    
    public function afterDelete()
    {
        $this->owner->infoInstance->uninstall(); // 调用扩展内置卸载方法
        $this->_syncExtensionData();
    }
    
    public function afterUpdate()
    {
        $this->_syncExtensionData();
    }
    
    private function _syncExtensionData()
    {
        Wc::$service->getExtension()->getLoad()->clearCache(); // 删除扩展缓存数据
        Wc::$service->getMenu()->syncMenus(); // 同步扩展菜单项
    }
    
}
