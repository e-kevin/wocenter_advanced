<?php

use yii\helpers\Html;

$this->title = Yii::t('wocenter/app', 'Find Password');
?>
<div class="well">
    <?= Yii::t('wocenter/app', 'Find password successful.', ['email' => $email]) ?>
</div>

<div class="social-auth-links text-center">
    <p>- OR -</p>
    <div class="form-group">
        <?= Html::a(Yii::t('wocenter/app', 'Login Now'), ['/passport/common/login'], ['class' => 'btn btn-success btn-block', 'name' => 'login-button']) ?>
        <?= Html::a(Yii::t('wocenter/app', 'Signup Now'), ['/passport/common/signup'], ['class' => 'btn btn-danger btn-block', 'name' => 'signup-button']) ?>
    </div>
    <p><?= Yii::t('wocenter/app', "We've been doing better for you!"); ?></p>
</div>