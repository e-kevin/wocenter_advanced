<?php

namespace wocenter\frontend\themes\basic\assetBundle;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class SiteAsset extends AssetBundle
{
    
    public $sourcePath = '@wocenter/frontend/themes/basic/assets';
    
    public $css = [
        'css/site.css',
    ];
    
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    
}
