<?php
/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\account\models\ExtendFieldSetting */
/* @var $profileName string */
/* @var $formTypeList array */

$this->title = $model->field_name;
$this->params['breadcrumbs'][] = ['label' => '扩展档案', 'url' => ['/account/profile/index']];
$this->params['breadcrumbs'][] = ['label' => '【' . $profileName . '】' . '字段管理', 'url' => ['/account/fields', 'profile_id' => $model->profile_id]];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/account/profile/index';
?>

<?= $this->render('_form', [
    'model' => $model,
    'formTypeList' => $formTypeList,
]) ?>
