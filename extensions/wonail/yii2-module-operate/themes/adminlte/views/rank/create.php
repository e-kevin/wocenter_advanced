<?php
/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\operate\models\Rank */

$this->title = Yii::t('wocenter/app', 'Create rank');
$this->params['breadcrumbs'][] = ['label' => Yii::t('wocenter/app', 'Ranks'), 'url' => ['/operate/rank/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/operate/rank/index';
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>
