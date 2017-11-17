<?php

namespace backend\core;

use wocenter\Wc;
use yii\data\ActiveDataProvider as baseActiveDataProvider;

class ActiveDataProvider extends baseActiveDataProvider
{
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->setPagination(['pageSize' => Wc::$service->getSystem()->getConfig()->get('LIST_ROWS')]);
    }
    
}
