<?php

namespace wocenter\backend\modules\operate\models;

use wocenter\core\ActiveRecord;
use wocenter\helpers\DateTimeHelper;
use wocenter\helpers\StringHelper;
use wocenter\backend\modules\account\models\Follow;
use wocenter\backend\modules\account\models\BaseUser;
use wocenter\Wc;
use Yii;

/**
 * This is the model class for table "{{%viMJHk_invite}}".
 *
 * @property integer $id
 * @property integer $invite_type
 * @property string $code
 * @property integer $uid
 * @property integer $can_num
 * @property integer $already_num
 * @property integer $status 0：已用完，1：可注册，2：已退还，-1：管理员删除
 * @property integer $created_at
 * @property integer $expired_at
 *
 * @property array $codeStatus
 *
 * @property BaseUser $user
 * @property InviteType $inviteType
 */
class Invite extends ActiveRecord
{
    
    /**
     * @var integer 生成邀请码个数
     */
    public $count = 1;
    
    /**
     * @var integer 生成邀请码场景
     */
    const SCENARIO_GENERATE = 'generate';
    
    /**
     * @var integer 已用完
     */
    const CODE_FINISHED = 0;
    
    /**
     * @var integer 可注册
     */
    const CODE_SURPLUS = 1;
    
    /**
     * @var integer 已退还
     */
    const CODE_CANCELED = 2;
    
    /**
     * @var integer 管理员删除
     */
    const CODE_DELETED = -1;
    
