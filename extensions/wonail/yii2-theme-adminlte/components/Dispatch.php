<?php

namespace wocenter\backend\themes\adminlte\components;

use Yii;
use yii\helpers\Url;
use yii\web\Request;
use yii\web\Response;

/**
 * AdminLTE主题调度器的实现类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class Dispatch extends \wocenter\core\Dispatch
{
    
    /**
     * @var string 调度器视图文件
     */
    public $viewFile = '@wocenter/backend/themes/adminlte/views/common/dispatch';
    
    /**
     * @inheritdoc
     */
    public function success($message = '', $jumpUrl = '', $data = [])
    {
        $this->dispatchJump($message ?: Yii::t('wocenter/app', 'Successful operation.'), 1, $jumpUrl, $data);
    }
    
    /**
     * @inheritdoc
     */
    public function error($message = '', $jumpUrl = '', $data = [])
    {
        $this->dispatchJump($message ?: Yii::t('wocenter/app', 'Operation failure.'), 0, $jumpUrl, $data);
    }
    
    /**
     * 默认跳转操作，支持错误跳转和正确跳转
     * 如果`$jumpUrl`跳转地址包含`js:`字符，表示该跳转地址交由JS处理
     *
     * @param string|array $message 提示信息
     * @param integer $status 状态 1:success 0:error
     * @param string|array $jumpUrl 页面跳转地址
     * @param array|integer $data
     */
    private function dispatchJump($message = '', $status = 1, $jumpUrl = '', $data = [])
    {
        if (!empty($jumpUrl)) {
            if (is_string($jumpUrl) && ($pos = strpos($jumpUrl, 'js:')) !== false) {
                $jumpUrl = $this->isFullPageLoad() ? '' : substr($jumpUrl, $pos + 3);
            } else {
                $jumpUrl = Url::to($jumpUrl);
            }
        }
        
        // 设置跳转时间
        if (is_int($data)) {
            $params['waitSecond'] = $data;
        } elseif (is_array($data)) {
            $params = $data;
            if (!isset($params['waitSecond'])) {
                $params['waitSecond'] = $status ? 1 : 3;
            }
        }
        $params['message'] = $message;
        $params['status'] = $status;
        
        if ($this->isFullPageLoad()) {
            $params['jumpUrl'] = $jumpUrl ?: "javascript:history.back(-1);";
            $params['header'] = $status ? Yii::t('wocenter/app', 'Success') : Yii::t('wocenter/app', 'Sorry');
            Yii::$app->getResponse()->data = $this->display($this->viewFile, $params);
        } else {
            $params['jumpUrl'] = $jumpUrl;
            $this->controller->asJson($params);
        }
        
        Yii::$app->end();
    }
    
    /**
     * 响应式渲染视图，支持AJAX,PJAX,GET的请求方式
     *
     * 根据请求方式自动渲染视图文件，并可自动定位当前动作所属的视图文件和自动加载视图所需的模板变量
     *
     * @param string|null $view 默认根据调用此方法的调度器类名去渲染所属视图模板文件
     * @param array $assign 需要赋值的模板变量，会和已经存在的变量合并
     *
     * @return string|Response
     */
    public function display($view = null, $assign = [])
    {
        // 没有指定渲染的视图文件名，则默认渲染当前调度器ID的视图文件
        $view = $view ?: $this->id;
        $assign = array_merge($this->_assign, $assign);
        /** @var Request $request */
        $request = Yii::$app->getRequest();
        if ($request->getIsAjax()) {
            if ($request->getIsPjax()) {
                // 使用布局文件并加载资源
                return $this->controller->renderContent($this->controller->renderAjax($view, $assign));
            } else {
                // 存在系统异常，则显示异常页面
                if (($exception = Yii::$app->getErrorHandler()->exception !== null)) {
                    return $this->controller->renderAjax($view, $assign);
                } else {
                    // 如果操作为只更新局部列表数据、翻页、搜索页面、数据切换时，则禁用布局文件和资源加载，直接解析视图文件
                    if (
                        $request->get('reload-list') // 更新局部列表数据
                        || $request->get('page') // 翻页
                        || $request->get('from-search') // 搜索页面
                        || $request->get('_toggle') // 数据切换时
                    ) {
                        return $this->controller->renderPartial($view, $assign);
                    } else {
                        return $this->controller->renderAjax($view, $assign);
                    }
                }
            }
        } else {
            return $this->controller->render($view, $assign);
        }
    }
    
}
