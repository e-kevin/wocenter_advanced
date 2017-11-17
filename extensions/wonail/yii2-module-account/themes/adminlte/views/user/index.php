<?php
use wonail\adminlte\grid\GridView;
use wocenter\libs\Constants;
use backend\core\ActiveDataProvider;
use wocenter\helpers\DateTimeHelper;
use wocenter\backend\modules\account\models\BaseUser;
use \wocenter\backend\modules\account\models\UserSearch;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $searchModel UserSearch */
/* @var $dataProvider ActiveDataProvider */

$headerToolbar = '';
$actionBtn = '';

$this->params['navSelectPage'] = '/' . Yii::$app->requestedRoute;
// full_page:START
if ($this->context->isFullPageLoad()) {
    switch (Yii::$app->controller->action->id) {
        case 'forbidden-list' :
            $this->title = '禁用列表';
            $actionBtn = Html::tag('div', Html::button('<i class="fa fa-check"></i> <span class="hidden-xs">' . Yii::t('wocenter/app', 'Active') . '</span>', [
                'class' => 'btn',
                'href' => Url::to(['active']),
                'data' => [
                    'method' => 'post',
                ],
            ]), [
                    'class' => 'btn-group hide',
                    'data-widget' => 'action-list',
                ]
            );
            break;
        case 'locked-list' :
            $this->title = '锁定列表';
            break;
        default :
            $this->title = '用户列表';
            $headerToolbar = Html::a('<i class="fa fa-plus"></i> <span class="hidden-xs">' . Yii::t('wocenter/app', 'Generate User') . '</span>', ['generate'], [
                'class' => 'btn btn-success',
                'data-method' => 'post',
            ]);
            $actionBtn = Html::tag('div', Html::button('<i class="fa fa-close"></i> <span class="hidden-xs">' . Yii::t('wocenter/app', 'Forbidden') . '</span>', [
                'class' => 'btn',
                'href' => Url::to(['forbidden']),
                'data' => [
                    'method' => 'post',
                ],
            ]), [
                    'class' => 'btn-group hide',
                    'data-widget' => 'action-list',
                ]
            );
    }

    $this->params['breadcrumbs'][] = '用户管理';
    $this->params['breadcrumbs'][] = $this->title;
}
// full_page:END
?>

<?php
$columns = [
    ['class' => \wonail\adminlte\grid\CheckboxColumn::className()],
    ['class' => yii\grid\SerialColumn::className()],
    'id',
    [
        'attribute' => 'username',
        'format' => 'raw',
        'value' => function ($model) {
            return Html::a($model->username, ['view', 'id' => $model->id], ['data-pjax' => 1]);
        },
    ],
    'userProfile.score_1',
    'userProfile.login_count',
    'userProfile.reg_time:datetime',
    [
        'attribute' => 'userProfile.last_login_time',
        'value' => function ($model) {
            return DateTimeHelper::timeFriendly($model->userProfile->last_login_time);
        },
    ],
    [
        'attribute' => 'userProfile.last_login_ip',
        'value' => function ($model, $key, $index, $column) {
            return $model->userProfile->last_login_ip ?: $column->grid->formatter->nullDisplay;
        },
    ],
    [
        'attribute' => 'userProfile.last_location',
        'value' => function ($model, $key, $index, $column) {
            return $model->userProfile->last_location ?: $column->grid->formatter->nullDisplay;
        },
    ],
    [
        'attribute' => 'status',
        'value' => function ($model) {
            return Constants::getUserStatusValue($model->status);
        },
    ],
    [
        'class' => '\kartik\grid\BooleanColumn',
        'attribute' => 'is_audit',
    ],
    [
        'class' => '\kartik\grid\BooleanColumn',
        'attribute' => 'is_active',
    ],
    [
        'class' => '\kartik\grid\BooleanColumn',
        'attribute' => 'validate_email',
    ],
    [
        'class' => '\kartik\grid\BooleanColumn',
        'attribute' => 'validate_mobile',
    ],
    [
        'class' => wonail\adminlte\grid\ActionColumn::className(),
        'template' => '{update} {delete} {unlock} {init-password}',
        'visibleButtons' => [
            'unlock' => function ($model, $key, $index) {
                return $model->status == BaseUser::STATUS_LOCKED;
            },
        ],
        'buttons' => [
            'unlock' => function ($url, $model, $key) {
                return Html::a('<span class="fa fa-fw fa-unlock"></span>', $url, [
                    'title' => Yii::t('wocenter/app', 'Unlock'),
                    'aria-label' => Yii::t('wocenter/app', 'Unlock'),
                    'data' => [
                        'method' => 'post',
                        'params' => [
                            'selection' => $key,
                        ],
                    ],
                ]);
            },
            'init-password' => function ($url, $model, $key) {
                return Html::a('<i class="fa fa-fw fa-refresh"></i>', $url, [
                    'title' => Yii::t('wocenter/app', 'Reset Password'),
                    'aria-label' => Yii::t('wocenter/app', 'Reset Password'),
                    'data' => [
                        'confirm' => Yii::t('wocenter/app', 'The new password is {password}', ['password' => '123456']),
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
        '{search}',
        '{refresh}',
        '{toggleData}',
    ],
    'panel' => [
        'headerToolbar' => [
            $headerToolbar,
            $actionBtn,
        ],
    ],
]);

?>