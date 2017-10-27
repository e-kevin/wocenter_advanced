<?php

namespace wocenter\backend\modules\system\models;

use wocenter\core\ActiveRecord;
use wocenter\Wc;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%viMJHk_verify}}".
 *
 * @property integer $id
 * @property string $identity
 * @property string $type 0:邮箱 1:手机
 * @property string $code
 * @property integer $created_at
 */
class Verify extends ActiveRecord
{
    
    const EMAIL = 0;
    const MOBILE = 1;
    
    public static $typeList = [
        'email' => self::EMAIL,
        'mobile' => self::MOBILE,
    ];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_verify}}';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false,
            ],
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['identity', 'type', 'code'], 'required'],
            [['created_at', 'type'], 'integer'],
            [['identity'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 50],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'identity' => '用户标识',
            'type' => '验证类型',
            'code' => '验证码',
            'created_at' => '创建时间',
        ];
    }
    
    /**
     * 发送验证码
     *
     * @param string $identity 验证类型 [mobile, email]
     * @param boolean $isRegisterPage 是否在注册页面请求发送，默认为false
     *
     * @return boolean true - 操作成功。false - 操作失败，可通过$this->message获取错误信息
     */
    public function sendVerify($identity, $isRegisterPage = false)
    {
        $verifyService = Wc::$service->getPassport()->getVerify();
        if ($verifyService->send($identity, $isRegisterPage)) {
            return true;
        } else {
            $this->message = $verifyService->getInfo();
            
            return false;
        }
    }
    
}
