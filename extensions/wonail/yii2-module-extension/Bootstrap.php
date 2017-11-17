<?php

namespace wocenter\backend\modules\extension;

use wocenter\Wc;
use Yii;
use yii\{
    base\Application, base\BootstrapInterface, helpers\ArrayHelper
};

/**
 * Class Bootstrap
 * 扩展机制启动器

 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class Bootstrap implements BootstrapInterface
{
    
    /**
     * @var array 执行方法
     */
    public $method = [
        'loadExtensionAliases',
        'loadExtensionConfig',
        'loadUrlRule',
        'loadBootstrap',
    ];
    
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        foreach ($this->method as $method) {
            if (method_exists($this, $method)) {
                $this->$method($app);
            }
        }
    }
    
    /**
     * 加载扩展别名
     *
     * @param Application $app 当前应用
     */
    protected function loadExtensionAliases($app)
    {
        $aliases = Wc::$service->getExtension()->getLoad()->loadAliases();
        foreach ($aliases as $namespacePrefix => $realPath) {
            Yii::setAlias($namespacePrefix, $realPath);
        }
    }
    
    /**
     * 加载扩展配置信息
     *
     * @param Application $app 当前应用
     */
    protected function loadExtensionConfig($app)
    {
        $controllerConfig = Wc::$service->getExtension()->getController()->getConfig();
        // 加载系统模块
        $this->_loadModules(
            Wc::$service->getExtension()->getModularity()->getConfig(),
            ArrayHelper::remove($controllerConfig, 'modules', []),
            $app
        );
        // 加载应用控制器扩展
        $this->_loadControllers(
            ArrayHelper::remove($controllerConfig, 'app', []),
            $app
        );
    }
    
    /**
     * 加载系统模块
     *
     * @param array $moduleConfig 模块配置信息
     * @param array $mcConfig 模块控制器配置信息
     * @param Application $app 当前应用
     */
    private function _loadModules($moduleConfig, $mcConfig, $app)
    {
        foreach ($moduleConfig as $moduleId => &$config) {
            // 加载系统模块
            if (!$app->hasModule($moduleId)) {
                // 为模块添加功能扩展配置信息
                if (isset($mcConfig[$moduleId])) {
                    $config['controllerMap'] = array_merge(
                        $config['controllerMap'] ?? [],
                        $mcConfig[$moduleId]
                    );
                }
                $app->setModule($moduleId, $config);
            }
        }
    }
    
    /**
     * 加载应用控制器扩展
     *
     * @param array $appConfig 应用控制器扩展配置
     * @param Application $app 当前应用
     */
    private function _loadControllers($appConfig, $app)
    {
        foreach ($appConfig as $controllerId => $config) {
            if (!isset($app->controllerMap[$controllerId])) {
                $app->controllerMap[$controllerId] = $config;
            }
        }
    }
    
    /**
     * 加载系统模块路由规则
     *
     * @param Application $app 当前应用
     */
    protected function loadUrlRule($app)
    {
        $app->getUrlManager()->addRules(Wc::$service->getExtension()->getModularity()->getUrlRules());
    }
    
    /**
     * 加载需要启用bootstrap的模块
     *
     * @param Application $app 当前应用
     */
    protected function loadBootstrap($app)
    {
        $app->bootstrap = array_merge($app->bootstrap, Wc::$service->getExtension()->getModularity()->getBootstraps());
    }
    
}
