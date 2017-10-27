<?php
use wonail\adminlte\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel \wocenter\backend\modules\account\models\IdentityGroupSearch */
/* @var $dataProvider \wocenter\backend\core\ActiveDataProvider */

$headerToolbar = '';
?>

<?php
// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = '身份分组';
    $this->params['breadcrumbs'][] = $this->title;
    $this->params['breadcrumb_description'] = '每个身份只能归属一个分组，同一分组下的身份不能同时被用户拥有';
    $this->params['navSelectPage'] = '/account/identity-group/index';
    $headerToolbar = Html::a('<i class="fa fa-plus"></i> <span class="hidden-xs">' . Yii::t('wocenter/app', 'New add') . '</span>', [
        'create',
    ], [
        'class' => 'btn btn-success',
        'data-pjax' => 1,
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
        '{refresh}',
    ],
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => yii\grid\SerialColumn::className()],
        [
            'attribute' => 'title',
            'format' => 'raw',
            'value' => function ($model) {
                return Html::a($model->title, ['update', 'id' => $model->id], ['data-pjax' => 1]);
            },
        ],
        [
            'value' => function ($model, $key, $index, $column) {
                $identities = \yii\helpers\ArrayHelper::getColumn($model->identities, 'title');

                return $identities ? implode(',', $identities) : $column->grid->formatter->nullDisplay;
            },
            'label' => '已关联身份'
        ],
        [
            'class' => \wonail\adminlte\grid\ActionColumn::className(),
            'template' => '{delete}',
        ],
    ],
]);
?>