<?php

use wonail\adminlte\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel \wocenter\backend\modules\data\models\UserScoreTypeSearch */
/* @var $dataProvider \backend\core\ActiveDataProvider */

$headerToolbar = '';
?>

<?php

// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = '积分类型';
    $this->params['breadcrumbs'][] = $this->title;
    $this->params['navSelectPage'] = '/data/score-type/index';
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
        'attribute' => 'name',
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column) {
            return Html::a($model['name'], ['update', 'id' => $key], ['data-pjax' => 1]);
        },
    ],
    'unit',
    [
        'class' => 'kartik\grid\BooleanColumn',
        'attribute' => 'status',
    ],
    [
        'class' => \wonail\adminlte\grid\ActionColumn::className(),
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
    'columns' => $column,
]);
?>