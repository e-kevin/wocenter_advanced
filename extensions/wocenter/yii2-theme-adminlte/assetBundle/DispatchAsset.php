<?php

namespace wocenter\backend\themes\adminlte\assetBundle;

use Yii;
use yii\web\AssetBundle;

class DispatchAsset extends AssetBundle
{
    
    public $depends = [
        'wonail\adminlte\assetBundle\AdminLteAsset',
    ];
    
    public function init()
    {
        parent::init();
        
        $this->registerJs();
    }
    
    public function registerJs()
    {
        $js = <<<JS
        var wait = document.getElementById('wait'),
            href = document.getElementById('href').href;

        var interval = setInterval(function(){
            var time = --wait.innerHTML;
            if(time <= 0) {
                if ($.support.pjax && href.indexOf('javascript') == -1) {
                    $.pjax({url: href, container: '#content-wrapper', timeout: 4000});
                } else {
                    location.href = href;
                }
                clearInterval(interval);
            };
        }, 1000);
        window.stopJumpUrl = function (){
            clearInterval(interval);
        }
JS;
        $view = Yii::$app->getView();
        $view->registerJs($js);
    }
    
}
