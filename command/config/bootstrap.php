<?php
Yii::$classMap['wocenter\Wc'] = '@command/core/Wc.php';
Yii::$container->set('wocenter\core\ServiceLocator', 'command\core\ServiceLocator');
Yii::setAlias('@wocenter/backend/modules/extension', '@extensions/wonail/yii2-module-extension');
