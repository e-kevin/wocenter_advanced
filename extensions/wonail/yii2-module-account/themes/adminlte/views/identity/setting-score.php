<?php
use wonail\adminlte\widgets\ActiveForm;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model wocenter\backend\modules\account\models\SettingScoreForm */
/* @var $form ActiveForm */
/* @var $identityList array 身份列表 */
/* @var $scoreList array 积分数据 */
/* @var $tips string 提示信息 */

$this->title = '奖励配置';
$this->params['breadcrumbs'][] = ['label' => '身份列表', 'url' => ['/account/identity/index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['navSelectPage'] = '/account/identity/index';
$identityId = Yii::$app->getRequest()->get('id');
$type = Yii::$app->getRequest()->get('type');
?>

<?=
\wonail\adminlte\widgets\Callout::widget([
    'type' => \wonail\adminlte\AdminLTE::TYPE_INFO,
    'body' => $tips,
]); ?>

<div class="nav-tabs-custom">
    <ul class="nav nav-tabs">
        <li><?= Html::a('头像配置', ['setting', 'type' => 'avatar', 'id' => $identityId], ['data-pjax' => 1]) ?></li>
        <li class="active"><?= Html::a('奖励配置', '#score', ['data-toggle' => 'tab']) ?></li>
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
        <div class="active tab-pane" id="score">
            <?php
            if ($scoreList) {
                $form = ActiveForm::begin(['box' => false]);
                foreach ($scoreList as $row) {
                    echo $form->field($model, "score_{$row['id']}")->textInput();
                }

                echo $form->defaultButtons();

                ActiveForm::end();
            } else {
                echo Html::tag('div', Html::a('点击此处添加积分配置', ['/data/score-type'], ['data-pjax' => 1]), ['class' => 'text-center']);
            }
            ?>
        </div>
        <!-- /.tab-pane -->
    </div>
    <!-- /.tab-content -->
</div>