<?php
/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\menu\models\MenuCategory */

$this->title = '新建菜单分类';
$this->params['breadcrumbs'][] = ['label' => '菜单管理', 'url' => ['/menu/category/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/menu/category/index';
?>

<?= $this->render('_form', [
    'model' => $model,
])
?>