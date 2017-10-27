<?php
use wocenter\backend\modules\account\models\IdentityGroup;

/* @var $this yii\web\View */
/* @var $model IdentityGroup */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '身份分组', 'url' => ['/account/identity-group']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/account/identity-group/index';
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
