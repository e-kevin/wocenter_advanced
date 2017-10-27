<?php
/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\action\models\ActionLimit */
/* @var $actionList array */

$this->title = '新增行为限制';
$this->params['breadcrumbs'][] = ['label' => '行为限制列表', 'url' => ['/action/limit/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/action/limit/index';
?>

<?= $this->render('_form', [
    'model' => $model,
    'actionList' => $actionList,
]) ?>