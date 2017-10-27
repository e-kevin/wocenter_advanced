<?php

namespace wocenter\backend\modules\system;

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
    public $id = 'system';
    
    /**
     * @inheritdoc
     */
    public $name = '系统管理';
    
    /**
     * @inheritdoc
     */
    public $description = '提供网站设置、配置管理等';
    
    /**
     * @inheritdoc
     */
    public $isSystem = true;
    
    /**
     * @inheritdoc
     */
    public function getMenus()
    {
        return [
            // 系统管理
            [
                'name' => '系统管理',
                'icon_html' => 'cog',
                'modularity' => 'core',
                'show_on_menu' => true,
                'sort_order' => 1002,
                'items' => [
                    // 网站设置
                    [
                        'name' => '网站设置',
                        'icon_html' => 'sliders',
                        'modularity' => $this->id,
                        'show_on_menu' => true,
                        'items' => [
                            ['name' => '基础配置', 'url' => "/{$this->id}/setting/basic", 'show_on_menu' => true],
                            ['name' => '内容配置', 'url' => "/{$this->id}/setting/content", 'show_on_menu' => true],
                            ['name' => '注册配置', 'url' => "/{$this->id}/setting/register", 'show_on_menu' => true],
                            ['name' => '系统配置', 'url' => "/{$this->id}/setting/config", 'show_on_menu' => true],
                            ['name' => '安全配置', 'url' => "/{$this->id}/setting/security", 'show_on_menu' => true],
                        ],
                    ],
                    [
                        'name' => '基础功能',
                        'icon_html' => 'cogs',
                        'modularity' => 'core',
                        'show_on_menu' => true,
                        'items' => [
                            // 配置管理
                            [
                                'name' => '配置管理',
                                'url' => "/{$this->id}/config-manager/index",
                                'show_on_menu' => true,
                                'sort_order' => 30,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->id}/config-manager/index", 'description' => '配置管理列表'],
                                    ['name' => '新增', 'url' => "/{$this->id}/config-manager/create"],
                                    ['name' => '编辑', 'url' => "/{$this->id}/config-manager/update"],
                                    ['name' => '删除', 'url' => "/{$this->id}/config-manager/delete"],
                                    ['name' => '搜索', 'url' => "/{$this->id}/config-manager/search"],
                                ],
                            ],
                            ['name' => '清理缓存', 'url' => "/{$this->id}/cache/flushCache", 'show_on_menu' => true, 'sort_order' => 100],
                        ],
                    ],
                ],
            ],
        ];
    }
    
}
