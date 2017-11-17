<?php
use wonail\adminlte\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel \wocenter\backend\modules\account\models\TagSearch */
/* @var $dataProvider \backend\core\ActiveDataProvider */
/* @var $pid integer */
/* @var $breadcrumbs array 面包屑导航 */
/* @var $title string 当前面包屑标题 */

$headerToolbar = '';
$actionBtn = '';
?>

<?php
// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = $title;
    $this->params['breadcrumbs'] = $breadcrumbs;
    $this->params['navSelectPage'] = '/account/tag/index';
    $headerToolbar = Html::a('<i class="fa fa-plus"></i> <span class="hidden-xs">' . Yii::t('wocenter/app', 'New add') . '</span>', [
        'create',
        'pid' => $pid ?: null
    ], [
        'class' => 'btn btn-success',
        'data-pjax' => 1,
    ]);
    $actionBtn = Html::tag('div',
        Html::button('<i class="fa fa-trash-o"></i> <span class="hidden-xs">' . Yii::t('wocenter/app', 'Delete') . '</span>', [
            'class' => 'btn',
            'href' => Url::to(['batch-delete']),
            'data' => [
                'method' => 'post',
                'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
            ],
        ]), [
            'class' => 'btn-group hide',
            'data-widget' => 'action-list',
        ]
    );
}
// full_page:END
?>

<?=
GridView::widget([
    'panel' => [
        'headerToolbar' => [
            $headerToolbar,
            $actionBtn,
        ],
    ],
    'toolbar' => [
        '{refresh}',
    ],
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => \wonail\adminlte\grid\CheckboxColumn::className()],
        ['class' => yii\grid\SerialColumn::className()],
        [
            'attribute' => 'title',
            'format' => 'raw',
            'value' => function ($model) {
                return Html::a($model->title, [
                    '/account/tag',
                    'pid' => $model->id,
                ], ['data-pjax' => 1]);
            },
        ],
        'sort_order',
        [
            'class' => 'kartik\grid\BooleanColumn',
            'attribute' => 'status',
        ],
        [
            'class' => \wonail\adminlte\grid\ActionColumn::className(),
            'template' => '{update} {delete}',
        ],
    ],
]);
?>