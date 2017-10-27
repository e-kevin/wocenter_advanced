<?php
namespace wocenter\backend\modules\operate\themes\adminlte\dispatches\inviteType;

use wocenter\backend\modules\account\models\Identity;
use wocenter\backend\modules\data\models\UserScoreType;
use wocenter\backend\modules\operate\models\InviteType;
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
        /** @var InviteType $model */
        $model = $this->loadModel(InviteType::className(), $id);
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
            'scoreList' => (new UserScoreType())->getSelectList(), // 积分列表
            'inviteIdentityList' => (new Identity())->getSelectList(['is_invite' => 1]), // 可邀请注册的身份列表
        ])->display();
    }

}
