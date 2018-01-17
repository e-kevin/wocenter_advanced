<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = Yii::t('wocenter/app', 'Message prompt');
?>
<div id="error-page" class="error-page animated rubberBand">
    <div style="font-size: 3200%;text-align: center;color: #578;">
        <?= $exception->statusCode ?>
    </div>
    <div class="text-center" style="margin-top: -60px;">
        <h2 class="text-muted"><?= nl2br(Html::encode($message)) ?></h2>
        <p class="text-gray">
            The above error occurred while the Web server was processing your request.
            Please contact us if you think this is a server error. Thank you.
        </p>
    </div>
</div>