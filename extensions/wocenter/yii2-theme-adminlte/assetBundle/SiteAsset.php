<?php

namespace wocenter\backend\themes\adminlte\assetBundle;

use yii\web\AssetBundle;

class SiteAsset extends AssetBundle
{
    
    public $depends = [
        'wonail\base\assetBundle\SlimScrollAsset',
        'wonail\adminlte\assetBundle\AdminLteAsset',
    ];
    
}
