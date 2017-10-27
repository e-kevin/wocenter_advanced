<?php
/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\extension\models\Module */
/* @var $validRunModuleList array */
/* @var $id string */

$this->title = '安装 ' . $id . ' 模块';
$this->params['breadcrumbs'][] = ['label' => '模块管理', 'url' => ['/extension/module/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/extension/module/index';
?>
<?=

$this->render('_form', [
    'model' => $model,
    'validRunModuleList' => $validRunModuleList,
])
?>