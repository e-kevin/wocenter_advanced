<?php

namespace wocenter\backend\modules\account\themes\adminlte\dispatches\user;

use wocenter\backend\themes\adminlte\components\Dispatch;

/**
 * Class Profile
 */
class Profile extends Dispatch
{
    
    /**
     * @return string|\yii\web\Response
     */
    public function run()
    {
        return $this->display();
    }
    
}
