<?php
Yii::$classMap['wocenter\Wc'] = '@backend/core/Wc.php';
Yii::$container->set('wocenter\core\ServiceLocator', 'backend\core\ServiceLocator');
Yii::setAlias('@wocenter/backend/modules/extension', '@extensions/wonail/yii2-module-extension');