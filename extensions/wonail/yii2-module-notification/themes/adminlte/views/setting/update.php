<?php
/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\notification\models\Notify */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '通知管理', 'url' => ['/notification/setting/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/notification/setting/index';
?>
<?=

$this->render('_form', [
    'model' => $model,
])
?>