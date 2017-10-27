<?php
/* @var $this yii\web\View */
/* @var $model wocenter\backend\modules\account\models\BaseUser */

$this->title = '更新用户信息';
$this->params['breadcrumbs'][] = ['label' => '用户管理', 'url' => ['/account/user/index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/account/user/index';
?>

<?= $this->render('_form', [
    'model' => $model,
])
?>