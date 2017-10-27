<?php

namespace wocenter\backend\modules\gii\generators\module;

use wocenter\core\Controller;
use wocenter\core\View;
use wocenter\helpers\StringHelper;
use wocenter\Wc;
use yii\gii\CodeFile;
use Yii;
use yii\helpers\Html;


/**
 * WoCenter模块生成器
 *
 * @property string $moduleClass 模块类名
 * @property string $defaultRoute 默认路由名
 * @property string $defaultAction 默认操作名
 * @property bool $isCoreModule [[$moduleId]]是否为核心模块
 * @property string $namespace 获取[[$moduleId]]的命名空间
 * @property string $controllerNamespace 获取[[$moduleId]]的控制器命名空间
 * @property array $coreModuleConfig [[$moduleId]]核心模块配置信息，仅在[[$moduleId]]为核心模块时有效
 * @property Controller $defaultController 默认控制器类
 */
class Generator extends \yii\gii\Generator
{
    
    /**
     * @var string 模块ID
     */
    public $moduleID;
    
    /**
     * @var bool 是否使用调度服务，默认为`true`，使用
     */
    public $useDispatch = false;
    
    /**
     * @var string 模块类名
     */
    protected $_moduleClass;
    
    /**
     * @var bool [[$moduleId]]是否为核心模块，默认为`false`，不是，则代表该模块是用户全新开发的模块
     */
    protected $_isCoreModule = false;
    
    /**
     * @var array [[$moduleId]]核心模块配置信息，仅在[[$moduleId]]为核心模块时有效
     */
    protected $_coreModuleConfig = [];
    
    /**
     * @var string 默认路由名
     */
    protected $_defaultRoute = 'common';
    
    /**
     * @var string 默认操作名
     */
    protected $_defaultAction = 'index';
    
