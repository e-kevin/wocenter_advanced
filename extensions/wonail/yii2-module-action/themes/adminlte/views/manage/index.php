<?php
use wonail\adminlte\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel wocenter\backend\modules\action\models\ActionSearch */
/* @var $dataProvider backend\core\ActiveDataProvider */

$headerToolbar = '';
$actionBtn = '';
?>

<?php

// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = '行为列表';
    $this->params['breadcrumbs'][] = $this->title;
    $this->params['breadcrumb_description'] = '每个行为均会产生一条行为日志';
    $this->params['navSelectPage'] = '/action/manage/index';
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
$columns = [
    [
        'attribute' => 'name',
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column) {
            return Html::a($model['name'], ['update', 'id' => $key], ['data-pjax' => 1]);
        },
    ],
    'title',
    'description',
    'typeValue',
//    'updated_at:datetime',
    [
        'class' => '\kartik\grid\BooleanColumn',
        'attribute' => 'status',
    ],
    [
        'class' => 'wonail\adminlte\grid\ActionColumn',
        'template' => '{delete}',
    ],
];

echo GridView::widget([
    'panel' => [
        'headerToolbar' => [
            $headerToolbar,
        ],
    ],
    'dataProvider' => $dataProvider,
    'columns' => $columns,
    'toolbar' => [
        '{search}',
        '{refresh}',
        '{toggleData}',
    ],
]);
?>