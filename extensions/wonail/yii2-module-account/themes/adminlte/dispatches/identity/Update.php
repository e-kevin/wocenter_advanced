<?php

namespace wocenter\backend\modules\account\themes\adminlte\dispatches\identity;

use wocenter\backend\modules\account\events\operateIdentityProfiles;
use wocenter\backend\modules\account\models\ExtendProfile;
use wocenter\backend\modules\account\models\Identity;
use wocenter\backend\modules\account\models\IdentityGroup;
use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\traits\LoadModelTrait;
use Yii;

/**
 * Class Update
 */
class Update extends Dispatch
{
    
    use LoadModelTrait;
    
    /**
     * @param integer $id
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\NotFoundHttpException
     */
    public function run($id)
    {
        /** @var Identity $model */
        $model = $this->loadModel(Identity::className(), $id);
        $request = Yii::$app->getRequest();
        $post = $request->getBodyParams();
        
        $model->on(Identity::EVENT_AFTER_UPDATE, [new operateIdentityProfiles(), 'update']);
        
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
