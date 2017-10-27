<?php

namespace wocenter\backend\modules\passport;

use wocenter\core\Modularity;

class Module extends Modularity
{
    
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'wocenter\backend\modules\passport\controllers';
    
    /**
     * @inheritdoc
     */
    public $layout = 'passport';
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        /** @var \wocenter\core\View $view */
        $view = \Yii::$app->getView();
        $this->setLayoutPath($this->getBasePath() . '/themes/' . $view->getThemeName() . '/views/layouts');
    }
    
}
