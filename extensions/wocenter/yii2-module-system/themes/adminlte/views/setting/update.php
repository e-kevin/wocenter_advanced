<?php
use wocenter\Wc;
use yii\web\View;

/* @var $this View */
/* @var $id string */

$title = Wc::$service->getSystem()->getConfig()->extra('CONFIG_GROUP_LIST');
$this->title = $title[$id] . '配置';
$this->params['breadcrumbs'][] = '网站设置';
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/' . Yii::$app->requestedRoute;
?>

<?= $this->render('_form', [
    'models' => $models,
])
?>