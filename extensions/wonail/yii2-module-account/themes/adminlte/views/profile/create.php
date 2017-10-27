<?php
/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\account\models\ExtendProfile */

$this->title = '新增扩展档案';
$this->params['breadcrumbs'][] = ['label' => '扩展档案', 'url' => ['/account/profile/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/account/profile/index';
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
