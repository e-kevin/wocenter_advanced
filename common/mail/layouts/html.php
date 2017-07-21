<?php

use yii\helpers\Html;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
        <title><?= Html::encode($this->title) ?></title>
        <style type="text/css">
            .body{padding:10px;font-family:Microsoft Yahei, Verdana, Simsun, sans-serif;font-size:17px;color:#707070;width:760px;margin:0 auto 30px auto;display:table;}
            .header{margin-bottom: 10px;}
            .body .tips{font-size:41px; color:#2672ec}
            .body .content{margin-top: 20px;font-size: 14px;color:#2a2a2a;}
            a:active,a:visited,a:link{color:#2672ec !important;text-decoration:none !important;}a:hover{color:#4284ee !important;text-decoration:none !important;}
            .body .footer{float: right;text-align:right;margin-top: 30px;}
        </style>
        <?php $this->head() ?>
    </head>
    <body>
        <?php $this->beginBody() ?>
        <div class="body">
            <div class="header"><?= Yii::$app->params['app.name'] ?></div>
            <div class="tips"><?= Html::encode($this->title) ?></div>
            <div class="content">
                <?= $content ?>
            </div>
            <div class="footer">
                <div>感谢您对我们一如既往的支持 ^_^</div>
                <div>—— <?= Html::a('Wc', Yii::$app->params['app.url']) ?></div>
            </div>
            <div style="text-align: center;font-size: 12px;clear: both">
                <p>此邮件是自动生成的 - 不要回复此邮件</p>
                <p>我们同样重视您的账户安全</p>
            </div>
        </div>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
