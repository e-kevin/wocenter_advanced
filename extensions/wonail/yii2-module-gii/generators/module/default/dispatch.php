<?php

use wocenter\core\View;
use wocenter\Wc;

/* @var $this yii\web\View */
/* @var View $view */
/* @var $generator wocenter\backend\modules\gii\generators\module\Generator */

$view = Yii::$app->getView();
$dispatch = substr(str_replace('/', '\\', $view->getCoreThemePath('components/Dispatch')), 1);
$ns = substr(str_replace('/', '\\',
    $view->getDeveloperThemePath('dispatches/' .
        $generator->moduleID . '/' .
        Wc::$service->getDispatch()->normalizeControllerName($generator->getDefaultRoute()))
), 1);
$dispatchId = Wc::$service->getDispatch()->normalizeDispatchName($generator->getDefaultAction());

echo "<?php\n";
?>
namespace <?= $ns ?>;

use <?= $dispatch ?>;

/**
* Class <?= $dispatchId . "\n" ?>
*
* @package <?= $ns . "\n" ?>
*/
class <?= $dispatchId ?> extends Dispatch
{

/**
* @return string|\yii\web\Response
*/
public function run()
{
return $this->display();
}

}
