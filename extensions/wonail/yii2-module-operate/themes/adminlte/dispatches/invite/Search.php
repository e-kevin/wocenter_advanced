<?php
namespace wocenter\backend\modules\operate\themes\adminlte\dispatches\invite;

use wocenter\backend\modules\operate\models\InviteSearch;
use wocenter\backend\modules\operate\models\InviteType;
use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\libs\Constants;
use Yii;
use yii\helpers\ArrayHelper;

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
        $searchModel = new InviteSearch();
        $searchModel->load(Yii::$app->getRequest()->getQueryParams());

        return $this->assign([
            'model' => $searchModel,
            'action' => ['index'],
            'statusList' => ArrayHelper::merge([Constants::UNLIMITED => Yii::t('wocenter/app', 'Unlimited')], $searchModel->getCodeStatus()),
            'inviteTypeList' => (new InviteType())->getSelectList(),
        ])->display('_search');
    }

}
