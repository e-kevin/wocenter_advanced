<?php
use wocenter\backend\modules\account\models\Identity;

/* @var $this yii\web\View */
/* @var $model Identity */
/* @var $identityGroup array */
/* @var $profiles array */

$this->title = '新增身份';
$this->params['breadcrumbs'][] = ['label' => '身份列表', 'url' => ['/account/identity/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/account/identity/index';
?>

<?=
$this->render('_form', [
    'model' => $model,
    'identityGroup' => $identityGroup,
    'profiles' => $profiles,
])
?>