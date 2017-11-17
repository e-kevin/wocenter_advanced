<?php
use wonail\adminlte\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel wocenter\backend\modules\action\models\ActionLimitSearch */
/* @var $dataProvider backend\core\ActiveDataProvider */

$headerToolbar = '';
?>

<?php

// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = '行为限制列表';
    $this->params['breadcrumbs'][] = $this->title;
    $this->params['navSelectPage'] = '/action/limit/index';
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
    'fullCycle',
    'punishValue',
    'action',
//    'updated_at:datetime',
//    [
//        'class' => '\kartik\grid\BooleanColumn',
//        'width' => '110px',
//        'attribute' => 'send_notification',
//    ],
    [
        'class' => '\kartik\grid\BooleanColumn',
        'attribute' => 'check_ip',
    ],
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