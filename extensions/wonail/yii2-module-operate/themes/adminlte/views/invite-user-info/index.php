<?php

use wonail\adminlte\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel \wocenter\backend\modules\operate\models\InviteUserInfoSearch */
/* @var $dataProvider \backend\core\ActiveDataProvider */

$headerToolbar = '';
$actionBtn = '';
?>

<?php
// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = Yii::t('wocenter/app', 'Invite User Infos');
    $this->params['breadcrumbs'][] = $this->title;
    $this->params['navSelectPage'] = '/operate/invite-user-info/index';
    $headerToolbar = Html::a('<i class="fa fa-plus"></i> <span class="hidden-xs">' . Yii::t('wocenter/app', 'New add') . '</span>', ['generate'], [
        'class' => 'btn btn-success',
        'data-method' => 'post',
    ]);
    $actionBtn = Html::tag('div',
        Html::button('<i class="fa fa-close"></i> <span class="hidden-xs">' . Yii::t('wocenter/app', 'Forbidden') . '</span>', [
            'class' => 'btn',
            'href' => Url::to(['forbidden']),
            'data' => [
                'method' => 'post',
            ],
        ]) .
        Html::button('<i class="fa fa-trash-o"></i> <span class="hidden-xs">' . Yii::t('wocenter/app', 'Delete') . '</span>', [
            'class' => 'btn',
            'href' => Url::to(['remove-all']),
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

<?= GridView::widget([
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
        'id',
        'invite_type',
        'uid',
        'num',
        'already_num',
        'success_num',
        [
            'class' => \wocenter\backend\modules\action\models\Action::className(),
        ],
    ],
]);
?>