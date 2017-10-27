<?php

namespace wocenter\backend\modules\account\themes\adminlte\dispatches\user;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\backend\modules\account\models\BaseUser;
use wocenter\traits\LoadModelTrait;

/**
 * Class View
 */
class View extends Dispatch
{
    
    use LoadModelTrait;
    
    /**
     * @param integer $id
     *
     * @return string|\yii\web\Response
     */
    public function run($id)
    {
        return $this->assign([
            'model' => $this->loadModel(BaseUser::className(), $id),
        ])->display();
    }
    
}
