<?php
use wonail\adminlte\grid\GridView;
use wocenter\libs\Constants;
use backend\core\ActiveDataProvider;
use wocenter\helpers\DateTimeHelper;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel wocenter\backend\modules\account\models\BackendUserSearch */
/* @var $dataProvider ActiveDataProvider */

$headerToolbar = '';

// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = '管理员列表';
    $this->params['navSelectPage'] = '/account/admin/index';
    $headerToolbar = Html::a('<i class="fa fa-plus"></i> <span class="hidden-xs">' . '添加管理员' . '</span>', ['add'], [
        'class' => 'btn btn-success',
        'data-pjax' => 1,
    ]);

    $this->params['breadcrumbs'][] = '用户管理';
    $this->params['breadcrumbs'][] = $this->title;
}
// full_page:END
?>

<?php
$columns = [
    ['class' => \wonail\adminlte\grid\CheckboxColumn::className()],
    ['class' => yii\grid\SerialColumn::className()],
    'user_id',
    [
        'attribute' => 'user.username',
        'format' => 'raw',
        'value' => function ($model) {
            return Html::a($model->user->username, ['update', 'id' => $model->id], ['data-pjax' => 1]);
        },
    ],
    'user.userProfile.login_count',
    'user.userProfile.reg_time:datetime',
    [
        'attribute' => 'user.userProfile.last_login_time',
        'value' => function ($model) {
            return DateTimeHelper::timeFriendly($model->user->userProfile->last_login_time);
        },
    ],
    [
        'attribute' => 'user.userProfile.last_login_ip',
        'value' => function ($model, $key, $index, $column) {
            return $model->user->userProfile->last_login_ip ?: $column->grid->formatter->nullDisplay;
        },
    ],
    [
        'attribute' => 'user.userProfile.last_location',
        'value' => function ($model, $key, $index, $column) {
            return $model->user->userProfile->last_location ?: $column->grid->formatter->nullDisplay;
        },
    ],
    [
        'attribute' => 'status',
        'value' => function ($model) {
            return Constants::getUserStatusValue($model->status);
        },
    ],
    [
        'class' => wonail\adminlte\grid\ActionColumn::className(),
        'template' => '{relieve}',
        'buttons' => [
            'relieve' => function ($url, $model, $key) {
                return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url, [
                    'title' => '解除管理员',
                    'aria-label' => '解除管理员',
                    'data' => [
                        'confirm' => '确定解除该用户管理员身份吗？解除后该用户将无法登录后台管理系统',
                        'method' => 'post',
                        'params' => [
                            'selection' => $key,
                        ],
                    ],
                ]);
            },
        ],
    ],
];

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => $columns,
    'toolbar' => [
        '{refresh}',
        '{toggleData}',
    ],
    'panel' => [
        'headerToolbar' => [
            $headerToolbar,
        ],
    ],
]);

?>