<?php
use wonail\adminlte\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel \wocenter\backend\modules\data\models\AreaRegionSearch */
/* @var $dataProvider \wocenter\backend\core\ActiveDataProvider */
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
    $this->params['navSelectPage'] = '/data/area-region/index';
    $headerToolbar = Html::a('<i class="fa fa-plus"></i> <span class="hidden-xs">' . Yii::t('wocenter/app', 'New add') . '</span>', [
        'create',
        'pid' => $pid ?: null,
    ], ['class' => 'btn btn-success', 'data-pjax' => 1]);
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
        '{search}',
        '{refresh}',
    ],
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'attribute' => 'region_name',
            'format' => 'raw',
            'value' => function ($model, $key, $index, $column) {
                return Html::a($model['region_name'], [
                    '/data/area-region',
                    'pid' => $model['region_id'],
                ], ['data-pjax' => 1]);
            },
        ],
        'regionTypeValue',
        [
            'class' => \wonail\adminlte\grid\ActionColumn::className(),
        ],
    ],
]);
?>