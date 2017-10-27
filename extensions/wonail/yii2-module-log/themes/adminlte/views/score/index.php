<?php

use wocenter\backend\core\ActiveDataProvider;
use wocenter\backend\modules\log\models\UserScoreLogSearch;
use wocenter\Wc;
use wonail\adminlte\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel UserScoreLogSearch */
/* @var $dataProvider ActiveDataProvider */
?>

<?php
// full_page:START
if ($this->context->isFullPageLoad()) {
    $this->title = '奖罚日志';
    $this->params['breadcrumbs'][] = $this->title;
    $this->params['navSelectPage'] = '/log/score/index';
}
// full_page:END
?>

<?php
$column = [
    'id',
    [
        'attribute' => 'uid',
        'value' => function ($model) {
            return Wc::$service->getAccount()->queryUser('username', $model->uid);
        },
    ],
    'remark',
    [
        'attribute' => 'typeValue.name',
        'label' => $searchModel->getAttributeLabel('type'),
    ],
    'actionValue',
    'value',
    'finally_value',
    [
        'attribute' => 'ip',
        'value' => function ($model) {
            return long2ip($model->ip);
        },
    ],
];

echo GridView::widget([
    'toolbar' => [
        '{search}',
        '{refresh}',
        '{toggleData}',
    ],
    'dataProvider' => $dataProvider,
    'columns' => $column,
]);
?>