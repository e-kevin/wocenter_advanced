<?php

namespace wocenter\backend\modules\gii;

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
    public $id = 'gii';
    
    /**
     * @inheritdoc
     */
    public $name = '代码生成器';
    
    /**
     * @inheritdoc
     */
    public $description = '提供 WoCenter 系统开发所需的代码生成器';
    
    /**
     * @inheritdoc
     */
    public $bootstrap = true;
    
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
                    [
                        'name' => '基础功能',
                        'icon_html' => 'cogs',
                        'modularity' => 'core',
                        'show_on_menu' => true,
                        'items' => [
                            ['name' => '代码生成器', 'url' => "/{$this->id}/default/index", 'show_on_menu' => true, 'description' => '提供 WoCenter 系统开发所需的代码生成器', 'sort_order' => 100],
                        ],
                    ],
                ],
            ],
        ];
    }
    
}
