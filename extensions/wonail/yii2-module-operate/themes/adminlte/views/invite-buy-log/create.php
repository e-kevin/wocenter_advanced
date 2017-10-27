<?php

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\operate\models\InviteBuyLog */

$this->title = Yii::t('wocenter/app', 'Create Invite Buy Log');
$this->params['breadcrumbs'][] = ['label' => Yii::t('wocenter/app', 'Invite Buy Logs'), 'url' => ['/operate/invite-buy-log/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/operate/invite-buy-log';
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>