    /**
     * @var integer 已过期
     */
    const CODE_EXPIRED = -2;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_invite}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['count', 'uid', 'invite_type', 'code', 'can_num', 'already_num', 'status', 'created_at', 'expired_at'], 'required'],
            ['count', 'integer', 'min' => 1, 'max' => 3000],
            [['invite_type', 'uid', 'can_num', 'already_num', 'status', 'created_at', 'expired_at'], 'integer'],
            [['code'], 'string', 'max' => 25],
            [['invite_type'], 'exist', 'skipOnError' => true, 'targetClass' => InviteType::className(), 'targetAttribute' => ['invite_type' => 'id']],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            parent::SCENARIO_DEFAULT => ['invite_type', 'can_num', 'count',
                'code', 'created_at', 'expired_at', 'status', 'uid',
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function transactions()
    {
        return [
            parent::SCENARIO_DEFAULT => parent::OP_INSERT,
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invite_type' => '邀请码类型',
            'code' => '邀请码',
            'uid' => '购买者',
            'can_num' => '可注册',
            'already_num' => '已注册',
            'status' => '状态',
            'created_at' => '购买时间',
            'expired_at' => '有效期至',
            'count' => '生成个数',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
        
        if ($this->expired_at < time() && $this->status != self::CODE_FINISHED) {
            $this->status = self::CODE_EXPIRED;
        }
    }
    
    /**
     * 获取邀请码状态列表
     *
     * @return array
     */
    public function getCodeStatus()
    {
        return [
            self::CODE_FINISHED => '已用完',
            self::CODE_SURPLUS => '可注册',
            self::CODE_CANCELED => '已退还',
            self::CODE_DELETED => '已删除',
            self::CODE_EXPIRED => '已过期',
        ];
    }
    
    /**
     * 判断邀请码是否可用
     *
     * @param string $code 邀请码
     * @param boolean $returnObject 是否返回对象结果，默认为否，返回数组
     *
     * @return boolean|array|Invite
     *  - false: 验证码无效，$this->message返回错误信息
     *  - array|Invite: 验证码可用，返回邀请码详情数组
     *  - id：邀请码ID
     *  - uid：创建者ID
     *  - code：邀请码
     *  - invite_type：邀请码所属邀请类型ID
     *  - expired_at：有效期
     *  - already_num：已注册用户数
     *  - can_num：可以注册用户数
     */
    public function checkInviteCode($code, $returnObject = false)
    {
        // todo 是否显示不同状态的验证码提示语?
        $query = self::find();
        // 必须查询的字段
        $needFields = 'id,uid,invite_type,code,already_num,can_num,expired_at';
        $query->select($needFields)
            ->where(['code' => $code, 'status' => 1]);
        $InviteInfo = $returnObject ? $query->one() : $query->asArray()->one();
        if ($InviteInfo) {
            if ($InviteInfo['expired_at'] <= time()) {
                $this->message = '无效邀请码：邀请码已过期';
                
                return false;
            } else {
                return $InviteInfo;
            }
        } else {
            $this->message = '无效邀请码：邀请码不存在';
            
            return false;
        }
    }
    
    /**
     * 初始化邀请用户信息
     *
     * @param Invite $codeInfo 邀请码详情
     *  - uid：邀请码所属用户id
     *  - invite_type：邀请码所属邀请类型
     *  - already_num：已经注册用户数
     *  - can_num：可以注册用户数
     *
     * @see Invite::checkInviteCode()
     *
     * @param integer $newUid 通过邀请方式新注册的用户ID
     */
    public function initInviteUser(Invite $codeInfo, $newUid = 0)
    {
        // 更新邀请码使用情况
        $this->updateInviteCodeInfo($codeInfo);
        // 邀请注册成功后增加邀请者的成功邀请名额
        (new InviteUserInfo())->increaseSuccessNum($codeInfo->invite_type, $codeInfo->uid);
        $inviteTypeInfo = $codeInfo->getInviteType()->select('each_follow, increase_score, increase_score_type')
            ->asArray()->one();
        // 根据邀请码配置是否互相`关注`用户
        if ($inviteTypeInfo['each_follow']) {
            (new Follow())->eachFollow($codeInfo->uid, $newUid, true);
        }
        // 更新邀请者的积分情况
        Wc::$service->getAccount()->updateUserScore(
            $codeInfo->uid,
            $inviteTypeInfo['increase_score'],
            $inviteTypeInfo['increase_score_type']
        );
    }
    
    /**
     * 更新邀请码使用情况
     *
     * @param Invite $codeInfo 邀请码详情
     *  - already_num：已经注册用户数
     *  - can_num：可以注册用户数
     */
    protected function updateInviteCodeInfo(Invite $codeInfo)
    {
        $codeInfo->already_num += 1;
        if ($codeInfo->already_num == $codeInfo->can_num) {
            $codeInfo->status = self::CODE_FINISHED; // 邀请数已用完
        }
        $codeInfo->updateAttributes(['already_num', 'status']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(BaseUser::className(), ['id' => 'uid']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInviteType()
    {
        return $this->hasOne(InviteType::className(), ['id' => 'invite_type']);
    }
    
    /**
     * 生成邀请码
     *
     * @return bool|int
     * @throws \yii\db\Exception
     */
    public function generate()
    {
        // 邀请码类型详情
        $inviteTypeInfo = InviteType::find()->select(['length', 'expired_at', 'expired_time_unit'])
            ->where(['id' => $this->invite_type])->asArray()->one();
        $this->created_at = time();
        $this->expired_at = DateTimeHelper::getAfterTime($inviteTypeInfo['expired_at'], $inviteTypeInfo['expired_time_unit']);
        $this->status = self::CODE_SURPLUS;
        $this->uid = Yii::$app->getUser()->getId();
        
        $add = [];
        do {
            $this->_generateCode($inviteTypeInfo['length']);
            $add[] = $this->getDirtyAttributes();
        } while (count($add) < $this->count);
        
        if (!$this->validate()) {
            return false;
        }
        
        return Yii::$app->getDb()->createCommand()->batchInsert(self::tableName(), array_keys($add[0]), $add)->execute();
    }
    
    /**
     * 生成邀请码
     *
     * @param integer $length 邀请码长度
     */
    protected function _generateCode($length)
    {
        do {
            $this->code = StringHelper::randString($length);
        } while (self::find()->where(['code' => $this->code])->exists());
    }
    
    /**
     * 清空无效邀请码，包括已删除，已过期
     *
     * @return bool
     */
    public function clearCode()
    {
        return $this->deleteAll([
            'or', ['status' => self::CODE_DELETED], ['<', 'expired_at', time()],
        ]) ? true : false;
    }
    
}
