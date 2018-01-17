<?php
use wocenter\backend\modules\system\models\ConfigSearch;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model ConfigSearch */
/* @var $configGroupList array */
/* @var $configTypeList array */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '配置管理', 'url' => ['/system/config-manager/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/system/config-manager/index';
?>

<?php \wonail\adminlte\widgets\Box::begin([
    'type' => \wonail\adminlte\AdminLTE::TYPE_PRIMARY,
    'leftToolbar' => '{goback}',
]) ?>

<?=

DetailView::widget([
    'options' => ['class' => 'table table-striped table-hover detail-view'],
    'model' => $model,
    'attributes' => [
        'id',
        'name',
        'title',
        [
            'attribute' => 'remark',
            'format' => 'html',
            'value' => nl2br($model->remark),
        ],
        'value',
        [
            'attribute' => 'extra',
            'format' => 'html',
            'value' => nl2br($model->extra),
        ],
        [
            'attribute' => 'category_group',
            'value' => $configGroupList[$model->category_group],
        ],
        [
            'attribute' => 'type',
            'value' => $configTypeList[$model->type],
        ],
        'updated_at:datetime',
        'created_at:datetime',
    ],
])
?>

<?php \wonail\adminlte\widgets\Box::end() ?>
