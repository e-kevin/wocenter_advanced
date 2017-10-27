<?php
namespace wocenter\backend\modules\operate\themes\adminlte\dispatches\inviteLog;

use wocenter\backend\modules\operate\models\InviteLogSearch;
use wocenter\backend\modules\operate\models\InviteType;
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
        $searchModel = new InviteLogSearch();
        $searchModel->load(Yii::$app->getRequest()->getQueryParams());

        return $this->assign([
            'model' => $searchModel,
            'action' => ['index'],
            'inviteTypeList' => (new InviteType())->getSelectList(),
        ])->display('_search');
    }

}
