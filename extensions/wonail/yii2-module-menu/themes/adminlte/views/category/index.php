<?php
use wonail\adminlte\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel \wocenter\backend\modules\menu\models\MenuCategorySearch */
/* @var $dataProvider \backend\core\ActiveDataProvider */

$headerToolbar = '';
$toolbars = '';
?>

<?php

// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = '菜单管理';
    $this->params['breadcrumbs'][] = $this->title;
    $this->params['navSelectPage'] = '/menu/category/index';
    $headerToolbar = Html::a('<i class="fa fa-plus"></i> <span class="hidden-xs">' . Yii::t('wocenter/app', 'New add') . '</span>', [
        'create',
    ], [
        'class' => 'btn btn-success',
        'data-pjax' => 1,
    ]);
    $toolbars = Html::button('<i class="fa fa-refresh"></i> <span class="hidden-xs">同步后台菜单</span>', [
        'class' => 'btn btn-warning',
        'href' => Url::toRoute(['sync-menus']),
        'data' => [
            'method' => 'post',
        ],
    ]);
}
// full_page:END
?>

<?=

GridView::widget([
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
        $toolbars
    ],
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        [
            'attribute' => 'name',
            'format' => 'raw',
            'value' => function ($model, $key, $index, $column) {
                return Html::a($model->name, ['/menu/detail', 'category' => $model->id], ['data-pjax' => 1]);
            },
        ],
        'description',
        ['class' => \wonail\adminlte\grid\ActionColumn::className()],
    ],
]);

?>