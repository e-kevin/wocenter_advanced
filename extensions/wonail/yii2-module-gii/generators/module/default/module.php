<?php
/* @var $this yii\web\View */
/* @var $generator wocenter\backend\modules\gii\generators\module\Generator */
/* @var string $useModuleClass */

echo "<?php\n";
?>
namespace <?= $generator->getNamespace() . ";\n" ?>

<?= $useModuleClass . "\n" ?>

class Module extends baseModule
{
<?php if (!$generator->getIsCoreModule()) : ?>

    /**
    * @inheritdoc
    */
    public $controllerNamespace = '<?= $generator->getControllerNamespace() ?>';
<?php endif; ?>

}
