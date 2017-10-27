<?php
namespace wocenter\backend\modules\operate\themes\adminlte\dispatches\invite;

use wocenter\backend\modules\operate\models\Invite;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Clear
 */
class Clear extends Dispatch
{

    public function run()
    {
        (new Invite())->clearCode();
        $this->success('', self::RELOAD_LIST);
    }

}
