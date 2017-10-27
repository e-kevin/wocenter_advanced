<?php

namespace wocenter\backend\modules\operate\models;

use wocenter\backend\modules\data\models\UserScoreType;
use wocenter\core\ActiveRecord;
use wocenter\helpers\ArrayHelper;
use wocenter\backend\modules\account\models\Identity;
use wocenter\helpers\DateTimeHelper;
use wocenter\Wc;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%viMJHk_invite_type}}".
 *
 * @property integer $id
 * @property string $title
 * @property integer $length
 * @property integer $expired_at
 * @property integer $expired_time_unit
 * @property integer $cycle_num
 * @property integer $cycle_time
 * @property integer $cycle_time_unit
 * @property string $identities
 * @property string $auth_groups
 * @property integer $pay_score
 * @property integer $pay_score_type
 * @property integer $increase_score
 * @property integer $increase_score_type
 * @property integer $each_follow
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Invite[] $invites
 * @property InviteBuyLog[] $inviteBuyLogs
 * @property InviteUserInfo[] $inviteUserInfos
 */
class InviteType extends ActiveRecord
{
    
    const CACHE_ALL_INVITE_TYPE = 'all_invite_type';
    
    /**
     * 默认身份　当注册身份为空时，系统自动为用户绑定该身份
     */
    const DEFAULT_IDENTITY = 1;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_invite_type}}';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => TimestampBehavior::className(),
            ],
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'title', 'expired_at', 'expired_time_unit',
                    'cycle_num', 'cycle_time', 'cycle_time_unit',
                    'pay_score', 'pay_score_type',
                    'increase_score', 'increase_score_type',
                ],
                'required',
            ],
            [
                [
                    'expired_at', 'expired_time_unit', 'cycle_num', 'cycle_time', 'cycle_time_unit',
                    'pay_score', 'pay_score_type', 'increase_score', 'increase_score_type',
                    'each_follow', 'status', 'created_at', 'updated_at',
                ],
                'integer',
            ],
            ['length', 'integer', 'max' => 25], // 限制邀请码最大长度，该值取决于[[Invite::$code]]值
            ['title', 'unique'],
            [['title'], 'string', 'max' => 25],
            [['auth_groups'], 'string', 'max' => 50],
            ['identities', 'safe'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'length' => '邀请码长度',
            'expired_at' => '有效时长',
            'expired_time_unit' => '有效时长单位',
            'cycle_num' => '周期内可购买个数',
            'cycle_time' => '周期时长',
            'cycle_time_unit' => '周期时长单位',
            'identities' => '绑定身份',
            'auth_groups' => '允许购买的用户组',
            'pay_score' => '每个额度消费',
            'pay_score_type' => '每个额度消费类型',
            'increase_score' => '每个邀请成功后获得',
            'increase_score_type' => '每个邀请成功后获得类型',
            'each_follow' => '邀请成功后互相关注',
            'status' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'fullExpiredAt' => '有效时长',
            'fullCycleTime' => '周期时长',
            'fullPayScore' => '每个额度消费',
            'fullIncreaseScore' => '每个成功后获得',
            'identityValue' => '绑定身份',
            'fullCycle' => '周期',
        ];
    }
    
    public function attributeHints()
    {
        return [
            'identities' => '为空则绑定系统默认身份：普通用户',
            'each_follow' => '邀请人为系统，则只会单向关注，即用户关注系统',
        ];
    }
    
    /**
     * 获取完整有效时长
     *
     * @return string
     */
    public function getFullExpiredAt()
    {
        return $this->expired_at . ' ' . DateTimeHelper::getTimeUnitValue($this->expired_time_unit);
    }
    
    /**
     * 获取完整周期时长
     *
     * @return string
     */
    public function getFullCycleTime()
    {
        return $this->cycle_time . ' ' . DateTimeHelper::getTimeUnitValue($this->cycle_time_unit);
    }
    
    /**
     * 获得完整周期信息
     *
     * @return string
     */
    public function getFullCycle()
    {
        return $this->getFullCycleTime() . ' 内可购买 ' . $this->cycle_num . ' 个';
    }
    
    /**
     * 获取完整的额度消费
     *
     * @return string
     */
    public function getFullPayScore()
    {
        return $this->pay_score . ' ' . (new UserScoreType())->selectList[$this->pay_score_type];
    }
    
    /**
     * 获取完整的成功后获得
     *
     * @return string
     */
    public function getFullIncreaseScore()
    {
        return $this->increase_score . ' ' . (new UserScoreType())->selectList[$this->increase_score_type];
    }
    
    /**
     * 获取绑定身份
     *
     * @return string
     */
    public function getIdentityValue()
    {
        $identityList = (new Identity())->getSelectList();
        $tmp = [];
        $identities = (array)$this->identities;
        if (empty($identities)) {
            return '';
        }
        foreach ($identities as $val) {
            $tmp[] = $identityList[$val];
        }
        
        return implode(',', $tmp);
    }
    
    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
        
        $this->identities = $this->parseIdentityIds($this->identities);
    }
    
    /**
     * @inheritdoc
     */
    public function afterValidate()
    {
        parent::afterValidate();
        
        if ($this->identities) {
            $tmp = [];
            foreach ((array)$this->identities as &$v) {
                $tmp[] = '[' . $v . ']';
            }
            $this->identities = implode(',', $tmp);
        }
    }
    
    /**
     * 解析绑定身份IDS
     *
     * @param string $ids 需要解析的身份IDS，格式如：[1],[2],[3]
     *
     * @return array
     */
    protected function parseIdentityIds($ids = '')
    {
        if (empty($ids)) {
            return [];
        }
        $ids = str_replace('[', '', $ids);
        $ids = str_replace(']', '', $ids);
        $ids = explode(',', $ids);
        
        return $ids;
    }
    
    /**
     * 获取所有身份类型筛选列表
     *
     * @return array 身份类型筛选列表
     */
    public function getSelectList()
    {
        return ArrayHelper::map($this->getAll(), 'id', 'title');
    }
    
    /**
     * 获取所有邀请类型数据
     *
     * @param boolean|integer $duration 缓存周期，默认缓存`60`秒
     *
     * @return mixed
     */
    public function getAll($duration = 60)
    {
        return Wc::getOrSet(self::CACHE_ALL_INVITE_TYPE, function () {
            return self::find()->asArray()->all() ?: [];
        }, $duration);
    }
    
    /**
     * 获取指定邀请码类型所绑定的身份IDS
     *
     * @param integer $inviteTypeId 邀请类型id
     *
     * @return array
     */
    public function getIdentityIds($inviteTypeId = 0)
    {
        $ids = self::find()->select('identities')->where(['id' => $inviteTypeId])->scalar();
        
        return $this->parseIdentityIds($ids);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvites()
    {
        return $this->hasMany(Invite::className(), ['invite_type' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInviteBuyLogs()
    {
        return $this->hasMany(InviteBuyLog::className(), ['invite_type' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInviteUserInfos()
    {
        return $this->hasMany(InviteUserInfo::className(), ['invite_type' => 'id']);
    }
    
    /**
     * @inheritdoc
     */
    public function clearCache()
    {
        Yii::$app->getCache()->delete(self::CACHE_ALL_INVITE_TYPE);
    }
    
}
