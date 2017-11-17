<?php

namespace wocenter\backend\modules\action\services;

use wocenter\backend\modules\action\models\Action;
use wocenter\backend\modules\action\models\ActionLimit;
use wocenter\backend\modules\log\models\ActionLog;
use wocenter\core\Service;
use wocenter\backend\modules\account\models\BaseUser;
use wocenter\Wc;
use wocenter\helpers\DateTimeHelper;
use wocenter\libs\Utils;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * 行为服务类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class ActionService extends Service
{
    
    /**
     * @var boolean 检测限制行为是否通过，默认为通过
     */
    protected $_status = true;
    
    /**
     * @var array 参数
     */
    private $_params = [];
    
    /**
     * @var array 行为限制信息
     */
    private $_limitInfo;
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'action';
    }
    
    /**
     * 检测行为限制
     *
     * @param string $action 行为标识
     * @param string $modelClassName 触发行为的模型名
     * @param integer $actionUserId 触发行为的用户id，默认为系统触发
     * - null:不检索触发用户数据
     * - 1:系统触发
     * @param integer $recordId 触发行为的记录，默认不检索触发行为的记录id数据
     *
     * @return boolean false - 限制行为 true - 允许行为
     * @throws NotFoundHttpException
     */
    public function checkLimit($action, $modelClassName, $actionUserId = 1, $recordId = null)
    {
        // 查询行为限制，判断是否执行
        $this->_limitInfo = ActionLimit::find()
            ->select('name, time_unit, timestamp, frequency, punish, send_notification,
             action, warning_message, remind_message, finish_message, status, check_ip')
            ->where(['name' => $action])
            ->asArray()->one();
        if (!$this->_limitInfo) {
            throw new NotFoundHttpException(Yii::t('wocenter/app', 'Action limit indicator: {action} does not exist.', [
                'action' => $action,
            ]));
        }
        
        // 设置参数
        $this->setParams($modelClassName, $actionUserId, $recordId);
        
        // 排序惩罚
        $this->_sortPunish();
        
        // 解析提示语模板
        $this->_parseContent();
        
        // 初始化提醒消息
        $this->_initRemindMessage();
        
        // 执行惩罚
        $this->_executePunish();
        
        return $this->_status;
    }
    
    /**
     * 设置参数
     *
     * @param string $modelClassName 触发行为的模型名
     * @param integer $actionUserId 触发行为的用户id，默认为系统触发
     * - null:不检索触发用户数据
     * - 1:系统触发
     * @param integer $recordId 触发行为的记录，默认不检索触发行为的记录id数据
     */
    protected function setParams($modelClassName, $actionUserId, $recordId)
    {
        // 禁用行为限制后则不检索行为日志数据
        if ($this->disabled || $this->_limitInfo['status'] != 1) {
            // 行为限制被禁用则日志总数总为最后一次，确保正确触发提醒信息`finish_message`
            $count_action_log = $this->_limitInfo['frequency'] - 1;
            $begin_log_time = time(); // 行为限制被禁用则最早执行的记录时间为当前时间
            $end_log_time = time(); // 行为限制被禁用则最后执行的记录时间为当前时间
        } // 激活行为限制后则检索行为日志数据
        else {
            // 获取行为限制所绑定的行为日志在有效周期内的记录总数
            $action_log = ActionLog::find()->select('created_at')->where([
                'action_id' => Action::find()->select('id')->where(['name' => $this->_limitInfo['action']])->scalar(),
                'model' => $modelClassName,
            ])->andWhere('created_at >= :ago', [
                ':ago' => DateTimeHelper::getTimeAgo($this->_limitInfo['timestamp'], $this->_limitInfo['time_unit']),
            ])->andFilterWhere([
                'user_id' => $actionUserId,
                'record_id' => $recordId,
                'action_ip' => $this->_limitInfo['check_ip'] ? Utils::getClientIp(1) : null,
            ])->orderBy('created_at ASC')->asArray()->all();
            $count_action_log = count($action_log);
            // 获取该行为在有效周期内最早执行的记录时间，如果不存在，则默认为当前时间
            $begin_log_time = $count_action_log > 0 ? $action_log[0]['created_at'] : time();
            // 获取该行为在有效周期内最后一条日志的记录时间，如果不存在，表示该日志为生成
            $end_log_time = $count_action_log > 0 ? $action_log[$count_action_log - 1]['created_at'] : null;
        }
        
        // 设置参数
        $this->_params = [
            // 触发行为的用户ID
            'user_id' => $actionUserId,
//            // 触发行为的记录id
//            'record_id' => $recordId,
            // 记录总数
            'count_action_log' => $count_action_log,
            // 剩余次数
            'surplus_number' => $this->_limitInfo['frequency'] - $count_action_log - 1,
            // 最开始的记录时间
            'begin_log_time' => $begin_log_time,
            // 最后结束的记录时间
            'end_log_time' => $end_log_time,
            // 下次操作时间
            'next_action_time' => DateTimeHelper::getAfterTime($this->_limitInfo['timestamp'], $this->_limitInfo['time_unit'], $begin_log_time),
        ];
    }
    
    /**
     * 初始化提醒消息
     */
    protected function _initRemindMessage()
    {
        switch (true) {
            case $this->_params['surplus_number'] > 0:
                $this->_info = $this->_limitInfo['remind_message'];
                break;
            case $this->_params['surplus_number'] == 0:
                $this->_info = $this->_limitInfo['finish_message'];
                break;
            default:
                $this->warning();
                break;
        }
    }
    
    /**
     * 频次结束后的提示语
     */
    protected function warning()
    {
        $this->_info = $this->_limitInfo['warning_message'];
        // 频次结束后标识行为限制不通过，直接终止后续操作
        $this->_status = false;
    }
    
    /**
     * 锁定账户，频次结束后执行，且只执行一次
     */
    protected function lockAccount()
    {
        // 没有开启账号锁定功能或不存在有效执行用户则不执行
        if (!Wc::$service->getSystem()->getConfig()->get('USER_LOCK_OPEN') || in_array($this->_params['user_id'], [1, null])) {
            return;
        }
        /* @var BaseUser $class */
        $class = Yii::$app->getUser()->identityClass;
        // 添加锁定日志记录
        Wc::$service->getLog()->create('lock_user', $class::tableName(), $this->_params['user_id'], $this->_params['user_id']);
        
        // 系统级别锁定
        Yii::$app->getDb()->createCommand('UPDATE ' . $class::tableName() . ' SET status=:status WHERE id=:uid', [
            ':uid' => $this->_params['user_id'],
            ':status' => $class::STATUS_LOCKED,
        ])->execute();
        
        // 发送系统通知
        if ($this->_limitInfo['send_notification']) {
            Wc::$service->getNotification()->sendNotify('user_lock', $this->_params['user_id'], [
                'count' => $this->_limitInfo['frequency'],
                'lock_time' => $this->_data['lock_time'],
                'lock_formatted_time' => DateTimeHelper::timeFormat($this->_params['lock_expire_time'], 'H时i分'),
            ]);
        }
    }
    
    /**
     * 登出系统
     */
    protected function logout()
    {
        Wc::$service->getPassport()->getUcenter()->logout();
    }
    
    /**
     * 封锁IP
     */
    protected function lockIp()
    {
    
    }
    
    /**
     * 解析提示语模板
     * 支持变量{timestamp}、{time_unit}、{surplus_number}、{time}、{url}、{next_action_time}::已格式化
     */
    private function _parseContent()
    {
        switch (true) {
            case $this->_params['surplus_number'] > 0:
                $messageType = 'remind_message';
                break;
            case $this->_params['surplus_number'] == 0:
                $messageType = 'finish_message';
                break;
            default:
                $messageType = 'warning_message';
                break;
        }
        // 提示语列表
        $message_list = ['warning_message', 'remind_message', 'finish_message'];
        // 生成模板变量
        $content_data = $this->_generateTemplateData();
        
        // 替换准备提示内容变量
        if (in_array($messageType, $message_list) && !empty($this->_limitInfo[$messageType])) {
            if (preg_match_all('/\{(\S+?)\}/', $this->_limitInfo[$messageType], $match)) {
                $replace = [];
                foreach ($match[1] as $value) {
                    $replace[] = $content_data[$value];
                }
                $this->_limitInfo[$messageType] = str_replace($match[0], array_unique($replace), $this->_limitInfo[$messageType]);
            }
        }
        $this->_limitInfo['warning_message'] = $this->_limitInfo['warning_message'] ?:
            Yii::t('wocenter/app', 'Frequent operation: Please do this after {next_action_time}.', [
                'datetime' => $content_data['next_action_time'],
            ]);
        $this->_limitInfo['remind_message'] = $this->_limitInfo['remind_message'] ?:
            Yii::t('wocenter/app', 'In {next_action_time} you can also perform the operation {surplus_number} times!', [
                'next_action_time' => $content_data['next_action_time'],
                'surplus_number' => $content_data['surplus_number'],
            ]);
    }
    
    /**
     * 生成模板变量
     *
     * @return array
     */
    private function _generateTemplateData()
    {
        // 频率
        $data['timestamp'] = $this->_limitInfo['timestamp'];
        // 频率单位
        $data['time_unit'] = DateTimeHelper::getTimeUnitValue($this->_limitInfo['time_unit']);
        // 剩余次数
        $data['surplus_number'] = $this->_params['surplus_number'];
        // 当前格式化后的时间
        $data['time'] = DateTimeHelper::timeFormat(time(), 'Y-m-d H:i:s');
        // 当前触发URL
        $data['url'] = Yii::$app->getRequest()->getUrl();
        // 下次操作时间
        $data['next_action_time'] = $this->_params['count_action_log'] > 0
            ? DateTimeHelper::timeFormat($this->_params['next_action_time'], 'Y-m-d H:i:s')
            : $data['timestamp'] . $data['time_unit'];
//        $data['user'] = $this->_params['user_id'] == 1 ?
//            '系统' :
//            Wc::$service->getAccount()->queryUser('username', $this->_params['user_id']);
        
        switch (true) {
            case $this->_params['surplus_number'] <= 0:
                // 处罚包含【锁定账号】则获取相关数据
                if (in_array(ActionLimit::CF_LOCK_ACCOUNT, $this->_limitInfo['punish']) ||
                    $this->_limitInfo['name'] == 'lock_user'
                ) {
                    if ($this->_limitInfo['name'] == 'lock_user') {
                        $lockUserInfo = $this->_limitInfo;
                    } else {
                        $lockUserInfo = ActionLimit::find()->select('time_unit, timestamp,warning_message')
                            ->where(['name' => 'lock_user'])->asArray()->one();
                    }
                    // 处罚包含【锁定账号】则设置相应参数返回给客户端
                    $this->_data['lock_time'] = $lockUserInfo['timestamp'];  //e.g. 15
                    $this->_data['lock_time_unit'] = $lockUserInfo['time_unit']; // 锁定时间单位
                    $this->_data['lock_expire_time'] = DateTimeHelper::getAfterTime($lockUserInfo['timestamp'],
                        $lockUserInfo['time_unit'], $this->_params['end_log_time']); // 锁定过期时间戳
                    
                    $data['lock_time'] = $this->_data['lock_time'];
                    $data['lock_time_unit'] = $this->_data['lock_time_unit'];
                    $data['lock_expire_time'] = DateTimeHelper::timeFormat($this->_data['lock_expire_time'], 'Y-m-d H:i:s');
                    if (empty($lockUserInfo['warning_message'])) {
                        $this->_limitInfo['warning_message'] = Yii::t(
                            'wocenter/app',
                            'Your account has been locked, please do this after {datetime} or contact the administrator to unlock.', [
                            'datetime' => $data['lock_time'] . DateTimeHelper::getTimeUnitValue($data['lock_time_unit']) .
                                ' (' . $data['lock_expire_time'] . ')',
                        ]);
                    }
                    // 密码输入错误次数过多，下次操作的时间修正为锁定账号的过期时间
                    $data['next_action_time'] = $data['lock_expire_time'];
                    $this->_params['next_action_time'] = $this->_data['lock_expire_time'];
                }
                break;
        }
        
        return $data;
    }
    
    /**
     * 排序惩罚
     */
    private function _sortPunish()
    {
        $this->_limitInfo['punish'] = explode(',', $this->_limitInfo['punish']);
        $tmp = [];
        foreach (array_keys(ActionLimit::$punishList) as $punish) {
            if (in_array($punish, $this->_limitInfo['punish'])) {
                $tmp[] = $punish;
            }
        }
        $this->_limitInfo['punish'] = $tmp;
        unset($tmp);
    }
    
    /**
     * 执行惩罚
     */
    private function _executePunish()
    {
        if ($this->_params['surplus_number'] == 0 && !($this->disabled || $this->_limitInfo['status'] != 1)) {
            foreach ($this->_limitInfo['punish'] as $punish) {
                if (method_exists($this, $punish)) {
                    $this->$punish();
                }
            }
        }
    }
    
    /**
     * 获取执行结果信息
     */
    public function getInfo()
    {
        $operation = $this->getOperation();
        
        return $this->_info . ($operation && $this->_params['surplus_number'] > 0
                ? Yii::t('wocenter/app', 'After the number of times will automatically run the following: {operations}', [
                    'operations' => $operation,
                ])
                : ''
            );
    }
    
    /**
     * 获取执行结果数据
     *
     * @return array
     */
    public function getData()
    {
        // 标识由哪个行为触发
        $this->_data['action'] = $this->_limitInfo['name'];
        // 剩余次数
        $this->_data['surplus_number'] = $this->_params['surplus_number'];
        // 下次操作时间
        $this->_data['next_action_time'] = $this->_params['next_action_time'];
        // 处罚方式
        $this->_data['operations'] = $this->getOperation(true);
        
        return $this->_data;
    }
    
    /**
     * 获取可以操作的惩罚处理，主要是翻译处罚名字
     *
     * @param boolean $returnArray 是否返回数组格式的数据，默认不返回
     *
     * @return array|string 处罚名字
     */
    protected function getOperation($returnArray = false)
    {
        $tmp = [];
        foreach ($this->_limitInfo['punish'] as $opt) {
            $tmp[] = ActionLimit::$punishList[$opt];
        }
        
        return $returnArray ? $tmp : implode(',', $tmp);
    }
    
}
