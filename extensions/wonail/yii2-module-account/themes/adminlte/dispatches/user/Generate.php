<?php

namespace wocenter\backend\modules\account\themes\adminlte\dispatches\user;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\backend\modules\passport\models\SignupForm;

/**
 * Class Generate
 */
class Generate extends Dispatch
{
    
    public function run()
    {
        $model = new SignupForm();
        if ($model->generateUser()) {
            $this->success('生成用户成功', parent::RELOAD_LIST);
        } else {
            $this->error($model->message);
        }
    }
    
}
