<?php
use wonail\adminlte\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel \wocenter\backend\modules\operate\models\InviteTypeSearch */
/* @var $dataProvider \wocenter\backend\core\ActiveDataProvider */

$headerToolbar = '';
?>

<?php
// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = '邀请码类型';
    $this->params['breadcrumbs'][] = $this->title;
    $this->params['navSelectPage'] = '/operate/invite-type/index';
    $headerToolbar = Html::a('<i class="fa fa-plus"></i> <span class="hidden-xs">' . Yii::t('wocenter/app', 'New add') . '</span>', [
        'create'
    ], [
        'class' => 'btn btn-success',
        'data-pjax' => 1
    ]);
}
// full_page:END
?>

<?php
$column = [
    ['class' => 'yii\grid\SerialColumn'],
    [
        'attribute' => 'title',
        'format' => 'raw',
        'value' => function ($model, $key) {
            return Html::a($model['title'], ['update', 'id' => $key], ['data-pjax' => 1]);
        },
    ],
    'length',
    'fullExpiredAt',
    'fullCycle',
//    'cycle_num',
//    'fullCycleTime',
//    'identities',
    'identityValue',
//    'auth_groups',
    'fullPayScore',
    'fullIncreaseScore',
    [
        'class' => '\kartik\grid\BooleanColumn',
        'attribute' => 'each_follow',
        'width' => 'auto'
    ],
    [
        'class' => '\kartik\grid\BooleanColumn',
        'attribute' => 'status',
    ],
    [
        'class' => \wonail\adminlte\grid\ActionColumn::className(),
        'template' => '{delete}'
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