    /**
     * @var Controller 默认控制器类
     */
    protected $_defaultController;
    
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'WoCenter模块生成器';
    }
    
    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return '帮助你生成WoCenter系统开发所需的模块';
    }
    
    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['module.php', 'controller.php', 'view.php', 'info.php', 'dispatch.php'];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['moduleID'], 'filter', 'filter' => 'trim'],
            [['moduleID'], 'required'],
            [['moduleID'], 'match', 'pattern' => '/^[\w\\-]+$/', 'message' => 'Only word characters and dashes are allowed.'],
            [['useDispatch'], 'boolean'],
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'moduleID' => 'Module ID',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function hints()
    {
        return [
            'moduleID' => 'This refers to the ID of the module, e.g., <code>admin</code>.',
            'useDispatch' => 'Using system dispatch services.',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function successMessage()
    {
        if (Yii::$app->hasModule($this->moduleID)) {
            $link = Html::a('try it now', Yii::$app->getUrlManager()->createUrl($this->moduleID), ['target' => '_blank']);
            
            return "The module has been generated successfully. You may $link.";
        }
        
        $link = Html::a('Install it now',
            Yii::$app->getUrlManager()->createUrl(['/modularity/manage/install', 'id' => $this->moduleID]),
            ['target' => '_blank']
        );
        
        return "The module has been generated successfully. You may $link.";
    }
    
    /**
     * @inheritdoc
     */
    public function generate()
    {
        /** @var View $view */
        $view = Yii::$app->getView();
        $modularityService = Wc::$service->getExtension()->getModularity();
        $dispatchService = Wc::$service->getDispatch();
        // 开发者模块路径下的模块类名
        $this->_moduleClass = $modularityService->getDeveloperModuleNamespace() . '\\' . $this->moduleID . '\\Module';
        // 开发者模块路径
        $developerModulePath = StringHelper::ns2Path($modularityService->getDeveloperModuleNamespace()) . '/' . $this->moduleID;
        $files = [];
        $this->_isCoreModule = !empty($this->getCoreModuleConfig()) ? true : false;
        // 如果为核心模块，则加载该模块相关配置信息
        if ($this->_isCoreModule) {
            $this->_coreModuleConfig = $this->_coreModuleConfig[$this->moduleID];
            /** @var \yii\base\Module $module */
            $module = Yii::createObject($this->_coreModuleConfig['module']['class'], [$this->moduleID, Yii::$app]);
            $this->_defaultRoute = $module->defaultRoute;
            $this->_defaultController = $module->createControllerByID($module->defaultRoute);
            $this->_defaultAction = $this->_defaultController->defaultAction;
            /** @var \wocenter\core\ModularityInfo $moduleInfoInstance */
            $moduleInfoInstance = $this->_coreModuleConfig['infoInstance']['module'];
            $useInfoClass = "use {$moduleInfoInstance->className()} as baseInfo;";
            $useModuleClass = "use {$module->className()} as baseModule;";
        } else {
            $useInfoClass = 'use wocenter\core\ModularityInfo as baseInfo;';
            $useModuleClass = 'use wocenter\backend\core\Modularity as baseModule;';
        }
        
        $files[] = new CodeFile(
            $developerModulePath . '/Info.php',
            $this->render("info.php", ['useInfoClass' => $useInfoClass])
        );
        $files[] = new CodeFile(
            $developerModulePath . '/Module.php',
            $this->render("module.php", ['useModuleClass' => $useModuleClass])
        );
        /**
         * 开发者模块默认生成控制器，核心模块只需生成调度器即可。
         * WoCenter认为，该模块生成器主要是为了让开发者了解WoCenter调度器服务对于二次开发的友好性以及其存在的重要性，
         * 该调度器服务对于WoCenter日后的开发都是非常重要的。
         * 如果你能灵活运用，相信你会喜欢它的^_^
         */
        if (!$this->_isCoreModule) {
            $files[] = new CodeFile(
                $developerModulePath . '/controllers/' . ucwords($this->_defaultRoute) . 'Controller.php',
                $this->render("controller.php")
            );
        }
        // 是否使用调度服务
        $defaultRoute = $dispatchService->normalizeControllerName($this->_defaultRoute);
        $defaultAction = $dispatchService->normalizeDispatchName($this->_defaultAction);
        if ($this->useDispatch) {
            /**
             * WoCenter在设计之初就已经考虑到对二次开发友好性的重要性，故系统所有的核心模块在响应路由时都交由调度服务来返回执行结果。
             * 正因为调度服务的存在，现在你可以很简单的对系统功能进行二次开发以满足你的需要。
             * 正如这个将要生成的调度器文件，一旦生成，便能马上起到接替原来系统核心功能的作用。
             */
            $files[] = new CodeFile(
                Yii::getAlias($view->getDeveloperThemePath("dispatches/{$this->moduleID}/{$defaultRoute}/{$defaultAction}.php")),
                $this->render("dispatch.php")
            );
        }
        /**
         * 修改系统核心模板的视图文件其实就是这么简单，只需把待修改的视图文件复制到开发者模块主题目录的相同路径下即可,
         * 剩下的便是你的为所欲为^_^
         */
        $defaultAction = $dispatchService->normalizeDispatchViewFileName($this->_defaultAction);
        $files[] = new CodeFile(
            Yii::getAlias($view->getDeveloperThemePath("modules/{$this->moduleID}/views/{$this->_defaultRoute}/{$defaultAction}.php")),
            $this->render("view.php")
        );
        
        $modularityService->clearAllModuleConfig();
        
        return $files;
    }
    
    /**
     * @return string 模块类名
     */
    public function getModuleClass()
    {
        return $this->_moduleClass;
    }
    
    /**
     * @return string 默认路由名
     */
    public function getDefaultRoute()
    {
        return $this->_defaultRoute;
    }
    
    /**
     * @return string 默认操作名
     */
    public function getDefaultAction()
    {
        return $this->_defaultAction;
    }
    
    /**
     * @return bool [[$moduleId]]是否为核心模块
     */
    public function getIsCoreModule()
    {
        return $this->_isCoreModule;
    }
    
    /**
     * 获取[[$moduleId]]核心模块配置信息，仅在[[$moduleId]]为核心模块时有效
     *
     * @return array
     */
    public function getCoreModuleConfig()
    {
        if ($this->_coreModuleConfig == null) {
            $this->_coreModuleConfig = Wc::$service->getExtension()->getModularity()->filterModules(
                Wc::$service->getExtension()->getModularity()->getCoreModuleConfig(),
                [
                    $this->moduleID,
                ]
            );
        }
        
        return $this->_coreModuleConfig;
    }
    
    /**
     * @return Controller 默认控制器类
     */
    public function getDefaultController()
    {
        return $this->_defaultController;
    }
    
    /**
     * @return string 获取[[$moduleId]]的命名空间
     */
    public function getNamespace()
    {
        return substr($this->_moduleClass, 0, strrpos($this->_moduleClass, '\\'));
    }
    
    /**
     * @return string 获取[[$moduleId]]的控制器命名空间
     */
    public function getControllerNamespace()
    {
        return $this->getNamespace() . '\controllers';
    }
    
}
