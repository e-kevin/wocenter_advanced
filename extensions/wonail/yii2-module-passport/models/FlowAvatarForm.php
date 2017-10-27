<?php

namespace wocenter\backend\modules\passport\models;

use wocenter\core\Model;
use wocenter\backend\modules\account\models\UserIdentity;

/**
 * 注册流程 - 修改头像
 */
class FlowAvatarForm extends Model
{
    
    /**
     * @var integer 用户ID，数据来源在PassportForm()->login()里设置
     */
    public $uid;
    
    /**
     * @var integer 身份ID，数据来源在PassportForm()->login()里设置
     */
    public $identityId;
    
    /**
     * @var array 用户当前注册流程的进度数据，['step', 'nextStep']
     */
    public $userStep;
    
    /**
     * 保存用户-头像关联信息，包括更新已有，新建未有
     *
     * @param array $data
     * @param boolean $canSkip 步骤是否可以跳过
     *
     * @return boolean
     */
    public function save($data, $canSkip = false)
    {
        if ($canSkip) {
            return (new UserIdentity())->updateStep($this->uid, $this->identityId, $this->userStep);
        }
        (new UserIdentity())->updateStep($this->uid, $this->identityId, $this->userStep);
        
        return true;
    }
    
}
