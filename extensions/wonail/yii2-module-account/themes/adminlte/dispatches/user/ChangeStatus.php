<?php

namespace wocenter\backend\modules\account\themes\adminlte\dispatches\user;

use wocenter\backend\themes\adminlte\components\Dispatch;
use wocenter\backend\modules\account\models\BaseUser;
use wocenter\backend\modules\passport\models\SecurityForm;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Class ChangeStatus
 */
class ChangeStatus extends Dispatch
{
    
    public $method;
    
    /**
     * @throws InvalidConfigException
     */
    public function run()
    {
        if ($this->method === null) {
            throw new InvalidConfigException("The `method` property must be set.");
        }
        $request = Yii::$app->getRequest();
        $selections = $request->getBodyParam('selection');
        
        if (empty($selections)) {
            $this->error(Yii::t('wocenter/app', 'Select the data to be operated.'));
        }
        if (is_array($selections)) {
            if (in_array(Yii::$app->getUser()->getId(), $selections)) {
                $this->error(Yii::t('wocenter/app', "Can't do yourself."));
            }
        } else {
            if (Yii::$app->getUser()->getId() == $selections) {
                $this->error(Yii::t('wocenter/app', "Can't do yourself."));
            }
        }
        if (in_array($this->controller->action->id, ['forbidden', 'active']) && strpos($request->getReferrer(), 'locked-list') !== false) {
            $this->error(Yii::t('wocenter/app', 'The user in the locked state cannot perform this operation.'));
        }
        if (in_array($this->controller->action->id, ['delete', 'unlock']) && count($selections) > 1) {
            $this->error(Yii::t('wocenter/app', 'Only one user can be operated at a time.'));
        }
        
        switch ($this->method) {
            case 'forbidden':
                if (BaseUser::updateAll(['status' => BaseUser::STATUS_FORBIDDEN], ['id' => $selections])) {
                    $this->success('', parent::RELOAD_LIST);
                } else {
                    $this->error();
                }
                break;
            case 'active':
                if (BaseUser::updateAll(['status' => BaseUser::STATUS_ACTIVE], ['id' => $selections])) {
                    $this->success('', parent::RELOAD_LIST);
                } else {
                    $this->error();
                }
                break;
            case 'delete':
                if (BaseUser::deleteAll(['id' => $selections])) {
                    $this->success(Yii::t('wocenter/app', 'Delete successful.'), parent::RELOAD_LIST);
                } else {
                    $this->error(Yii::t('wocenter/app', 'Delete failure.'));
                }
                break;
//            case 'unlock':
//                if (is_array($selections)) {
//                    $this->error('一次只能解锁一个用户');
//                }
//                $hasLock = LoginLock::findOne(['uid' => $selections]);
//                if ($hasLock !== null) {
//                    $user = BaseUser::findOne($selections);
//                    if ($user !== null) {
//                        $user->status = BaseUser::STATUS_ACTIVE;
//                        if ($user->save(false)) {
//                            $hasLock->expire_at = 0;
//                            $hasLock->save(false);
//                            $this->responseSuccess('解锁成功', [$redirect]);
//                        } else {
//                            $this->error('解锁失败，Ucenter用户中心未知错误', '', 4);
//                        }
//                    } else {
//                        $this->error('解锁失败，用户不存在', '', 4);
//                    }
//                } else {
//                    $this->error('解锁失败：系统锁定表不存在所选数据', '', 4);
//                }
//                break;
            case 'unlock':
                $model = new SecurityForm();
                if ($model->unlockUser($selections)) {
                    $this->success('解锁成功', parent::RELOAD_LIST);
                } else {
                    $this->error($model->message);
                }
                break;
            default:
                $this->error($this->method . '非法参数');
        }
    }
    
}
