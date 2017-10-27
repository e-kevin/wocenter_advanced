<?php

namespace wocenter\backend\modules\account\models;

use wocenter\backend\modules\data\models\TagUser;
use wocenter\backend\modules\data\models\UserScoreType;
use wocenter\backend\modules\operate\models\RankUser;
use wocenter\backend\modules\log\models\UserScoreLog;
use wocenter\backend\modules\passport\models\LoginForm;
use wocenter\core\ActiveRecord;
use wocenter\Wc;
use wocenter\helpers\DateTimeHelper;
use wocenter\libs\Utils;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%viMJHk_user_identity}}".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $identity_id
 * @property integer $status 2-未审核，1-启用，0-禁用，-1-删除
 * @property integer $step
 * @property integer $is_init 0-未初始化 1-已初始化 是否初始化身份相关信息 身份积分设置、角色组和头衔分配等
 *
 * todo 以下属性暂未使用
 * @property integer $avatar_is_init 头像是否初始化
 * @property integer $rank_is_init 头衔是否初始化
 * @property integer $profile_is_init 扩展档案是否初始化
 *
 * @property User $user
 * @property Identity $identity
 */
class UserIdentity extends ActiveRecord
{
    
    /**
     * @var integer 新用户注册后所拥有的默认身份
     */
    const DEFAULT_IDENTITY = 1;
    
    /**
     * @var integer 未审核
     */
    const STATUS_NOT_AUDITED = 2;
    
    /**
     * @var integer 启用
     */
    const STATUS_ACTIVE = 1;
    
    /**
     * @var integer 禁用
     */
    const STATUS_FORBIDDEN = 0;
    
    /**
     * @var integer 删除
     */
    const STATUS_DELETED = -1;
    
    /**
     * @var integer 未开始步骤
     */
    const STEP_START = 0;
    
    /**
     * @var integer 步骤结束
     */
    const STEP_FINISHED = 9;
    
    /**
     * @var integer 修改头像步骤
     */
    const STEP_AVATAR = 1;
    
    /**
     * @var integer 填写个人档案
     */
    const STEP_PROFILE = 2;
    
    /**
     * @var integer 选择个人标签
     */
    const STEP_TAG = 3;
    
