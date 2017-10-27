<?php
/* @var $this yii\web\View */
/* @var $generator wocenter\backend\modules\gii\generators\module\Generator */
/* @var string $useInfoClass */

echo "<?php\n";
?>
namespace <?= $generator->getNamespace() . ";\n" ?>

<?= $useInfoClass . "\n" ?>

class Info extends baseInfo
{
<?php if (!$generator->getIsCoreModule()) : ?>

    /**
    * @inheritdoc
    */
    public $name = '<?= $generator->moduleID ?>';

    /**
    * @inheritdoc
    */
    public $description = '<?= $generator->moduleID ?> description';

    /**
    * @inheritdoc
    */
    public $developer = 'Developer';
<?php endif; ?>

}
