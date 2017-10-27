<?php

namespace wocenter\backend\modules\passport\events;

use wocenter\backend\modules\account\models\BackendUser;
use wocenter\events\UserEvent;
use Yii;

/**
 * 验证管理员合法性
 *
 * @auth E-Kevin <e-kevin@qq.com>
 */
class validateAdministrator
{
    
    /**
     * 验证管理员合法性
     *
     * @param UserEvent $event
     */
    public function run(UserEvent $event)
    {
        // 检测用户是否为后台管理员
        /** @var BackendUser $admin */
        $admin = $event->identity->getBackendUsers()->one();
        if (!$admin) {
            $event->sender->message = Yii::t('wocenter/app', 'User does not exist.');
            $event->isValid = false;
        } elseif ($admin->status == 0) {
            $event->sender->message = Yii::t('wocenter/app', 'Your account is forbidden from logging into the backend system.');
            $event->isValid = false;
        }
    }
    
}
