<?php

use wonail\adminlte\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel \wocenter\backend\modules\operate\models\InviteBuyLogSearch */
/* @var $dataProvider \backend\core\ActiveDataProvider */

$headerToolbar = '';
$actionBtn = '';
?>

<?php
// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = Yii::t('wocenter/app', 'Invite Buy Logs');
    $this->params['breadcrumbs'][] = $this->title;
    $this->params['navSelectPage'] = '/operate/invite-buy-log/index';
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
        'heading' => [
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
        'content',
        'created_at:datetime',
        [
            'class' => \wocenter\backend\modules\action\models\Action::className(),
        ],
    ],
]);
?>