<?php
/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\extension\models\ModuleFunction */
/* @var $id string */
/* @var $dependList array */

$this->title = '管理 ' . $id . ' 功能扩展';
$this->params['breadcrumbs'][] = ['label' => '功能扩展', 'url' => ['/extension/func/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/extension/func/index';
?>
<?=

$this->render('_form', [
    'model' => $model,
    'dependList' => $dependList,
])
?>