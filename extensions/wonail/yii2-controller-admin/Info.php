<?php

namespace wocenter\backend\controllers\admin;

use wocenter\core\FunctionInfo;
use wocenter\interfaces\MenuInterface;

class Info extends FunctionInfo
{
    
    /**
     * @inheritdoc
     */
    public $app = 'backend';
    
    /**
     * @inheritdoc
     */
    public $moduleId = 'account';
    
    /**
     * @inheritdoc
     */
    public $name = '管理员管理';
    
    /**
     * @inheritdoc
     */
    public $description = '对后台管理员进行添加、删除、更新等管理';
    
    /**
     * @inheritdoc
     */
    public function getMenus()
    {
        return [
            // 人事管理
            [
                'name' => '人事管理',
                'icon_html' => 'user',
                'modularity' => 'core',
                'show_on_menu' => true,
                'sort_order' => 1001,
                'items' => [
                    // 用户管理
                    [
                        'name' => '用户管理',
                        'icon_html' => 'users',
                        'modularity' => $this->moduleId,
                        'show_on_menu' => true,
                        'items' => [
                            [
                                'name' => '管理员列表',
                                'url' => "/{$this->moduleId}/{$this->id}/index",
                                'show_on_menu' => true,
                                'created_type' => MenuInterface::CREATE_TYPE_BY_EXTENSION,
                                'items' => [
                                    [
                                        'name' => '列表',
                                        'url' => "/{$this->moduleId}/{$this->id}/index",
                                        'description' => '管理员列表',
                                        'created_type' => MenuInterface::CREATE_TYPE_BY_EXTENSION,
                                    ],
                                    [
                                        'name' => '添加',
                                        'url' => "/{$this->moduleId}/{$this->id}/add",
                                        'description' => '添加管理员',
                                        'created_type' => MenuInterface::CREATE_TYPE_BY_EXTENSION,
                                    ],
                                    [
                                        'name' => '解除',
                                        'url' => "/{$this->moduleId}/{$this->id}/relieve",
                                        'description' => '解除管理员',
                                        'created_type' => MenuInterface::CREATE_TYPE_BY_EXTENSION,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
    
}
