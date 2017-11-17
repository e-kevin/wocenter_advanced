<?php
use wonail\adminlte\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel \wocenter\backend\modules\operate\models\RankSearch */
/* @var $dataProvider \backend\core\ActiveDataProvider */

$headerToolbar = '';
$actionBtn = '';
?>

<?php
// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = Yii::t('wocenter/app', 'Ranks');
    $this->params['breadcrumbs'][] = $this->title;
    $this->params['navSelectPage'] = '/operate/rank/index';
    $headerToolbar = Html::a('<i class="fa fa-plus"></i> <span class="hidden-xs">' . Yii::t('wocenter/app', 'New add') . '</span>', [
        'create',
    ], [
        'class' => 'btn btn-success',
        'data-pjax' => 1,
    ]);
}
// full_page:END
?>

<?= GridView::widget([
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
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'name',
            'format' => 'raw',
            'value' => function ($model, $key, $index, $column) {
                return Html::a($model['name'], ['update', 'id' => $key], ['data-pjax' => 1]);
            },
        ],
//        'logo',
        'label',
        [
            'class' => '\kartik\grid\BooleanColumn',
            'attribute' => 'allow_user_apply',
            'width' => 'auto'
        ],
        [
            'class' => \wonail\adminlte\grid\ActionColumn::className(),
            'template' => '{delete}',
        ],
    ],
]);
?>