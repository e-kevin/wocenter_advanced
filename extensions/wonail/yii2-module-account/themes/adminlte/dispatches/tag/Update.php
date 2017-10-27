<?php

namespace wocenter\backend\modules\account\themes\adminlte\dispatches\tag;

use wocenter\backend\modules\data\models\Tag;
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
        /** @var Tag $model */
        $model = $this->loadModel(Tag::className(), $id);
        $request = Yii::$app->getRequest();
        
        if ($request->getIsPost()) {
            if ($model->load($request->getBodyParams()) && $model->save()) {
                $this->success($model->message, [
                    "/{$this->controller->getUniqueId()}",
                    'pid' => $model->parent_id,
                ]);
            } else {
                $this->error($model->message);
            }
        }
        
        $breadcrumbs = $model->getBreadcrumbs($model->id, '标签列表', '/account/tag/index');
        
        return $this->assign([
            'model' => $model,
            'tagList' => $model->getTreeSelectList($model->getList()),
            'breadcrumbs' => $breadcrumbs,
            'title' => $breadcrumbs[$model->id]['label'],
        ])->display();
    }
    
}
