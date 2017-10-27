<?php

namespace wocenter\backend\modules\menu\themes\adminlte\dispatches\detail;

use wocenter\backend\modules\menu\models\MenuCategory;
use wocenter\backend\modules\menu\models\MenuSearch;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;

/**
 * Class Index
 */
class Index extends Dispatch
{
    
    /**
     * @param string $category
     * @param integer $pid
     *
     * @return string|\yii\web\Response
     */
    public function run($category = '', $pid = 0)
    {
        $searchModel = new MenuSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());
        
        if ($searchModel->message) {
            $this->error($searchModel->message, '', 2);
        }
        
        $categoryName = MenuCategory::find()->select('name')->where(['id' => $category])->scalar();
        $breadcrumbs = $searchModel->getBreadcrumbs(
            $pid,
            $categoryName,
            '/menu/detail/index',
            ['category' => $category],
            [
                -1 => ['label' => '菜单管理', 'url' => ['/menu/category/index']],
            ]
        );
        
        return $this->assign([
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'category' => $category,
            'pid' => $pid,
            'breadcrumbs' => $breadcrumbs,
            'title' => $breadcrumbs[$pid]['label'],
        ])->display();
    }
    
}