    /**
     * @var array 流程列表
     */
    protected $stepList = [
        self::STEP_AVATAR,
        self::STEP_PROFILE,
        self::STEP_TAG,
    ];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_user_identity}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'identity_id', 'status', 'step'], 'required'],
            [['uid', 'identity_id', 'status', 'step', 'is_init', 'avatar_is_init', 'rank_is_init', 'profile_is_init'], 'integer'],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'id']],
            [['identity_id'], 'exist', 'skipOnError' => true, 'targetClass' => Identity::className(), 'targetAttribute' => ['identity_id' => 'id']],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'UID',
            'identity_id' => '身份ID',
            'status' => '状态',
            'step' => '当前执行步骤',
            'is_init' => '是否初始化',
            'avatar_is_init' => '头像是否初始化',
            'rank_is_init' => '头衔是否初始化',
            'profile_is_init' => '扩展档案是否完善',
        ];
    }
    
    /**
     * 绑定用户-身份关联信息
     *
     * @param integer $uid 需要关联的用户ID
     * @param integer $identityId 需要关联的身份ID
     *
     * @return boolean
     */
    public function bindUserIdentity($uid, $identityId)
    {
        if ($uid == 1) {
            $this->message = '系统默认管理员无需绑定身份';
            
            return false;
        }
        // 已经绑定则不执行后续操作
        $info = self::findOne(['uid' => $uid, 'identity_id' => $identityId]);
        if ($info) {
            if ($info->is_init == 0) {
                // 正常情况下是不存在该情况的
                $this->message = '用户已经绑定所选身份，请执行初始化操作，初始化成功后即可激活该身份';
                
                return false;
            } else {
                $this->message = '用户已经绑定所选身份，请勿重复操作';
                
                return false;
            }
        }
        $identityInfo = (new Identity())->find()->select('id, is_audit, status, title')
            ->where(['id' => $identityId])
            ->asArray()->one();
        if ($identityInfo === null) {
            $this->message = '所需关联的身份信息不存在';
            
            return false;
        } elseif ($identityInfo['status'] == 0) {
            $this->message = '所需关联的身份已禁用';
            
            return false;
        }
        // 添加用户和身份关联
        $this->uid = $uid;
        $this->identity_id = $identityId;
        $this->step = UserIdentity::STEP_START;
        $this->status = $identityInfo['is_audit'] ? self::STATUS_NOT_AUDITED : self::STATUS_ACTIVE; // 身份需要审核则标识为`未审核`，只有审核通过后才可正常使用该身份
        $this->is_init = $identityInfo['is_audit'] ? 0 : 1; // 标记用户和身份关联信息是否初始化
        $res = $this->save(true, ['uid', 'identity_id', 'step', 'status', 'is_init']);
        // 绑定用户身份失败
        if ($res == false) {
            return false;
        }
        // 身份不需要审核则直接初始化用户的身份配置信息
        if (!$identityInfo['is_audit']) {
            // 初始化用户-身份关联配置信息
            $this->_initUserIdentityConfig($identityInfo);
        }
        // 初始化默认显示哪一个身份的个人主页
        $this->_initDefaultIdentity($identityId);
        
        return true;
    }
    
    /**
     * 初始化用户-身份关联配置信息，包括[积分，头衔，角色]
     *
     * @param Identity[] $identityInfo 身份信息
     */
    private function _initUserIdentityConfig($identityInfo)
    {
        // 获取身份的积分和头衔配置信息
        $config = (new IdentityConfig())->getConfig($identityInfo['id'], ['score', 'rank', 'tag'], false, true);
        // 分配默认角色
//        $this->_initRoleConfig($uid, $identityInfo['roles]);
        // 设置默认积分
        $this->_initScoreConfig(ArrayHelper::remove($config, 'score', []), $identityInfo['title']);
        // 设置默认头衔
        $this->_initRankConfig(ArrayHelper::remove($config, 'rank', []));
        // 设置默认标签
        $this->_initTagConfig(ArrayHelper::remove($config, 'tag', []));
    }
    
    /**
     * 分配默认角色
     *
     * @param array $roles 角色组
     *
     * @return boolean
     */
    private function _initRoleConfig($roles)
    {
        if (!empty($roles)) {
            // 获取用户已拥有的角色组
            $userRoles = [];
            // 剔除已拥有的角色组
            $diff = array_diff(explode(',', $roles), $userRoles);
            // 待添加角色组数据
            $add = [];
            /** todo 暂未使用角色 */
            foreach ($diff as $role) {
                $add[] = [
                    'uid' => $this->uid,
                    'group_id' => $role,
                ];
            }
            if (!empty($add)) {
                Yii::$app->getDb()->createCommand()->batchInsert('{}', array_keys($add[0]), $add);
            }
        }
    }
    
    /**
     * 设置默认积分
     *
     * @param array $config 积分配置信息
     * @param string $title 身份名称，用于添加积分变动日志
     */
    private function _initScoreConfig($config, $title)
    {
        if (!empty($config['value'])) {
            // 需要更新积分的数据
            $update = [];
            // 积分变动日志数据
            $add = [];
            // 获取用户积分
            $userScore = (new UserProfile())->getUserScore($this->uid);
            // 获取积分类型列表
            $scoreInfos = ArrayHelper::index((new UserScoreType())->getList(), function ($element) {
                return 'score_' . $element['id'];
            });
            // 备注信息
            $actionUser = Wc::$service->getAccount()->queryUser('nickname', $this->uid);
            $createdAt = DateTimeHelper::timeFormat(time());
            $remark = "{$actionUser} 在 {$createdAt} 绑定身份 {$title}";
            $ip = ip2long(Utils::getClientIp());
            $url = Yii::$app->getRequest()->getUrl();
            foreach ($config['value'] as $scoreTypeField => $parseScore) {
                if ($parseScore['score'] > 0) {
                    $update[$scoreTypeField] = "`{$scoreTypeField}` = {$scoreTypeField}{$parseScore['scoreFormat']}";
                    $add[] = [
                        'uid' => $this->uid,
                        'type' => $scoreInfos[$scoreTypeField]['id'],
                        'action' => $parseScore['operator'],
                        'value' => $parseScore['score'],
                        'finally_value' => isset($userScore[$scoreTypeField]) ? $userScore[$scoreTypeField] + $parseScore['score'] : 0,
                        'remark' => $remark . ' 【' . $scoreInfos[$scoreTypeField]['name']
                            . $parseScore['scoreFormat'] . $scoreInfos[$scoreTypeField]['unit'] . '】',
                        'record_id' => $this->uid,
                        'created_at' => time(),
                        'model' => self::tableName(),
                        'ip' => $ip,
                        'request_url' => $url,
                    ];
                }
            }
            if (!empty($update)) {
                /* @var $class User */
                $class = Yii::$app->getUser()->identityClass;
                $res = Yii::$app->getDb()->createCommand('UPDATE ' . UserProfile::tableName() . ' SET '
                    . implode(',', $update) . ' WHERE `uid`=:uid AND `status`=:status', [
                    ':uid' => $this->uid,
                    ':status' => $class::STATUS_ACTIVE,
                ])->execute();
                if ($res) {
                    // 添加积分变动日志
                    Yii::$app->getDb()->createCommand()->batchInsert(UserScoreLog::tableName(), array_keys($add[0]), $add)->execute();
                }
            }
        }
    }
    
    /**
     * 设置默认头衔
     *
     * @param array $config 头衔配置信息
     */
    private function _initRankConfig($config)
    {
        if (!empty($config['value'])) {
            // 获取用户已拥有的头衔
            $rankIds = RankUser::find()->select('rank_id')->where(['uid' => $this->uid])->column();
            // 剔除已拥有的头衔
            $diff = array_diff($config['value']['data'], $rankIds);
            if (empty($diff)) {
                return;
            }
            // 待添加头衔数据
            $add = [];
            foreach ($diff as $rank) {
                $add[] = [
                    'uid' => $this->uid,
                    'rank_id' => $rank,
                    'reason' => $config['value']['reason'],
                    'is_show' => 1,
                    'status' => 1,
                    'created_at' => time(),
                ];
            }
            Yii::$app->getDb()->createCommand()->batchInsert(RankUser::tableName(), array_keys($add[0]), $add)->execute();
        }
    }
    
    /**
     * 设置默认标签
     *
     * @param array $config 标签配置信息
     */
    private function _initTagConfig($config)
    {
        if (!empty($config['value'])) {
            // 获取用户已拥有的标签
            $tagIds = TagUser::find()->select('tag_id')->where(['uid' => $this->uid])->column();
            // 剔除已拥有的标签
            $diff = array_diff($config['value'], $tagIds);
            if (empty($diff)) {
                return;
            }
            // 待添加头衔数据
            $add = [];
            foreach ($diff as $tag) {
                $add[] = [
                    'uid' => $this->uid,
                    'tag_id' => $tag,
                ];
            }
            Yii::$app->getDb()->createCommand()->batchInsert(TagUser::tableName(), array_keys($add[0]), $add)->execute();
        }
    }
    
    /**
     * 初始化默认显示哪一个身份的个人主页
     *
     * @param integer $identityId 身份ID
     */
    private function _initDefaultIdentity($identityId)
    {
        $identities = self::find()->where(['uid' => $this->uid, 'status' => self::STATUS_ACTIVE])
            ->andWhere(['not in', 'identity_id', $identityId])->count();
        // 如果不存在激活身份，则设置当前身份为默认身份
        if (empty($identities)) {
            UserProfile::updateAll(['default_identity' => $identityId], ['uid' => $this->uid]);
        }
    }
    
    /**
     * 获取指定用户指定身份当前的执行步骤
     *
     * @param integer $uid 用户ID
     * @param integer $identityId 身份ID
     *
     * @return string 当前步骤
     * @return boolean false 数据不存在
     */
    public function getCurrentStep($uid = 0, $identityId = 0)
    {
        // 获取已经初始化且激活的用户-身份关联信息，审核没有通过的则不执行步骤流程
        return self::find()->select('step')->where([
            'uid' => $uid,
            'identity_id' => $identityId,
            'is_init' => 1,
            'status' => self::STATUS_ACTIVE,
        ])->scalar();
    }
    
    /**
     * 获取指定用户指定身份的步骤信息（当前所执行的步骤和下一个步骤）
     *
     * @param integer $uid 用户ID
     * @param integer $identityId 身份ID
     *
     * @return array [step => 当前所执行的步骤, nextStep => 下一个步骤]或[]
     */
    public function getUserStep($uid = 0, $identityId = 0)
    {
        $step = $this->getCurrentStep($uid, $identityId);
        if ($step === false) {
            return [];
        }
        
        return ['step' => $step, 'nextStep' => $this->getNextStep($step)];
    }
    
    /**
     * 获取注册流程下一步
     *
     * @param integer $nowStep 当前步骤
     *
     * @return string 下一个步骤
     */
    public function getNextStep($nowStep = self::STEP_START)
    {
        // 步骤如果已经完成则返回结束步骤
        if ($nowStep == self::STEP_FINISHED) {
            return self::STEP_FINISHED;
        }
        $step = Wc::$service->getSystem()->getConfig()->kanban('REGISTER_STEP');
        // 系统没有开启注册步骤流程则结束流程，否则获取步骤流程数据
        if (empty($step)) {
            return self::STEP_FINISHED;
        } else {
            $step = ArrayHelper::getColumn($step, 'id');
        }
        // 当前步骤为空时，如果存在步骤信息则执行步骤信息里的第一个步骤，否则结束执行步骤
        if (empty($nowStep)) {
            $next = $step[0];
        } else {
            $nowKey = array_search($nowStep, $step);
            $next = $nowKey !== false && isset($step[$nowKey + 1]) ? $step[$nowKey + 1] : self::STEP_FINISHED;
        }
        // 如果下一步骤不在步骤流程里则结束流程
        if (!in_array($next, $this->stepList)) {
            $next = self::STEP_FINISHED;
        }
        
        return $next;
    }
    
    /**
     * 判断注册步骤是否可跳过
     *
     * @param string $step 需要判断的步骤
     *
     * @return boolean false - 不可跳过 true - 可跳过
     */
    public function checkStepCanSkip($step)
    {
        return in_array($step, explode(',', Wc::$service->getSystem()->getConfig()->get('REGISTER_STEP_CAN_SKIP')));
    }
    
    /**
     * 更新注册流程进度
     *
     * @param integer $uid 用户ID
     * @param integer $identityId 身份ID
     * @param array $userStep 用户步骤信息，由`$this->getUserStep()`生成
     *
     * @return boolean
     */
    public function updateStep($uid, $identityId, $userStep)
    {
        $step = '';
        switch ($userStep['step']) {
            case self::STEP_START:
                $step = $this->getNextStep($userStep['nextStep']);
                break;
            case self::STEP_FINISHED:
                break;
            default:
                // 如果当前步骤已经是最后一个步骤，则结束流程
                if ($userStep['nextStep'] == self::STEP_FINISHED) {
                    $step = self::STEP_FINISHED;
                } else {
                    $step = $userStep['nextStep'];
                }
                break;
        }
        
        return self::updateAll(['step' => $step], ['uid' => $uid, 'identity_id' => $identityId]);
    }
    
    /**
     * 检测用户是否需要执行步骤流程
     *
     * @param integer $uid 用户ID
     * @param string $identity 用户标识[username, email, mobile]
     * @param integer $rememberMe 步骤完成后直接登录时是否使用`记住我`功能
     *
     * @return boolean
     *  - true: 需要执行步骤流程
     *  - false: 不需要
     */
    public function needStep($uid, $identity, $rememberMe = 0)
    {
        // 获取用户登陆身份，主要用于根据不同身份验证是否已完成步骤流程
        $identityId = (new UserProfile())->getLoginIdentity($uid);
        if (empty($identityId)) {
            return false;
        }
        // 步骤未完成则执行相应身份的步骤流程
        $userStep = $this->getUserStep($uid, $identityId);
        if (!empty($userStep) && $userStep['step'] != self::STEP_FINISHED) {
            // 设置SESSION数据
            Yii::$app->getSession()->set(LoginForm::TMP_LOGIN, [
                'identity' => $identity,
                'rememberMe' => $rememberMe,
                'uid' => $uid,
                'identityId' => $identityId,
            ]);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdentity()
    {
        return $this->hasOne(Identity::className(), ['id' => 'identity_id']);
    }
    
}
