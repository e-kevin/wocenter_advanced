<?php
use backend\core\ActiveDataProvider;
use wocenter\backend\modules\log\models\ActionLogSearch;
use wocenter\Wc;
use wonail\adminlte\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel ActionLogSearch */
/* @var $dataProvider ActiveDataProvider */

$actionBtn = '';
?>

<?php
// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = '行为日志';
    $this->params['breadcrumbs'][] = $this->title;
    $this->params['navSelectPage'] = '/log/action/index';
    $actionBtn = Html::tag('div', Html::button('<i class="fa fa-trash-o"></i> <span class="hidden-xs">' . Yii::t('wocenter/app', 'Delete') . '</span>', [
        'class' => 'btn',
        'href' => Url::to(['batch-delete']),
        'data' => [
            'method' => 'post',
            'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
        ],
    ]), [
        'class' => 'btn-group hide',
        'data-widget' => 'action-list',
    ]);
}
// full_page:END
?>

<?php
$column = [
    ['class' => \wonail\adminlte\grid\CheckboxColumn::className()],
    'id',
    [
        'attribute' => 'user_id',
        'value' => function ($model) {
            if ($model->user_id == 0) {
                return '系统';
            } else {
                return Wc::$service->getAccount()->queryUser('username', $model->user_id);
            }
        },
    ],
    [
        'attribute' => 'action.title',
        'label' => '行为',
    ],
    'created_at:datetime',
    [
        'attribute' => 'action_ip',
        'value' => function ($model) {
            return long2ip($model->action_ip);
        },
    ],
    'action_location',
    [
        'class' => wonail\adminlte\grid\ActionColumn::className(),
        'template' => '{delete}',
    ],
];

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'panel' => [
        'headerToolbar' => [
            $actionBtn,
        ],
    ],
    'toolbar' => [
        '{search}',
        '{refresh}',
        '{export}',
        '{toggleData}',
    ],
    'columns' => $column,
]);
?>