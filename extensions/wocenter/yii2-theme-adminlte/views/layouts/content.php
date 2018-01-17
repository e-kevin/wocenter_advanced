<?php
use wonail\adminlte\widgets\FlashAlert;
use rmrevin\yii\fontawesome\FA;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

?>
<div class="content-wrapper" id="content-wrapper"
     data-title="<?= Html::encode($this->title . ' - ' . Yii::t('wocenter/app', Yii::$app->name)) ?>">
    <?php if (!isset($this->params['dispatchView'])) : ?>
        <section class="content-header">
            <h1>
                <?php
                if ($this->title !== null) {
                    echo Html::encode($this->title);
                    echo isset($this->params['breadcrumb_description']) ?
                        '&nbsp;<small>' . Html::encode($this->params['breadcrumb_description']) . '</small>' :
                        '';
                } else {
                    echo Inflector::camel2words(
                        Inflector::id2camel($this->context->module->id)
                    );
                    echo ($this->context->module->id !== Yii::$app->id) ? '&nbsp;<small>Module</small>' : '';
                }
                ?>
            </h1>

            <?=
            Breadcrumbs::widget([
                'homeLink' =>
                    [
                        'label' => FA::i(FA::_DASHBOARD) . Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                        'encode' => false,
                    ],
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ])
            ?>
        </section>
    <?php endif; ?>

    <section class="content">
        <?= FlashAlert::widget() ?>

        <?= $content ?>
    </section>

    <?= Html::hiddenInput('navSelectPage',
        isset($this->params['navSelectPage'])
            ? Url::toRoute($this->params['navSelectPage'])
            : Url::toRoute('/')
        , ['id' => 'navSelectPage']);
    ?>
</div>