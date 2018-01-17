<?php
use backend\core\ActiveDataProvider;
use wocenter\backend\modules\system\models\ConfigSearch;
use wonail\adminlte\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel ConfigSearch */
/* @var $dataProvider ActiveDataProvider */
/* @var $configGroupList array */
/* @var $configTypeList array */

$headerToolbar = '';
?>

<?php

// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = '配置管理';
    $this->params['navSelectPage'] = '/system/config-manager/index';
    $this->params['breadcrumbs'][] = $this->title;
    $headerToolbar = Html::a('<i class="fa fa-plus"></i> <span class="hidden-xs">' . Yii::t('wocenter/app', 'New add') . '</span>', [
        'create',
    ], [
        'class' => 'btn btn-success',
        'data-pjax' => 1,
    ]);
}
// full_page:END
?>

<?php
$column = [
    [
        'attribute' => 'title',
        'format' => 'raw',
        'value' => function ($model) {
            return Html::a($model->title, ['view', 'id' => $model->id], ['data-pjax' => 1]);
        },
    ],
    'name',
    [
        'format' => 'html',
        'attribute' => 'remark',
        'value' => function ($model) {
            return nl2br($model->remark);
        },
    ],
    'rule',
    [
        'attribute' => 'type',
        'value' => function ($model) use ($configTypeList) {
            return $configTypeList[$model->type];
        },
    ],
    [
        'attribute' => 'category_group',
        'value' => function ($model) use ($configGroupList) {
            return $configGroupList[$model->category_group];
        },
    ],
    'sort_order',
    [
        'class' => 'kartik\grid\BooleanColumn',
        'attribute' => 'status',
    ],
    ['class' => \wonail\adminlte\grid\ActionColumn::className()],
];

echo GridView::widget([
    'panel' => [
        'headerToolbar' => [
            $headerToolbar,
        ],
    ],
    'toolbar' => [
        '{search}',
        '{refresh}',
    ],
    'dataProvider' => $dataProvider,
    'columns' => $column,
]);
?>