<?php

namespace wocenter\backend\modules\operate;

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
    public $id = 'operate';
    
    /**
     * @inheritdoc
     */
    public $name = '运营管理';
    
    /**
     * @inheritdoc
     */
    public $description = '管理系统所有运营数据，邀请码、头衔等';
    
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
            // 运营管理
            [
                'name' => '运营管理',
                'icon_html' => 'line-chart',
                'modularity' => 'core',
                'show_on_menu' => true,
                'sort_order' => 1004,
                // 邀请码管理
                'items' => [
                    [
                        'name' => '邀请管理',
                        'icon_html' => 'key',
                        'modularity' => $this->id,
                        'show_on_menu' => true,
                        'sort_order' => 10,
                        'items' => [
                            // 类型列表
                            [
                                'name' => '邀请码类型',
                                'url' => "/{$this->id}/invite-type/index",
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->id}/invite-type/index"],
                                    ['name' => '新增', 'url' => "/{$this->id}/invite-type/create"],
                                    ['name' => '编辑', 'url' => "/{$this->id}/invite-type/update"],
                                    ['name' => '删除', 'url' => "/{$this->id}/invite-type/delete"],
                                ],
                            ],
                            [
                                'name' => '邀请码列表',
                                'url' => "/{$this->id}/invite/index",
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->id}/invite/index"],
                                    ['name' => '搜索', 'url' => "/{$this->id}/invite/search"],
                                    ['name' => '生成', 'url' => "/{$this->id}/invite/generate"],
                                    ['name' => '删除', 'url' => "/{$this->id}/invite/delete"],
                                    ['name' => '批量删除', 'url' => "/{$this->id}/invite/batch-delete"],
                                    ['name' => '清空', 'url' => "/{$this->id}/invite/clear", 'description' => '清空无用邀请码（真删除）'],
                                ],
                            ],
                            [
                                'name' => '邀请记录',
                                'url' => "/{$this->id}/invite-log/index",
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->id}/invite-log/index"],
                                    ['name' => '搜索', 'url' => "/{$this->id}/invite-log/search"],
                                    ['name' => '删除', 'url' => "/{$this->id}/invite-log/delete"],
                                    ['name' => '批量删除', 'url' => "/{$this->id}/invite-log/batch-delete"],
                                ],
                            ],
//                            ['name' => '兑换记录', 'url' => "/{$this->id}/invite-buy-log", 'show_on_menu' => true],
//                            ['name' => '邀请人列表', 'url' => "/{$this->id}/invite-user-info", 'show_on_menu' => true],
                        ],
                    ],
                    // 头衔管理
                    [
                        'name' => '头衔管理',
                        'icon_html' => 'mortar-board',
                        'modularity' => $this->id,
                        'show_on_menu' => true,
                        'sort_order' => 20,
                        'items' => [
                            [
                                'name' => '头衔列表',
                                'url' => "/{$this->id}/rank/index",
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->id}/rank/index", 'description' => '头衔列表'],
                                    ['name' => '新增', 'url' => "/{$this->id}/rank/create"],
                                    ['name' => '编辑', 'url' => "/{$this->id}/rank/update"],
                                    ['name' => '删除', 'url' => "/{$this->id}/rank/delete"],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    
}
