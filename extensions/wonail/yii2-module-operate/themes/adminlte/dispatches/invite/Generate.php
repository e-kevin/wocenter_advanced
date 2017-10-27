<?php
namespace wocenter\backend\modules\operate\themes\adminlte\dispatches\invite;

use wocenter\backend\modules\operate\models\Invite;
use wocenter\backend\modules\operate\models\InviteType;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Generate
 */
class Generate extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        $model = new Invite();
        $model->loadDefaultValues();
        $request = Yii::$app->getRequest();

        if ($request->getIsPost()) {
            if ($model->load($request->getBodyParams()) && $model->generate()) {
                $this->success($model->message, ["/{$this->controller->getUniqueId()}"]);
            } else {
                $this->error($model->message);
            }
        }

        return $this->assign([
            'model' => $model,
            'inviteTypeList' => (new InviteType())->getSelectList(),
        ])->display();
    }

}
