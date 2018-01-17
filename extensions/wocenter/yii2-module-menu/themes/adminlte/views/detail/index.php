<?php
use wocenter\libs\Constants;
use wonail\adminlte\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider \backend\core\ActiveDataProvider */
/* @var $category integer 所属分类ID */
/* @var $pid integer */
/* @var $breadcrumbs array 面包屑导航 */
/* @var $title string 当前面包屑标题 */

$headerToolbar = '';
?>

<?php

// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = $title;
    $this->params['breadcrumbs'] = $breadcrumbs;
    $this->params['navSelectPage'] = '/menu/category/index';
    $headerToolbar = Html::a('<i class="fa fa-plus"></i> <span class="hidden-xs">' . Yii::t('wocenter/app', 'New add') . '</span>', [
        'create',
        'category' => $category,
        'pid' => $pid ?: null,
    ], [
        'class' => 'btn btn-success',
        'data-pjax' => 1,
    ]);
}
// full_page:END
?>

<?php
$column = [
    'id',
    [
        'attribute' => 'name',
        'format' => 'raw',
        'value' => function ($model, $key, $index, $column) use ($category) {
            return Html::a($model['name'], [
                '/menu/detail',
                'category' => $category,
                'pid' => $model['id'],
            ], ['data-pjax' => 1]);
        },
    ],
    'alias_name',
    'url',
    'description:ntext',
    [
        'attribute' => 'target',
        'value' => function ($model, $key, $index, $column) {
            return Constants::getOpenTargetValue($model['target']);
        },
    ],
    'icon_html',
    [
        'class' => 'kartik\grid\BooleanColumn',
        'width' => 'auto',
        'attribute' => 'is_dev',
    ],
    [
        'class' => 'kartik\grid\BooleanColumn',
        'attribute' => 'show_on_menu',
    ],
    'createdTypeValue',
    'sort_order',
    [
        'class' => 'kartik\grid\BooleanColumn',
        'attribute' => 'status',
    ],
    [
        'class' => \wonail\adminlte\grid\ActionColumn::className(),
        'buttons' => [
            'update' => function ($url, $model, $key) use ($pid) {
                return Html::a('<i class="glyphicon glyphicon-pencil"></i>', $url . "&pid={$pid}", [
                    'title' => Yii::t('yii', 'Update'),
                    'aria-label' => Yii::t('yii', 'Update'),
                    'data-pjax' => 1,
                ]);
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
        [
            'content' => '{refresh}',
            'options' => [
                'class' => 'hide'
            ]
        ],
    ],
    'dataProvider' => $dataProvider,
    'columns' => $column,
]);
?>