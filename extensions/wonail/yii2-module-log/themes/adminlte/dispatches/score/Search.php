<?php

namespace wocenter\backend\modules\log\themes\adminlte\dispatches\score;

use wocenter\backend\modules\data\models\UserScoreType;
use wocenter\backend\modules\log\models\UserScoreLogSearch;
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
        $searchModel = new UserScoreLogSearch();
        $searchModel->load(Yii::$app->getRequest()->getQueryParams());
        
        return $this->assign([
            'model' => $searchModel,
            'action' => ['index'],
            'typeList' => ArrayHelper::merge([Constants::UNLIMITED => Yii::t('wocenter/app', 'Unlimited')], (new UserScoreType())->getSelectList()),
            'actionList' => ArrayHelper::merge([Constants::UNLIMITED => Yii::t('wocenter/app', 'Unlimited')], $searchModel->actionList),
        ])->display('_search');
    }
    
}
