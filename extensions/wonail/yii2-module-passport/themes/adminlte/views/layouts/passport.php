<?php
use wocenter\backend\modules\passport\themes\adminlte\assetBundle\PassportAsset;
use wocenter\Wc;
use yii\helpers\Html;
use yii\widgets\Pjax;
use kartik\dialog\Dialog;

/* @var $this \yii\web\View */
/* @var $content string */

PassportAsset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <?php
    $this->registerMetaTag([
        'charset' => Yii::$app->charset,
    ]);
    $this->registerMetaTag([
        'http-equiv' => 'X-UA-Compatible',
        'content' => 'IE=edge',
    ]);
    $this->registerMetaTag([
        'name' => 'viewport',
        'content' => 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no',
    ]);
    $this->registerMetaTag([
        'name' => 'description',
        'content' => Html::encode(Wc::$service->getSystem()->getConfig()->get('WEB_SITE_DESCRIPTION')),
    ], 'description');
    $this->registerMetaTag([
        'name' => 'keywords',
        'content' => Html::encode(Wc::$service->getSystem()->getConfig()->get('WEB_SITE_KEYWORD')),
    ], 'keywords');
    echo Html::csrfMetaTags();
    $this->title .= ' - ' . Yii::t('wocenter/app', Yii::$app->name);
    echo Html::tag('title', Html::encode($this->title));
    ?>
    <?php $this->head() ?>
</head>

<body class="hold-transition">
<?php $this->beginBody() ?>
<?php
echo Dialog::widget([
    'libName' => 'successDialog',
    'options' => [
        'closable' => true,
        'type' => Dialog::TYPE_SUCCESS,
    ],
]);
echo Dialog::widget([
    'libName' => 'errorDialog',
    'options' => [
        'closable' => true,
        'type' => Dialog::TYPE_DANGER,
    ],
]);
?>

<div class="login-box">
    <div class="login-logo">
        <?= Html::encode(Yii::t('wocenter/app', Yii::$app->name)) ?>
    </div>
    <p class="login-box-msg"><?= Yii::t('wocenter/app', 'Focus on digital operation management.') ?></p>
    <div class="login-box-body">
        <?php Pjax::begin([
            'formSelector' => false,
        ]); ?>
        <?= $content ?>
        <?php Pjax::end(); ?>
    </div>
</div>

<footer class="footer">
    <p class="text-center text-muted">&copy;
        <?= Yii::$app->params['app.copyright'] . ' ' . Yii::$app->params['app.name'] ?>
        All Rights Reserved.</p>
</footer>

<?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>
