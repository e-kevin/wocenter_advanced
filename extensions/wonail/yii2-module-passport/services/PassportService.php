<?php

namespace wocenter\backend\modules\passport\services;

use wocenter\core\Service;

/**
 * 通行证服务类
 *
 * @property \wocenter\backend\modules\passport\services\passport\UcenterService $ucenter
 * @property \wocenter\backend\modules\passport\services\passport\VerifyService $verify
 * @property \wocenter\backend\modules\passport\services\passport\ValidationService $validation
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class PassportService extends Service
{
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'passport';
    }
    
    /**
     * 获取认证中心子服务类
     *
     * @return \wocenter\backend\modules\passport\services\passport\UcenterService|Service
     */
    public function getUcenter()
    {
        return $this->getSubService('ucenter');
    }
    
    /**
     * 获取验证中心子服务类
     *
     * @return \wocenter\backend\modules\passport\services\passport\VerifyService|Service
     */
    public function getVerify()
    {
        return $this->getSubService('verify');
    }
    
    /**
     * 规则验证服务类
     *
     * @return \wocenter\backend\modules\passport\services\passport\ValidationService|Service
     */
    public function getValidation()
    {
        return $this->getSubService('validation');
    }
    
}
