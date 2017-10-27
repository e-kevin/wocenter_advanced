<?php
use wonail\adminlte\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel \wocenter\backend\modules\operate\models\InviteLogSearch */
/* @var $dataProvider \wocenter\backend\core\ActiveDataProvider */

$actionBtn = '';
?>

<?php
// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = '邀请记录';
    $this->params['breadcrumbs'][] = $this->title;
    $this->params['navSelectPage'] = '/operate/invite-log/index';
    $actionBtn = Html::tag('div',
        Html::button('<i class="fa fa-trash-o"></i> <span class="hidden-xs">' . Yii::t('wocenter/app', 'Delete') . '</span>', [
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

<?= GridView::widget([
    'panel' => [
        'headerToolbar' => [
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
        'uid',
        'inviter_id',
        [
            'attribute' => 'inviteType.title',
            'label' => '邀请码类型'
        ],
        'remark',
        'created_at:datetime',
        [
            'class' => \wonail\adminlte\grid\ActionColumn::className(),
            'template' => '{delete}'
        ],
    ],
]);
?>