<?php

use kartik\grid\GridView;
use wonail\adminlte\widgets\ActiveForm;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\extension\models\Module */
/* @var $form ActiveForm */
/* @var $runModuleList array */
/* @var $dependList array */

$footer = '<blockquote class="help-block">';
$footer .= '<p>系统扩展：安装后无法卸载</p>';
$footer .= '<p>运行模块：选择哪个模块配置来运行当前模块</p>';
if (Yii::$app->controller->action->id == 'install' && $model->infoInstance->canInstall) {
    $footer .= '<p>安装后，系统将自动加载所需菜单、路由规则、权限配置等信息</p>';
} else {
    $footer .= '<p>更改设置后，系统将自动同步更新所需菜单、路由规则、权限配置等信息</p>';
}
$footer .= '</blockquote>';
?>

<?php $form = ActiveForm::begin([
    'box' => [
        'footer' => $footer,
    ],
]);
?>

<div class="jumbotron text-center">
    <h1><?= $model->infoInstance->name ?>
        <small class="text-danger">
            <?php if ($model->is_system) : ?>系统扩展<?php endif; ?>
        </small>
    </h1>
    <p class="lead"><?= $model->infoInstance->description ?></p>
</div>

<?php
echo $form->field($model, 'id')->textInput(['disabled' => true]);

echo $form->field($model, 'module_id')->textInput();

// 系统模块
if (!$model->infoInstance->isSystem) {
    echo $form->field($model, 'is_system')->radioList(['否', '是']);
}
// 运行模块列表
echo $form->field($model, 'run')->radioList($runModuleList);
//echo $form->field($model, 'status')->radioList(\wocenter\libs\Constants::getStatusList());

$btn[] = Html::submitButton(Yii::t('wocenter/app',
    ($this->context->action->id == 'install' && $model->infoInstance->canInstall)
        ? 'Install'
        : 'Save'
), ['class' => 'btn btn-success width-200']);
$btn[] = Html::resetButton(Yii::t('wocenter/app', 'Reset'), ['class' => 'btn btn-default']);
$btn[] = Html::button(Yii::t('wocenter/app', 'Go back'), ['class' => 'btn btn-default', 'data-widget' => 'goback',]);
echo Html::tag('div', implode("\n", $btn), [
    'class' => 'text-center',
]);
?>

<hr>

<div class="row-fluid">
    <div class="col-lg-9">
        <?php
        $dataProvider = new ArrayDataProvider([
            'allModels' => $dependList,
            'pagination' => [
                'pageSize' => -1, //不使用分页
            ],
        ]);
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => "{items}",
            'bordered' => false,
            'hover' => true,
            'emptyText' => '不存在任何依赖关系',
            'emptyTextOptions' => ['class' => 'text-center text-muted'],
            'columns' => [
                [
                    'label' => '扩展名称',
                    'value' => function ($model, $key) {
                        return $key;
                    },
                ],
                [
                    'label' => '名称',
                    'attribute' => 'name',
                ],
                [
                    'label' => '描述',
                    'attribute' => 'description',
                ],
                [
                    'label' => '版本',
                    'format' => 'raw',
                    'value' => function ($model) {
                        if ($model['currentVersion'] == $model['needVersion']) {
                            return $model['needVersion'];
                        } else {
                            return Html::tag('div', Html::tag('span','版本冲突', ['class'=> 'text-red']), [
                                'data-toggle' => 'tooltip',
                                'title' => nl2br('本地版本：' . $model['currentVersion'] . "\n" . '依赖版本：' . $model['needVersion']),
                                'data-html' => 'true',
                            ]);
                        }
                    },
                ],
                [
                    'class' => 'kartik\grid\BooleanColumn',
                    'attribute' => 'downloaded',
                    'label' => '已下载',
                ],
                [
                    'class' => 'kartik\grid\BooleanColumn',
                    'attribute' => 'installed',
                    'label' => '已安装',
                ],
            ],
        ]);
        ?>
    </div>
    <div class="col-lg-3 text-muted">
        <h4>扩展依赖</h4>
        <p>
            使用该扩展前必须首先解决扩展依赖，只有满足依赖关系才能确保正常使用该扩展功能。
        </p>
    </div>
</div>

<?php ActiveForm::end(); ?>
