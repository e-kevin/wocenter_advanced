<?php
use kartik\grid\BooleanColumn;
use wonail\adminlte\grid\ActionColumn;
use wonail\adminlte\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel wocenter\backend\modules\account\models\IdentitySearch */
/* @var $dataProvider backend\core\ActiveDataProvider */

$headerToolbar = '';
?>

<?php

// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = '身份列表';
    $this->params['breadcrumbs'][] = $this->title;
    $this->params['navSelectPage'] = '/account/identity/index';
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
    ['class' => yii\grid\SerialColumn::className()],
    [
        'attribute' => 'title',
        'format' => 'raw',
        'value' => function ($model) {
            return Html::a($model->title, ['update', 'id' => $model->id], ['data-pjax' => 1]);
        },
    ],
    'name',
    [
        'attribute' => 'identityGroup.title',
        'label' => '身份分组',
    ],
    [
        'label' => '绑定档案',
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column) {
            $profiles = \yii\helpers\ArrayHelper::getColumn($model->profiles, 'profile_name');

            return $profiles ? implode(',', $profiles) : $column->grid->formatter->nullDisplay;
        },
    ],
    'description',
    'sort_order',
    [
        'class' => BooleanColumn::className(),
        'attribute' => 'is_invite',
        'width' => '110px',
    ],
    [
        'class' => BooleanColumn::className(),
        'attribute' => 'is_audit',
    ],
    [
        'class' => BooleanColumn::className(),
        'attribute' => 'status',
    ],
    [
        'class' => ActionColumn::className(),
        'template' => '{setting} {delete}',
        'buttons' => [
            'setting' => function ($url, $model, $key) {
                return Html::a('默认信息配置', ['setting', 'type' => 'score', 'id' => $key], ['data-pjax' => 1]);
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
        '{toggleData}',
    ],
    'dataProvider' => $dataProvider,
    'columns' => $column,
]);
?>