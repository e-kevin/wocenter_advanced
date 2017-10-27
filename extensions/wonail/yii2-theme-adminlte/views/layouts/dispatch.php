<?php
use wocenter\Wc;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $message string */
/* @var $waitSecond integer */
/* @var $jumpUrl string */
/* @var $header string */
/* @var $content string */
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
    ]);
    $this->registerMetaTag([
        'name' => 'keywords',
        'content' => Html::encode(Wc::$service->getSystem()->getConfig()->get('WEB_SITE_KEYWORD')),
    ]);
    echo Html::csrfMetaTags();
    $this->title .= ' - ' . Yii::t('wocenter/app', Yii::$app->name);
    $title = Html::encode($this->title);
    echo Html::tag('title', $title);
    ?>
    <?php $this->head() ?>
</head>

<body class="hold-transition">
<?php $this->beginBody() ?>
<div id="content-wrapper">
    <?= $content ?>
</div>
<?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>