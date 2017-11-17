<?php

use yii\helpers\Inflector;

/* @var $this yii\web\View */
/* @var $generator wocenter\backend\modules\gii\generators\module\Generator */

$controllerName = Inflector::camelize($generator->getDefaultRoute());
$controllerClass = $generator->getDefaultController()
    ? $generator->getDefaultController()->className()
    : 'backend\core\Controller';

echo "<?php\n";
?>
namespace <?= $generator->getControllerNamespace() ?>;

use <?= $controllerClass ?> as Controller;

/**
* <?= $controllerName ?> controller for the `<?= $generator->moduleID ?>` module
*/
class <?= $controllerName ?>Controller extends Controller
{

<?php if ($generator->useDispatch) : ?>
    /**
    * @inheritdoc
    */
    public function dispatches()
    {
    return [
    'index',
    ];
    }
<?php else: ?>
    /**
    * @return string
    */
    public function actionIndex()
    {
    return $this->render('index');
    }
<?php endif; ?>

}
