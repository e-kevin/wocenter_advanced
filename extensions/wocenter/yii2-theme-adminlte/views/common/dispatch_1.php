<?php
use yii\helpers\Html;
use rmrevin\yii\fontawesome\FA;

/* @var $this yii\web\View */
/* @var $message string */
/* @var $status integer */
/* @var $waitSecond integer */
/* @var $jumpUrl string */

if ($status) {
    $this->title = Yii::t('wocenter/app', 'Successful operation.');
    $icon = FA::i(FA::_CHECK_CIRCLE, ['class' => "text-green"]);
    $name = 'SUCCESS';
} else {
    $this->title = Yii::t('wocenter/app', 'Operation failure.');
    $icon = FA::i(FA::_MINUS_CIRCLE, ['class' => "text-red"]);
    $name = 'ERROR';
}
?>
<div id="error-page" class="error-page animated fadeIn">
    <h2 class="headline"><?= $icon; ?></h2>

    <div class="error-content">
        <h3><b><?= $header; ?></b></h3>

        <p>
            <?= nl2br(Html::encode($message)) ?>
        </p>

        <div>
            <p class="text-muted">
                <small><b id="wait"><?= $waitSecond ?></b>
                    <?= Yii::t('wocenter/app', 'After a few seconds the page will automatically jump.') ?>
                </small>
            </p>
            <?php
            if (!Yii::$app->user->isGuest) {
                echo Html::a('返回首页', Yii::$app->getHomeUrl(), [
                    'class' => 'btn btn-sm btn-info',
                ]);
            }
            ?>
            <a id="href" class="btn btn-sm btn-success" href="<?= $jumpUrl ?>"><?= Yii::t('wocenter/app', 'Jump to url.') ?></a>
            <a class="btn btn-sm btn-danger" onclick="stop()" data-no-pjax role="button"><?= Yii::t('wocenter/app', 'Stop to jump.') ?></a>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function () {
        var wait = document.getElementById('wait'),
            href = document.getElementById('href').href;

        var interval = setInterval(function () {
            var time = --wait.innerHTML;
            if (time <= 0) {
                location.href = href;
                clearInterval(interval);
            }
            ;
        }, 1000);
        window.stop = function () {
            clearInterval(interval);
        }
    })();
</script>