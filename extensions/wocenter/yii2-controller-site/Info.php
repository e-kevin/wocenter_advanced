<?php

namespace wocenter\backend\controllers\site;

use wocenter\core\FunctionInfo;

class Info extends FunctionInfo
{
    
    /**
     * @inheritdoc
     */
    public $app = 'backend';
    
    /**
     * @inheritdoc
     */
    protected $moduleId = '';
    
    /**
     * @inheritdoc
     */
    public $name = '首页';
    
    /**
     * @inheritdoc
     */
    public $description = '提供后台首页访问、错误控制器等操作';
    
    /**
     * @inheritdoc
     */
    protected $depends = [
        'wocenter/yii2-module-extension:dev-master',
    ];
    
    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        return [
            'components' => [
                'urlManager' => [
                    'rules' => [
                        '' => "{$this->id}/index",
                    ],
                ],
                'errorHandler' => [
                    'errorAction' => "{$this->id}/error",
                ],
            ],
        ];
    }
    
}
