<?php

/* @var $this \yii\web\View */
/* @var $content string */

use wocenter\frontend\themes\basic\assetBundle\SiteAsset;
use wocenter\Wc;
use wonail\adminlte\widgets\FlashAlert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;

SiteAsset::register($this);
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
    echo Html::tag('title', Html::encode($this->title));
    ?>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = [
        ['label' => 'Home', 'url' => ['/site/index']],
        ['label' => 'About', 'url' => ['/site/about']],
        ['label' => 'Contact', 'url' => ['/site/contact']],
    ];
    if (Yii::$app->hasModule('passport')) {
        if (Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => 'Signup', 'url' => ['/passport/common/signup']];
            $menuItems[] = ['label' => 'Login', 'url' => ['/passport/common/login']];
        } else {
            $menuItems[] = '<li>'
                . Html::a('Logout (' . Yii::$app->user->identity->username . ')', ['/passport/common/logout'], [
                    'class' => 'btn btn-link logout',
                    'data-method' => 'post',
                ])
                . '</li>';
        }
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= FlashAlert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">
            <strong>Copyright &copy; <?= Yii::$app->params['app.copyright'] . ' ' . Yii::$app->params['app.name'] ?>
                .</strong>
            All rights reserved.
        </p>

        <p class="pull-right">技术支持 <?= Html::a('WoCenter', 'https://github.com/Wonail/wocenter', [
                'target' => '_blank',
            ]) ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
