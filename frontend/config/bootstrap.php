<?php
Yii::$classMap['wocenter\Wc'] = '@frontend/core/Wc.php';
Yii::$container->set('wocenter\core\ServiceLocator', 'frontend\core\ServiceLocator');
Yii::setAlias('@wocenter/backend/modules/extension', '@extensions/wocenter/yii2-module-extension');
