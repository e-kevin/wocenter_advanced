<?php
/* @var $this yii\web\View */
/* @var $model wocenter\backend\modules\account\models\Identity */
/* @var $identityGroup array */
/* @var $profiles array */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '身份列表', 'url' => ['/account/identity/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/account/identity/index';
?>

<?= $this->render('_form', [
    'model' => $model,
    'identityGroup' => $identityGroup,
    'profiles' => $profiles,
])
?>