<?php

namespace wocenter\backend\modules\passport\themes\adminlte\dispatches\security;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\backend\modules\system\models\Verify;
use Yii;

/**
 * Class SendVerify
 */
class SendVerify extends Dispatch
{
    
    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        $model = new Verify();
        if ($model->load(Yii::$app->getRequest()->getBodyParams(), '') &&
            $model->sendVerify($model->identity, true)
        ) {
            $this->success('验证码已发出，请注意查收~');
        } else {
            $this->error($model->message, '', 2);
        }
    }
    
}
