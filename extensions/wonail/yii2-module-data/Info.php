<?php

namespace wocenter\backend\modules\data;

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
    public $id = 'data';
    
    /**
     * @inheritdoc
     */
    public $name = '基础数据';
    
    /**
     * @inheritdoc
     */
    public $description = '提供系统所有基础数据的支持，如：区域数据、积分类型';
    
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
                    [
                        'name' => '基础数据',
                        'icon_html' => 'database',
                        'modularity' => 'core',
                        'show_on_menu' => true,
                        'items' => [
                            // 区域管理
                            [
                                'name' => '区域管理',
                                'url' => "/{$this->id}/area-region/index",
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->id}/area-region/index", 'description' => '区域管理列表'],
                                    ['name' => '新增', 'url' => "/{$this->id}/area-region/create"],
                                    ['name' => '编辑', 'url' => "/{$this->id}/area-region/update"],
                                    ['name' => '删除', 'url' => "/{$this->id}/area-region/delete"],
                                    ['name' => '搜索', 'url' => "/{$this->id}/area-region/search"],
                                ],
                            ],
                            // 积分类型
                            [
                                'name' => '积分类型',
                                'url' => "/{$this->id}/score-type/index",
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->id}/score-type/index", 'description' => '积分类型列表'],
                                    ['name' => '新增', 'url' => "/{$this->id}/score-type/create"],
                                    ['name' => '编辑', 'url' => "/{$this->id}/score-type/update"],
                                    ['name' => '删除', 'url' => "/{$this->id}/score-type/delete"],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    
}
