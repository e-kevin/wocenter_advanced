<?php
/* @var $this yii\web\View */
/* @var $model wocenter\backend\modules\account\models\BackendUser */
/* @var $showStatus boolean */

$this->title = '更新管理员';
$this->params['breadcrumbs'][] = ['label' => '管理员列表', 'url' => ['/account/admin/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/account/admin/index';
?>

<?= $this->render('_form', [
    'model' => $model,
    'showStatus' => $showStatus,
]) ?>