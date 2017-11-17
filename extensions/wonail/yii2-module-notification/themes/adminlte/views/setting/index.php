<?php
use backend\core\ActiveDataProvider;
use wocenter\backend\modules\notification\models\NotifySearch;
use wocenter\Wc;
use wonail\adminlte\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel NotifySearch */
/* @var $dataProvider ActiveDataProvider */

$headerToolbar = '';
?>

<?php

// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = '通知管理';
    $this->params['navSelectPage'] = '/notification/setting/index';
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
        'attribute' => 'node',
        'format' => 'raw',
        'value' => function ($model, $key) {
            return Html::a($model['node'], ['update', 'id' => $key], ['data-pjax' => 1]);
        },
    ],
    'name',
    'description',
    [
        'attribute' => 'sender',
        'value' => function ($model) {
            return $model->getSenderValue();
        },
    ],
    [
        'attribute' => 'email_sender',
        'value' => function ($model, $key, $index, $column) {
            return $model->email_sender
                ? Wc::$service->getSystem()->getConfig()->extra('EMAIL_SENDER')[$model->email_sender]
                : $column->grid->formatter->nullDisplay;
        },
    ],
    [
        'class' => 'kartik\grid\BooleanColumn',
        'width' => 'auto',
        'attribute' => 'send_message',
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
    'toolbar' => [
        '{search}',
        '{refresh}',
    ],
    'dataProvider' => $dataProvider,
    'columns' => $column,
]);
?>