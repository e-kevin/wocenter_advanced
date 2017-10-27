<?php
/* @var $this yii\web\View */
/* @var $configGroupList array */
/* @var $configTypeList array */

$this->title = '新增配置项';
$this->params['breadcrumbs'][] = ['label' => '配置管理', 'url' => ['/system/config-manager/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/system/config-manager/index';
?>

<?=

$this->render('_form', [
    'model' => $model,
    'configGroupList' => $configGroupList,
    'configTypeList' => $configTypeList,
])
?>