<?php

namespace wocenter\backend\modules\account\services;

use wocenter\backend\modules\account\models\User;
use wocenter\backend\modules\action\models\Action;
use wocenter\backend\modules\log\models\ActionLog;
use wocenter\backend\modules\data\models\UserScoreType;
use wocenter\backend\modules\log\models\UserScoreLog;
use wocenter\backend\modules\account\models\Follow;
use wocenter\backend\modules\account\models\UserProfile;
use wocenter\core\Service;
use wocenter\helpers\DateTimeHelper;
use wocenter\libs\PinYin;
use wocenter\Wc;
use Yii;

/**
 * 账户服务类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class AccountService extends Service
{
    
    /**
     * @var string|array|callable|User 用户模型类
     */
    public $userModel;
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'account';
    }
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        if ($this->userModel == null) {
            $this->userModel = Yii::$app->getUser()->identityClass;
        }
        
        $this->userModel = Yii::createObject($this->userModel);
    }
    
    /**
     * 获取用户信息
     *
     * @param string $identity 用户标识 [手机，邮箱，用户名]
     *
     * @return null|array
     *  - null: 用户不存在
     *  - array: 用户信息 [id,username,email,mobile]
     */
    public function info($identity)
    {
        $param = Wc::$service->getPassport()->getUcenter()->parseIdentity($identity);
        $user = $this->userModel->find()->select('id,username,email,mobile,status')->where([$param => $identity])->asArray()->one();
        if ($user == null) {
            return null;
        }
        
        return [
            $user['id'],
            $user['username'],
            $user['email'],
            $user['mobile'],
        ];
    }
    
    /**
     * 获取多个用户信息
     *
     * @param string $identity 用户标识
     * @param boolean $validateStatus 用户状态值验证，默认开启
     *
     * @return array 用户信息 [key][id,username,email,mobile]
     */
