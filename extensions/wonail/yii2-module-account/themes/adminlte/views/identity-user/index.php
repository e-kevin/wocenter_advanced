<?php
use wonail\adminlte\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel \wocenter\backend\modules\account\models\UserIdentitySearch */
/* @var $dataProvider \backend\core\ActiveDataProvider */

?>

<?php
// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = '身份用户管理';
    $this->params['breadcrumbs'][] = $this->title;
    $this->params['navSelectPage'] = '/account/identity-user/index';
}
// full_page:END
?>

<?=
GridView::widget([
    'panel' => [
        'headerToolbar' => [
        ],
    ],
    'toolbar' => [
        '{refresh}',
    ],
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => yii\grid\SerialColumn::className()],
        'uid',
        [
            'attribute' => 'identity.title',
            'label' => '绑定身份'
        ],
        [
            'class' => '\kartik\grid\BooleanColumn',
            'attribute' => 'status',
        ],
    ],
]);
?>