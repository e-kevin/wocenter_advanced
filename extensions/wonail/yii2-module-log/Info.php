<?php

namespace wocenter\backend\modules\log;

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
    public $id = 'log';
    
    /**
     * @inheritdoc
     */
    public $name = '日志管理';
    
    /**
     * @inheritdoc
     */
    public $description = '管理系统所有日志，如：行为日志、积分日志';
    
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
                        'name' => '日志管理',
                        'icon_html' => 'list',
                        'modularity' => $this->id,
                        'show_on_menu' => true,
                        'items' => [
                            // 行为日志
                            [
                                'name' => '行为日志',
                                'url' => "/{$this->id}/action/index",
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->id}/action/index", 'description' => '行为日志列表'],
                                    ['name' => '删除', 'url' => "/{$this->id}/action/delete"],
                                    ['name' => '批量删除', 'url' => "/{$this->id}/action/batch-delete"],
                                    ['name' => '搜索', 'url' => "/{$this->id}/action/search", 'description' => '搜索行为日志'],
                                ],
                            ],
                            // 奖罚日志
                            [
                                'name' => '奖罚日志',
                                'url' => "/{$this->id}/score/index",
                                'show_on_menu' => true,
                                'items' => [
                                    ['name' => '列表', 'url' => "/{$this->id}/score/index", 'description' => '奖罚日志列表'],
                                    ['name' => '搜索', 'url' => "/{$this->id}/score/search", 'description' => '搜索奖罚日志'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    
}
