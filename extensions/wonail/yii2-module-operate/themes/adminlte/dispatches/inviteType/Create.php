<?php
namespace wocenter\backend\modules\operate\themes\adminlte\dispatches\inviteType;

use wocenter\backend\modules\account\models\Identity;
use wocenter\backend\modules\data\models\UserScoreType;
use wocenter\backend\modules\operate\models\InviteType;
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
        $model = new InviteType();
        $model->loadDefaultValues();
        $request = Yii::$app->getRequest();

        if ($request->getIsPost()) {
            if ($model->load($request->getBodyParams()) && $model->save()) {
                $this->success($model->message, ["/{$this->controller->getUniqueId()}"]);
            } else {
                $this->error($model->message);
            }
        }

        return $this->assign([
            'model' => $model,
            'scoreList' => (new UserScoreType())->getSelectList(),
            'inviteIdentityList' => (new Identity())->getSelectList(['is_invite' => 1]), // 可邀请注册的身份列表
        ])->display();
    }

}
