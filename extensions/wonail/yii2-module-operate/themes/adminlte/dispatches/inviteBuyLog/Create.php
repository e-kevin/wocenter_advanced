<?php
namespace wocenter\backend\modules\operate\themes\adminlte\dispatches\inviteBuyLog;

use wocenter\backend\modules\operate\models\InviteBuyLog;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Create
 */
class Create extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        $model = new InviteBuyLog();
        $model->loadDefaultValues();
        $request = Yii::$app->getRequest();

        if ($request->getIsPost()) {
            if ($model->load($request->getBodyParams()) && $model->save()) {
                $this->success($model->message, ["/{$this->controller->getUniqueId()}"], 2);
            } else {
                $this->error($model->message);
            }
        }

        return $this->assign([
            'model' => $model,
        ])->display();
    }

}
