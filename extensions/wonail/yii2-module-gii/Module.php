<?php

namespace wocenter\backend\modules\gii;

use yii\gii\Module as baseModule;

class Module extends baseModule
{
    
    /**
     * @inheritdoc
     */
    public $generators = [
        'wocenter_module' => [
            'class' => 'wocenter\backend\modules\gii\generators\module\Generator',
        ],
    ];
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setBasePath('@vendor/yiisoft/yii2-gii');
    }
    
}
