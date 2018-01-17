<?php

namespace wocenter\backend\modules\menu;

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
    public $id = 'menu';
    
    /**
     * @inheritdoc
     */
    public $name = '菜单管理';
    
    /**
     * @inheritdoc
     */
    public $description = '提供系统所有的菜单功能支持';
    
    /**
     * @inheritdoc
     */
    protected $depends = [
        'wocenter/yii2-module-extension:dev-master',
    ];
    
    /**
     * @inheritdoc
     */
    public function getMenus()
    {
        return [
            [
                'name' => '系统管理',
                'icon_html' => 'cog',
                'modularity' => 'core',
                'show_on_menu' => true,
                'sort_order' => 1002,
                'items' => [
                    // 基础功能
                    [
                        'name' => '基础功能',
                        'icon_html' => 'cogs',
                        'modularity' => 'core',
                        'show_on_menu' => true,
                        'items' => [
                            // 菜单管理
                            [
                                'name' => '菜单管理',
                                'url' => "/{$this->id}/category/index",
                                'show_on_menu' => true,
                                'sort_order' => 10,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->id}/category/index", 'description' => '菜单分类列表'],
                                    ['name' => '新增', 'url' => "/{$this->id}/category/create", 'description' => '新增菜单分类'],
                                    ['name' => '编辑', 'url' => "/{$this->id}/category/update", 'description' => '编辑菜单分类'],
                                    ['name' => '删除', 'url' => "/{$this->id}/category/delete", 'description' => '删除菜单分类'],
                                    ['name' => '同步', 'url' => "/{$this->id}/category/sync-menus", 'description' => '同步后台菜单'],
                                    [
                                        'name' => '管理',
                                        'url' => "/{$this->id}/detail/index",
                                        'description' => '菜单明细管理',
                                        'items' => [
                                            ['name' => '列表', 'url' => "/{$this->id}/detail/index", 'description' => '菜单列表'],
                                            ['name' => '新增', 'url' => "/{$this->id}/detail/create", 'description' => '新增菜单'],
                                            ['name' => '编辑', 'url' => "/{$this->id}/detail/update", 'description' => '编辑菜单'],
                                        ],
                                    ],
                                ],
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
                'menuService' => [
                    'class' => 'wocenter\backend\modules\menu\services\MenuService',
                ],
            ],
        ];
    }
    
}
