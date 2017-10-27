<?php
use wonail\adminlte\widgets\ActiveForm;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model \wocenter\backend\modules\account\models\IdentityConfig */
/* @var $form ActiveForm */
/* @var $identityList array 身份列表 */

$this->title = '头像配置';
$this->params['breadcrumbs'][] = ['label' => '身份列表', 'url' => ['/account/identity/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/account/identity/index';
$identityId = Yii::$app->getRequest()->get('id');
$type = Yii::$app->getRequest()->get('type');
?>

<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li class="active"><?= Html::a('头像配置', '#avatar', ['data-toggle' => 'tab']) ?></li>
        <li><?= Html::a('奖励配置', ['setting', 'type' => 'score', 'id' => $identityId], ['data-pjax' => 1]) ?></li>
        <li><?= Html::a('头衔配置', ['setting', 'type' => 'rank', 'id' => $identityId], ['data-pjax' => 1]) ?></li>
        <li><?= Html::a('标签配置', ['setting', 'type' => 'tag', 'id' => $identityId], ['data-pjax' => 1]) ?></li>
        <li><?= Html::a('档案配置', ['setting', 'type' => 'profile', 'id' => $identityId], ['data-pjax' => 1]) ?></li>
        <li><?= Html::a('注册配置', ['setting', 'type' => 'signup', 'id' => $identityId], ['data-pjax' => 1]) ?></li>
        <li class="dropdown pull-right">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <?= $identityList[$identityId] ?> <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <?php
                foreach ($identityList as $id => $name) {
                    echo Html::tag('li', Html::a($name, [
                        'setting',
                        'type' => $type,
                        'id' => $id,
                    ], [
                        'data-pjax' => 1,
                    ]));
                }
                ?>
            </ul>
        </li>
    </ul>
    <div class="tab-content">
        <div class="active tab-pane" id="avatar">
        </div>
        <!-- /.tab-pane -->
    </div>
    <!-- /.tab-content -->
</div>