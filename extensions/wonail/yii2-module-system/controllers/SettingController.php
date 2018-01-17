<?php

namespace wocenter\backend\modules\system\controllers;

use wocenter\core\Controller;

/**
 * Class SettingController
 */
class SettingController extends Controller
{
    
    /**
     * @inheritdoc
     */
    public function dispatches()
    {
        return [
            // 基本配置
            'basic' => [
                'dispatchOptions' => [
                    'map' => 'update',
                ],
            ],
            // 内容配置
            'content' => [
                'dispatchOptions' => [
                    'map' => 'update',
                ],
                'group' => 2,
            ],
            // 注册配置
            'register' => [
                'dispatchOptions' => [
                    'map' => 'update',
                ],
                'group' => 3,
            ],
            // 系统配置
            'config' => [
                'dispatchOptions' => [
                    'map' => 'update',
                ],
                'group' => 4,
            ],
            // 安全配置
            'security' => [
                'dispatchOptions' => [
                    'map' => 'update',
                ],
                'group' => 5,
            ],
        ];
    }
    
}
