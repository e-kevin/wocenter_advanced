<?php
use kartik\grid\BooleanColumn;
use wonail\adminlte\grid\ActionColumn;
use wonail\adminlte\grid\GridView;
use yii\grid\SerialColumn;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel \wocenter\backend\modules\account\models\ExtendFieldSettingSearch */
/* @var $dataProvider \backend\core\ActiveDataProvider */
/* @var $profileId integer */
/* @var $profileName string */
/* @var $formTypeList array */

$headerToolbar = '';
?>

<?php

// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = '【' . $profileName . '】字段管理';
    $this->params['breadcrumbs'][] = ['label' => '扩展资料', 'url' => ['/account/profile/index']];
    $this->params['breadcrumbs'][] = $this->title;
    $this->params['navSelectPage'] = '/account/profile/index';
    $headerToolbar = Html::a('<i class="fa fa-plus"></i> <span class="hidden-xs">' . Yii::t('wocenter/app', 'New add') . '</span>', [
        'create', 'profile_id' => $profileId,
    ], [
        'class' => 'btn btn-success',
        'data-pjax' => 1,
    ]);
}
// full_page:END
?>

<?php
$column = [
    ['class' => SerialColumn::className()],
    [
        'attribute' => 'field_name',
        'format' => 'raw',
        'value' => function ($model) {
            return Html::a($model->field_name, ['update', 'id' => $model->id], ['data-pjax' => 1]);
        },
    ],
    [
        'attribute' => 'form_type',
        'value' => function ($model) use ($formTypeList) {
            return $formTypeList[$model->form_type];
        },
    ],
    'default_value',
    'rule',
    'hint',
//    'created_at:datetime',
    'sort_order',
    [
        'class' => BooleanColumn::className(),
        'attribute' => 'visible',
    ],
    [
        'class' => BooleanColumn::className(),
        'attribute' => 'status',
    ],
    [
        'class' => ActionColumn::className(),
        'template' => '{delete}',
    ],
];

echo GridView::widget([
    'panel' => [
        'headerToolbar' => [
            $headerToolbar,
        ],
    ],
    'toolbar' => [
        '{refresh}',
    ],
    'dataProvider' => $dataProvider,
    'columns' => $column,
]);
?>