<?php
use wocenter\backend\modules\system\models\Config;
use wocenter\helpers\StringHelper;
use wonail\adminlte\widgets\ActiveForm;
use wonail\adminlte\widgets\Select2;
use wonail\nestable\Nestable;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $form ActiveForm */
/* @var $model Config */
?>

<?php $form = ActiveForm::begin(); ?>

<?php

foreach ($models->models as $model) {
    switch ($model->type) {
        case Config::TYPE_STRING:
            echo $form->field($models, $model->name)->textInput();
            break;
        case Config::TYPE_TEXT:
            echo $form->field($models, $model->name)->textarea(['rows' => 4]);
            break;
        case Config::TYPE_SELECT:
            echo $form->field($models, $model->name)->widget(Select2::className(), [
                'hideSearch' => true,
            ])->dropDownList(StringHelper::parseString($model->extra), [], false);
            break;
        case Config::TYPE_CHECKBOX:
            echo $form->field($models, $model->name)->checkboxList(StringHelper::parseString($model->extra));
            break;
        case Config::TYPE_RADIO:
            echo $form->field($models, $model->name)->radioList(StringHelper::parseString($model->extra));
            break;
        case Config::TYPE_KANBAN:
            echo $form->field($models, $model->name)
                ->widget(Nestable::className(), [
                    'items' => Json::decode($model->value),
                    'pluginOptions' => [
                        'group' => 1,
                        'maxDepth' => 1,
                    ],
                ]);
            break;
    }
};
?>

<?= $form->defaultButtons() ?>

<?php ActiveForm::end(); ?>