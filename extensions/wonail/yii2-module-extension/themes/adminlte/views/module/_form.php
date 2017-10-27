<?php
use wonail\adminlte\widgets\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\extension\models\Module */
/* @var $form ActiveForm */
/* @var $validRunModuleList array */

$footer = '<blockquote class="help-block">';
$footer .= '<p>系统模块：安装后无法卸载</p>';
$footer .= '<p>运行模块：选择哪个模块配置来运行当前模块</p>';
if (Yii::$app->controller->action->id == 'install' && $model->infoInstance->canInstall) {
    $footer .= '<p>安装后，系统将自动同步更新所需菜单</p>';
} else {
    $footer .= '<p>更改设置后，系统将自动同步更新所需菜单</p>';
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
                <?php if ($model->infoInstance->isSystem) : ?>系统模块<?php endif; ?>
            </small>
        </h1>
        <p class="lead"><?= $model->infoInstance->description ?></p>
        <?php
            $btn[] = Html::submitButton(Yii::t('wocenter/app',
                ($this->context->action->id == 'install' && $model->infoInstance->canInstall)
                    ? 'Install'
                    : 'Save'
            ), ['class' => 'btn btn-success width-200']);
            $btn[] = Html::resetButton(Yii::t('wocenter/app', 'Reset'), ['class' => 'btn btn-default']);
            $btn[] = Html::button(Yii::t('wocenter/app', 'Go back'), ['class' => 'btn btn-default', 'data-widget' => 'goback',]);
            echo implode("\n", $btn);
        ?>
    </div>

<?php
echo $form->field($model, 'id')->textInput(['disabled' => true]);

// 系统模块
if (!$model->infoInstance->isSystem) {
    echo $form->field($model, 'is_system')->radioList(['否', '是']);
}
// 运行模块列表
echo $form->field($model, 'run')->radioList($validRunModuleList);
//echo $form->field($model, 'status')->radioList(\wocenter\libs\Constants::getStatusList());
?>

<?php ActiveForm::end(); ?>