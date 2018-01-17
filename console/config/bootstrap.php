<?php
Yii::$classMap['wocenter\Wc'] = '@console/core/Wc.php';
Yii::$container->set('wocenter\core\ServiceLocator', 'console\core\ServiceLocator');
Yii::setAlias('@wocenter/backend/modules/extension', '@extensions/wocenter/yii2-module-extension');
