<?php
use wonail\adminlte\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel \wocenter\backend\modules\operate\models\InviteSearch */
/* @var $dataProvider \backend\core\ActiveDataProvider */

$headerToolbar = '';
$actionBtn = '';
?>

<?php

// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = Yii::t('wocenter/app', 'Invites');
    $this->params['breadcrumbs'][] = $this->title;
    $this->params['navSelectPage'] = '/operate/invite/index';
    $headerToolbar = Html::a('<i class="fa fa-plus"></i> <span class="hidden-xs">' . '生成邀请码' . '</span>', ['generate'], [
            'class' => 'btn btn-success',
            'data-pjax' => 1,
        ]) . ' ' . Html::a('<i class="fa fa-trash"></i> <span class="hidden-xs">' . '清空' . '</span>', ['clear'], [
            'class' => 'btn btn-danger',
            'data' => [
                'method' => 'post',
                'confirm' => '确认清空所有无效邀请码吗？此操作为真删除，删除包括：已删除，已过期的邀请码',
            ],
        ]);
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
        '{search}',
        '{refresh}',
    ],
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => \wonail\adminlte\grid\CheckboxColumn::className()],
        ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'code',
            'value' => function ($model) {
                if ($model->invite_type == 1 || $model->uid == Yii::$app->getUser()->getId()) {
                    return $model->code;
                } else {
                    return \wocenter\helpers\SecurityHelper::markString($model->code);
                }
            },
        ],
        [
            'label' => '邀请码链接',
            'value' => function ($model, $key, $index, $column) {
                if ($model->invite_type == 1 || $model->uid == Yii::$app->getUser()->getId()) {
                    return Url::to(['/passport/common/invite-signup', 'code' => $model->code], true);
                } else {
                    return $column->grid->formatter->nullDisplay;
                }
            },
            'width' => '300px'
        ],
        [
            'attribute' => 'inviteType.title',
            'label' => $searchModel->getAttributeLabel('invite_type'),
        ],
        'can_num',
        'already_num',
        [
            'attribute' => 'user.username',
            'label' => $searchModel->getAttributeLabel('uid'),
        ],
        'created_at:datetime',
        'expired_at:datetime',
        [
            'attribute' => 'status',
            'value' => function ($model) {
                return $model->codeStatus[$model->status];
            },
        ],
        [
            'class' => \wonail\adminlte\grid\ActionColumn::className(),
            'template' => '{delete}',
            'visibleButtons' => [
                'delete' => function ($model, $key, $index) {
                    return $model->status !== $model::CODE_DELETED;
                },
            ],
        ],
    ],
]);
?>