//    public function infos($identity = '', $validateStatus = true)
//    {
//        if (empty($identity)) {
//            return -2001;
//        }
//
//        $param = (new UcenterApi())->parseIdentity($identity);
//        $map[$param] = $identity;
//        if ($validateStatus) {
//            $map['status'] = 1;
//        }
//
//        $users = $this->model->find()->select('id,username,email,mobile,status')->where($map)->indexBy('id')->asArray()->all();
//
//        return $users ?: -2002;    // 用户不存在
//    }
    
    /**
     * 计算用户资料完整度
     *
     * @param array $user 用户数组信息 [realname, nickname, username, email, mobile]
     *
     * @return integer 完整度
     */
    public function getProfilePercents($user = [])
    {
        $countProfile = 0;
        $profileFields = ['realname', 'nickname', 'username', 'email', 'mobile'];
        foreach ($profileFields as $value) {
            if (!empty($user[$value])) {
                $countProfile++;
            }
        }
        
        return $countProfile * 10;
    }
    
    /**
     * 操作用户积分、威望、贡献等数据
     *
     * @param integer $uid 触发操作的用户id
     * @param integer $score 积分数值，不包含运算符，则默认为+
     * @param integer $scoreType 积分类型
     * @param array|integer|null $actionLog 行为日志数据
     * - integer，获取该数值的日志数据
     * - array，直接调用，必须提供[model, record_id,action_id,created_at]
     * - null，积分变动备注只记录当前值的变动
     *
     * @return boolean true - 操作成功，false - 操作失败
     */
    public function updateUserScore($uid, $score = 0, $scoreType = UserScoreType::TYPE_JIFEN, $actionLog = null)
    {
        // 触发操作的用户为系统或需要操作的积分数值为0则不执行奖罚操作
        if ($uid == 1 || empty($score)) {
            return false;
        }
        // 只能操作积分、威望、贡献三种类型的数据
        if (!in_array($scoreType, [UserScoreType::TYPE_JIFEN, UserScoreType::TYPE_WEIWANG, UserScoreType::TYPE_GONGXIANG])) {
            return false;
        }
        
        $scoreTypeField = 'score_' . $scoreType;
        $parseScore = UserScoreType::parseScore($score);
        // 执行积分操作
        /** @var User $class */
        $class = Yii::$app->getUser()->identityClass;
        $res = Yii::$app->getDb()->createCommand('UPDATE ' . UserProfile::tableName() . ' SET `' . $scoreTypeField . '`=' . $scoreTypeField . $parseScore['scoreFormat'] . ' WHERE `uid`=:uid AND `status`=:status', [
            ':uid' => $uid,
            ':status' => $class::STATUS_ACTIVE,
        ])->execute();
        
        // 积分操作成功后记录积分变动日志
        if ($res) {
            if (is_int($actionLog)) {
                $actionLog = ActionLog::find()->select('record_id,model,action_id,created_at')->where(['id' => $actionLog])->asArray()->one();
            } elseif (is_null($actionLog)) {
                $actionLog['model'] = '';
                $actionLog['record_id'] = 0;
                $actionLog['created_at'] = 0;
            }
            // 验证$actionLog必须传入的数据
            $needData = ['model', 'record_id', 'created_at'];
            $pass = true;
            foreach ($needData as $val) {
                if (!array_key_exists($val, $actionLog)) {
                    $pass = false;
                    break;
                }
            }
            // 根据日志记录数据添加积分变动备注信息
            $remark = '';
            if ($pass && isset($actionLog['action_id'])) {
                $actionUser = $this->queryUser('nickname', $uid);
                $created_at = DateTimeHelper::timeFormat($actionLog['created_at']);
                $actionName = Action::find()->select('title')->where(['id' => $actionLog['action_id']])->scalar();
                $remark = "{$actionUser} 在 {$created_at} {$actionName}";
            }
            // 添加积分变动日志
            $scoreInfo = UserScoreType::find()->select('name, unit')->where(['id' => $scoreType])->asArray()->one();
            $UserScoreLog = new UserScoreLog();
            $UserScoreLog->uid = $uid;
            $UserScoreLog->type = $scoreType;
            $UserScoreLog->action = $parseScore['operator'];
            $UserScoreLog->value = $parseScore['score'];
            $UserScoreLog->finally_value = UserProfile::find()->select($scoreTypeField)->where(['uid' => $uid])->scalar();
            $UserScoreLog->remark = $remark . '【' . $scoreInfo['name'] . $parseScore['scoreFormat'] . $scoreInfo['unit'] . '】';
            $UserScoreLog->model = $actionLog['model'];
            $UserScoreLog->record_id = $actionLog['record_id'];
            $UserScoreLog->save(false);
        }
        
        return $res;
    }
    
    /**
     * 检测用户UID是否可用
     *
     * @param integer $uid 待检测用户UID
     *
     * @return boolean
     */
    public function checkAvailableUid($uid = 0)
    {
        if (empty($uid) || $uid < 0) {
            return false;
        }
        
        return true;
    }
    
    /**
     * 查询用户缓存数据
     * 支持的字段有
     * {{%user}}表中的所有字段，{{%user_profile}}表中的所有字段
     * 等级：level
     * 头像：avatar30 avatar50 avatar100 avatar200
     * 个人中心地址：space_url
     * 认证图标：icons_html
     *
     * @param array|string $fields 如果是数组，则返回数组。如果不是数组，则返回对应的值
     * @param integer $uid 用户UID，默认为当前登陆的用户ID
     *
     * @return array|null
     */
    public function queryUser($fields, $uid)
    {
        // 默认获取自己的资料
        $uid = $uid ?: Yii::$app->getUser()->identity->getId();
        if (!$uid) {
            return null;
        }
        
        // 如果fields不是数组，则返回值也不是数组
        if (!is_array($fields)) {
            $result = $this->queryUser([$fields], $uid);
            
            return $result ? $result[$fields] : null;
        }
        
        // 无需缓存的字段
        $noCache = ['icons_html', 'level', 'is_followed', 'is_following'];
        
        // 查询缓存，过滤掉已缓存的字段
        $cachedFields = [];
        $cacheResult = [];
        foreach ($fields as $field) {
            if (in_array($field, $noCache)) {
                continue;
            }
            $cache = $this->getUserDataWithCache($uid, $field);
            //获取已缓存字段
            if (!empty($cache)) {
                $cacheResult[$field] = $cache;
                $cachedFields[] = $field;
            }
        }
        
        // 去除已经缓存的字段
        $fields = array_diff($fields, $cachedFields);
        
        // 获取两张用户表格中的所有字段
        $User = new User();
        $UserProfile = new UserProfile();
        $userFields = array_keys($User->getAttributes());
        $userProfileFields = array_keys($UserProfile->getAttributes());
        
        // 分析每个表分别要读取哪些字段
        $avatarFields = ['avatar30', 'avatar50', 'avatar100', 'avatar200'];
        $avatarFields = array_intersect($avatarFields, $fields);
        $userFields = array_intersect($userFields, $fields);
        $userProfileFields = array_intersect($userProfileFields, $fields);
        
        // 获取每个表需要查询的字段结果
        $result = [];
        $userResult = [];
        $userProfileResult = [];
        if ($userFields) {
            $userResult = $User->find()->where(['id' => $uid])->select($userFields)->asArray()->one();
        }
        if ($userProfileFields) {
            $userProfileResult = $UserProfile->find()->where(['uid' => $uid])->select($userProfileFields)->asArray()->one();
        }
        
        // 读取用户名拼音
        if (in_array('pinyin', $fields)) {
            $result['pinyin'] = PinYin::Pinyin($userProfileResult['realname']);
        }
        
        // 获取用户签名
        if (in_array('signature', $fields)) {
            if ($userProfileResult['signature'] == '') {
                $result['signature'] = '暂无个人签名';
            }
        }
        
        // 粉丝数、关注数
        if (in_array('fans', $fields)) {
            $result['fans'] = (new Follow())->find()->where(['follow_who' => $uid])->count();
        }
        if (in_array('following', $fields)) {
            $result['following'] = (new Follow())->find()->where(['who_follow' => $uid])->count();
        }
        
        // 我是否关注$uid、自己是否被$uid关注
        if (in_array('is_following', $fields)) {
            $result['is_following'] = (new Follow())->find()->where([
                'who_follow' => Yii::$app->getUser()->id,
                'follow_who' => $uid,
            ])->one() ? true : false;
        }
        if (in_array('is_followed', $fields)) {
            $result['is_followed'] = (new Follow())->find()->where([
                'follow_who' => Yii::$app->getUser()->id,
                'who_follow' => $uid,
            ])->one() ? true : false;
        }
        
        // ↑↑↑ 新增字段应该写在在这行注释以上 ↑↑↑
        // 合并结果，不包括缓存
        $result = array_merge($userProfileResult ?: [], $userResult ?: [], $result);
        
        // 写入缓存
        foreach ($result as $field => $value) {
            if (in_array($field, $noCache)) {
                continue;
            }
            if (!in_array($field, ['rank_link', 'icons_html', 'space_link', 'expand_info'])) {
                $value = str_replace('"', '', $value);   //TODO 数据处理
            }
            
            $result[$field] = $value;
            $this->setUserDataWithCache($uid, $field, str_replace('"', '', $value));
        }
        
        // 合并结果，包括缓存
        $result = array_merge($result, $cacheResult);
        
        // 返回结果
        return $result;
    }
    
    protected function getUserDataWithCache($uid, $field)
    {
        return Yii::$app->getCache()->get("query_user_{$uid}_{$field}");
    }
    
    protected function setUserDataWithCache($uid, $field, $value)
    {
        return Yii::$app->getCache()->set("query_user_{$uid}_{$field}", $value, 1800);
    }
    
    public function clearUserData($uid, $field)
    {
        if (is_array($field)) {
            foreach ($field as $field_item) {
                Yii::$app->getCache()->delete("query_user_{$uid}_{$field_item}");
            }
        } else {
            Yii::$app->getCache()->delete("query_user_{$uid}_{$field}");
        }
    }
    
}
