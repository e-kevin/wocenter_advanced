<?php

namespace wocenter\backend\modules\extension;

use wocenter\core\ModularityInfo;

class Info extends ModularityInfo
{
    
    /**
     * @inheritdoc
     */
    public $app = 'backend';
    
    /**
     * @inheritdoc
     */
    public $id = 'extension';
    
    /**
     * @inheritdoc
     */
    public $name = '扩展管理';
    
    /**
     * @inheritdoc
     */
    public $description = '对系统中所有类型的扩展进行管理，包括模块扩展、功能扩展、主题扩展等';
    
    /**
     * @inheritdoc
     */
    protected $depends = [
        'wocenter/yii2-module-system:dev-master',
    ];
    
    /**
     * @inheritdoc
     */
    public function getMenus()
    {
        return [
            // 扩展中心
            [
                'name' => '扩展中心',
                'icon_html' => 'cube',
                'modularity' => 'extension',
                'show_on_menu' => true,
                'sort_order' => 1005,
                'items' => [
                    // 扩展管理
                    [
                        'name' => '扩展管理',
                        'icon_html' => 'cubes',
                        'modularity' => $this->id,
                        'show_on_menu' => true,
                        'items' => [
                            [
                                'name' => '模块管理',
                                'url' => "/{$this->id}/module/index",
                                'description' => '模块管理',
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->id}/module/index"],
                                    ['name' => '卸载', 'url' => "/{$this->id}/module/uninstall"],
                                    ['name' => '安装', 'url' => "/{$this->id}/module/install"],
                                    ['name' => '编辑', 'url' => "/{$this->id}/module/update"],
                                ],
                            ],
                            [
                                'name' => '功能管理',
                                'url' => "/{$this->id}/func/index",
                                'description' => '功能管理',
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->id}/func/index"],
                                    ['name' => '卸载', 'url' => "/{$this->id}/func/uninstall"],
                                    ['name' => '安装', 'url' => "/{$this->id}/func/install"],
                                    ['name' => '编辑', 'url' => "/{$this->id}/func/update"],
                                ],
                            ],
                            ['name' => '清理模块缓存', 'url' => "/{$this->id}/module/clear-cache",
                                'description' => '清理模块缓存', 'show_on_menu' => true,
                            ],
                            ['name' => '清理功能缓存', 'url' => "/{$this->id}/func/clear-cache",
                                'description' => '清理功能缓存', 'show_on_menu' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        return [
            'components' => [
                'extensionService' => [
                    'class' => 'wocenter\backend\modules\extension\services\ExtensionService',
                    'subService' => [
                        'controller' => ['class' => 'wocenter\backend\modules\extension\services\sub\ControllerService'],
                        'modularity' => ['class' => 'wocenter\backend\modules\extension\services\sub\ModularityService'],
                        'load' => ['class' => 'wocenter\backend\modules\extension\services\sub\LoadService'],
                        'theme' => ['class' => 'wocenter\backend\modules\extension\services\sub\ThemeService'],
                        'dependent' => ['class' => 'wocenter\backend\modules\extension\services\sub\DependentService'],
                    ],
                ],
            ]
        ];
    }
    
}
