<?php
use wonail\adminlte\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel \wocenter\backend\modules\account\models\ExtendProfileSearch */
/* @var $dataProvider \wocenter\backend\core\ActiveDataProvider */

$headerToolbar = '';
?>

<?php

// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = '扩展档案';
    $this->params['breadcrumbs'][] = $this->title;
    $this->params['navSelectPage'] = '/account/profile/index';
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
    ['class' => yii\grid\SerialColumn::className()],
    [
        'attribute' => 'profile_name',
        'format' => 'raw',
        'value' => function ($model) {
            return Html::a($model->profile_name, ['update', 'id' => $model->id], ['data-pjax' => 1]);
        },
    ],
    'sort_order',
    [
        'class' => '\kartik\grid\BooleanColumn',
        'attribute' => 'visible',
    ],
    [
        'class' => '\kartik\grid\BooleanColumn',
        'attribute' => 'status',
    ],
    [
        'class' => wonail\adminlte\grid\ActionColumn::className(),
        'template' => '{manage} {delete}',
        'buttons' => [
            'manage' => function ($url, $model, $key) {
                return Html::a('管理字段', ['/account/fields', 'profile_id' => $key], ['data-pjax' => 1]);
            },
        ],
    ],
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
    'columns' => $columns,
]);
?>