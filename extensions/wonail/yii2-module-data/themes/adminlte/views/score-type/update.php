<?php
/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\data\models\UserScoreType */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '积分类型', 'url' => ['/data/score-type/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/data/score-type/index';
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>