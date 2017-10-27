<?php
use wocenter\backend\modules\account\models\IdentityGroup;

/* @var $this yii\web\View */
/* @var $model IdentityGroup */

$this->title = '新增身份分组';
$this->params['breadcrumbs'][] = ['label' => '身份分组', 'url' => ['/account/identity-group/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/account/identity-group/index';
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>