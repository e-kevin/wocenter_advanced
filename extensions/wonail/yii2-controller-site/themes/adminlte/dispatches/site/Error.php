<?php

namespace wocenter\backend\controllers\site\themes\adminlte\dispatches\site;

use Exception;
use wocenter\backend\themes\adminlte\components\Dispatch;
use Yii;
use yii\base\UserException;
use yii\web\NotFoundHttpException;

/**
 * 错误控制器
 *
 * @see \yii\web\ErrorAction
 */
class Error extends Dispatch
{
    
    /**
     * @var string 默认提示消息。默认为Yii::t('wocenter/app', "An internal server error occurred.").
     */
    public $defaultMessage;
    
    /**
     * @var integer 等待多久自动跳转到指定页面
     */
    public $waitSecond = 5;
    
    /**
     * @var \Exception the exception object, normally is filled on [[init()]] method call.
     */
    protected $exception;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->exception = $this->findException();
        
        if ($this->defaultMessage === null) {
            $this->defaultMessage = Yii::t('wocenter/app', 'An internal server error occurred.');
        }
    }
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->error($this->getExceptionMessage(), '', $this->waitSecond);
    }
    
    /**
     * 异常处理器未获取到则自动赋值为[[\yii\web\NotFoundHttpException]]
     *
     * @return \Exception
     */
    protected function findException()
    {
        if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
            $exception = new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        
        return $exception;
    }
    
    /**
     * 异常显示消息
     * 异常由[[\yii\base\UserException]]触发，则获取用户自定义的异常消息，如果消息为空，则返回默认[[$defaultMessage]]消息。
     *
     * @return string
     */
    protected function getExceptionMessage()
    {
        if ($this->exception instanceof UserException) {
            return $this->exception->getMessage() ?: $this->defaultMessage;
        }
        
        return $this->defaultMessage;
    }
    
}
