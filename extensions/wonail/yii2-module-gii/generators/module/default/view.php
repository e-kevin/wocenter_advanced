<?php
/* @var $this yii\web\View */
/* @var $generator wocenter\backend\modules\gii\generators\module\Generator */

echo "<?php\n";
?>
/* @var $this yii\web\View */

$this->title = null;
?>

<!-- 自定义该页面时可直接从此行开始删除下面所有代码 -->
<?php if ($generator->useDispatch) : ?>
<?= "<?php\n"; ?>
$dispatchService = \wocenter\Wc::$service->getDispatch();
// 开发者调度器目录
$developerDispatchPath = $dispatchService->getView()->getDeveloperThemePath('dispatches');
$dispatchName = $dispatchService->normalizeDispatchName($this->context->action->id);
$route = $this->context->uniqueId . '/' . $dispatchName;
// 当前操作的开发者调度器
$developerDispatch = Yii::getAlias('@'.substr($developerDispatchPath . '/' . $route, 1).'.php');
?>

<div class="box box-default">
    <div class="box-body">
        <div class="jumbotron text-center">
            <h1>That's amazing!</h1>

            <p class="lead">It's so easy!</p>
            <p><a class="btn btn-lg btn-success" href="https://github.com/Wonail/wocenter_doc/blob/master/guide/zh-CN/dispatch.md" target="_blank">
                    关于 Dispatch 调度</a>
            </p>
        </div>

        <div class="<?= "<?= " ?>$this->context->action->uniqueId ?>">
            <h1>当前路由：<?= "<?= " ?>$this->context->action->uniqueId ?></h1>
            <p>
                这是操作<code><?= "<?= " ?>$this->context->action->id ?></code>的视图内容。
                该操作属于<code><?= "<?= " ?>$this->context->module->id ?></code>模块的<code><?= "<?= " ?>get_class($this->context) ?></code>
                控制器。
            </p>
            <p>
                您可以通过编辑以下文件来自定义此页面：<br>
                <code><?= "<?= " ?>__FILE__ ?></code>
            </p>
            <p>
                <?= "<?php " ?>if (is_file($developerDispatch) && !$dispatchService->getIsRunningCoreModule()) : ?>
                或通过修改<code><?= "<?= " ?>$this->context->action->id ?></code>操作专属的<code><?= "<?= " ?>$dispatchName ?></code>调度器文件来实现具体功能：<br>
                <code><?= "<?= " ?>$developerDispatch ?></code>
                <?= "<?php " ?>endif; ?>
            </p>
        </div>
    </div><!-- /.box-body -->
</div><!-- /.box -->
<?php else : ?>
<div class="<?= "<?= " ?>$this->context->action->uniqueId ?>">
    <h1>当前路由：<?= "<?= " ?>$this->context->action->uniqueId ?></h1>
    <p>
        这是操作<code><?= "<?= " ?>$this->context->action->id ?></code>的视图内容。
        该操作属于<code><?= "<?= " ?>$this->context->module->id ?></code>模块的<code><?= "<?= " ?>get_class($this->context) ?></code>
        控制器。
    </p>
    <p>
        您可以通过编辑以下文件来自定义此页面：<br>
        <code><?= "<?= " ?>__FILE__ ?></code>
    </p>
</div>
<?php endif; ?>