<?php

namespace wocenter\backend\modules\action;

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
    public $id = 'action';
    
    /**
     * @inheritdoc
     */
    public $name = '行为管理';
    
    /**
     * @inheritdoc
     */
    public $description = '管理系统所有行为操作，如：行为管理、行为限制管理';
    
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
            // 安全管理
            [
                'name' => '安全管理',
                'icon_html' => 'shield',
                'modularity' => 'core',
                'show_on_menu' => true,
                'sort_order' => 1003,
                'items' => [
                    [
                        'name' => '行为管理',
                        'icon_html' => 'paw',
                        'url' => "/{$this->id}/manage/index",
                        'show_on_menu' => true,
                        'items' => [
                            ['name' => '列表', 'url' => "/{$this->id}/manage/index", 'description' => '行为列表',],
                            ['name' => '新增', 'url' => "/{$this->id}/manage/create"],
                            ['name' => '编辑', 'url' => "/{$this->id}/manage/update"],
                            ['name' => '删除', 'url' => "/{$this->id}/manage/delete"],
                            ['name' => '搜索', 'url' => "/{$this->id}/manage/search", 'description' => '搜索行为'],
                        ],
                    ],
                    [
                        'name' => '行为限制管理',
                        'icon_html' => 'paw',
                        'url' => "/{$this->id}/limit/index",
                        'show_on_menu' => true,
                        'items' => [
                            ['name' => '列表', 'url' => "/{$this->id}/limit/index", 'description' => '行为限制列表'],
                            ['name' => '新增', 'url' => "/{$this->id}/limit/create"],
                            ['name' => '编辑', 'url' => "/{$this->id}/limit/update"],
                            ['name' => '删除', 'url' => "/{$this->id}/limit/delete"],
                            ['name' => '搜索', 'url' => "/{$this->id}/limit/search", 'description' => '搜索行为限制'],
                        ],
                    ],
                ],
            ],
        ];
    }
    
}
