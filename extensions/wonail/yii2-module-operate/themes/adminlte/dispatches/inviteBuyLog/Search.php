<?php
namespace wocenter\backend\modules\operate\themes\adminlte\dispatches\inviteBuyLog;

use wocenter\backend\modules\operate\models\InviteBuyLogSearch;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Search
 */
class Search extends Dispatch
{

    /**
     * @return string|\yii\web\Response
     */
    public function run()
    {
        $searchModel = new InviteBuyLogSearch();
        // 加载上次的搜索条件
        $searchModel->load(Yii::$app->getRequest()->getQueryParams());
        if ($searchModel->message) {
            $this->error($searchModel->message, '', 2);
        }

        return $this->assign([
            'model' => $searchModel,
            'action' => ['index'],
        ])->display('_search');
    }

}
