<?php

use wocenter\backend\themes\adminlte\assetBundle\DispatchAsset;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $message string */
/* @var $waitSecond integer */
/* @var $jumpUrl string */
/* @var $header string */
/* @var $status boolean */

if (Yii::$app->hasModule('passport')) {
    $is_guest = Yii::$app->getUser()->getIsGuest();
} else {
    $is_guest = true;
}
if (Yii::$app->getErrorHandler()->exception === null) {
    if ($is_guest) {
        $this->context->layout = '/dispatch';
    }
}
$padding_top = 'padding-top: 50px';
DispatchAsset::register($this);
$this->title = Yii::t('wocenter/app', 'Message prompt');
$this->params['dispatchView'] = true;
?>
<div class="container" style="<?= $padding_top ?>">
    <div class="row text-center">
        <div class="col-md-6 col-md-offset-3">
            <h1 style="font-size: 40px; margin-bottom: 10px;"><b><?= $header ?></b></h1>

            <p><?= \yii\helpers\HtmlPurifier::process($message) ?></p>

            <p class="text-muted">
                <small><b
                            id="wait"><?= $waitSecond ?: 3 ?></b><?= Yii::t('wocenter/app', 'After a few seconds the page will automatically jump.') ?>
                    <a onclick="stopJumpUrl();" data-no-pjax=""
                       role="button"><?= Yii::t('wocenter/app', 'Stop to jump.') ?></a>
                </small>
            </p>

            <div class="row">
                <div class="col-md-6">
                    <?php
                    if (Yii::$app->hasModule('passport')) {
                        if ($is_guest) {
                            echo Html::a(Yii::t('wocenter/app', 'Login again'), Yii::$app->getUser()->loginUrl, [
                                'class' => 'btn btn-success btn-block',
                                'data-pjax' => true,
                            ]);
                        } else {
                            echo Html::a(Yii::t('wocenter/app', 'Return home'), ['/'], [
                                'class' => 'btn btn-success btn-block',
                                'data-pjax' => true,
                            ]);
                        }
                    } else {
                        echo Html::a(Yii::t('wocenter/app', 'Return home'), ['/'], [
                            'class' => 'btn btn-success btn-block',
                            'data-pjax' => true,
                        ]);
                    }
                    ?>
                </div>
                <div class="col-md-6">
                    <?= Html::a(Yii::t('wocenter/app', 'Jump to url.'), $jumpUrl, [
                        'id' => 'href',
                        'class' => 'btn btn-primary btn-block',
                        'onclick' => 'stopJumpUrl();',
                        'data-pjax' => strpos('javascript', $jumpUrl) !== false ? 'true' : null,
                    ]) ?>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>