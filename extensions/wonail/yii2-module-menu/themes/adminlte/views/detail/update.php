<?php
/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\menu\models\Menu */
/* @var $menuList array */
/* @var $breadcrumbs array 面包屑导航 */
/* @var $title string 当前面包屑标题 */

$this->title = $title;
$this->params['breadcrumbs'] = $breadcrumbs;
$this->params['navSelectPage'] = '/menu/category/index';
?>

<?= $this->render('_form', [
    'model' => $model,
    'menuList' => $menuList,
])
?>