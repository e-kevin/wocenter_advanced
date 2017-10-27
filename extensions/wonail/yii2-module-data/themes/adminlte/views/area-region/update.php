<?php
/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\data\models\AreaRegion */
/* @var array $areaSelectList */
/* @var $breadcrumbs array 面包屑导航 */
/* @var $title string 当前面包屑标题 */

$this->title = $title;
$this->params['breadcrumbs'] = $breadcrumbs;
$this->params['navSelectPage'] = '/data/area-region/index';
?>

<?= $this->render('_form', [
    'model' => $model,
    'areaSelectList' => $areaSelectList
]) ?>