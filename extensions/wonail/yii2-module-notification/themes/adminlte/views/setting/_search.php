<?php
use wocenter\backend\modules\notification\models\NotifySearch;
use wocenter\libs\Constants;
use wocenter\Wc;
use wonail\adminlte\widgets\ActiveForm;
use wonail\adminlte\widgets\Select2;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model NotifySearch */
/* @var $form ActiveForm */
/* @var $action string|array */

$radioList = ArrayHelper::merge([Constants::UNLIMITED => Yii::t('wocenter/app', 'Unlimited')], Constants::getYesOrNoList());
?>

<?php $form = ActiveForm::begin([
    'id' => 'search_div',
    'action' => $action,
    'method' => 'get',
    'box' => false,
]); ?>

<?= $form->field($model, 'node') ?>

<?= $form->field($model, 'email_sender')->widget(Select2::className(), [
    'options' => [
        'placeholder' => Yii::t('wocenter/app', 'Please choose'),
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
    'hideSearch' => true,
])->dropDownList(Wc::$service->getSystem()->getConfig()->extra('EMAIL_SENDER'))->hint(false) ?>

<?= $form->field($model, 'send_message')->radioList($radioList) ?>

<?php ActiveForm::end(); ?>
