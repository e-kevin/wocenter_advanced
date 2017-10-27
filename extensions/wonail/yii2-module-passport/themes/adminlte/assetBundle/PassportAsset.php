<?php

namespace wocenter\backend\modules\passport\themes\adminlte\assetBundle;

use yii\web\AssetBundle;

class PassportAsset extends AssetBundle
{
    
    public $sourcePath = '@wocenter/backend/modules/passport/themes/adminlte/assets';
    
    public $css = [
        'css/passport.min.css',
    ];
    
    public $js = [
        'js/passport.min.js',
    ];
    
    public $depends = [
        'wonail\adminlte\assetBundle\AdminLteAsset',
    ];
    
    public function init()
    {
        $view = \Yii::$app->getView();
        $view->getAssetManager()->bundles['wonail\adminlte\assetBundle\AdminLteAsset'] = [
            'js' => [],
        ];
        $view->getAssetManager()->bundles['wonail\adminlte\assetBundle\BaseAdminLteAsset'] = [
            'skin' => null,
        ];
        
        parent::init();
    }
    
}
