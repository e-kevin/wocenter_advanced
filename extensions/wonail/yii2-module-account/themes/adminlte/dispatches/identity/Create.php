<?php

namespace wocenter\backend\modules\account\themes\adminlte\dispatches\identity;

use wocenter\backend\modules\account\events\operateIdentityProfiles;
use wocenter\backend\modules\account\models\ExtendProfile;
use wocenter\backend\modules\account\models\Identity;
use wocenter\backend\modules\account\models\IdentityGroup;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Create
 */
class Create extends Dispatch
{
    
    /**
     * @return string|\yii\web\Response
     */
    public function run()
    {
        $model = new Identity();
        $model->loadDefaultValues();
        $request = Yii::$app->getRequest();
        $post = $request->getBodyParams();
        
        $model->on(Identity::EVENT_AFTER_INSERT, [new operateIdentityProfiles(), 'create']);
        
        if ($request->getIsPost()) {
            $model->profileId = $post[$model->formName()]['profileId'];
            if ($model->load($post) && $model->save()) {
                $this->success($model->message, ["/{$this->controller->getUniqueId()}"]);
            } else {
                $this->error($model->message);
            }
        }
        
        return $this->assign([
            'model' => $model,
            'identityGroup' => (new IdentityGroup())->getSelectList(),
            'profiles' => (new ExtendProfile())->getSelectList(),
        ])->display();
    }
    
}
