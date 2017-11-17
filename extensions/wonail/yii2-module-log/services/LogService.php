<?php

namespace wocenter\backend\modules\log\services;

use wocenter\core\Service;
use wocenter\Wc;
use wocenter\helpers\DateTimeHelper;
use wocenter\backend\modules\action\models\Action;
use wocenter\backend\modules\log\models\ActionLog;
use wocenter\backend\modules\data\models\UserScoreType;
use Yii;
use yii\base\InvalidParamException;
use yii\base\InvalidValueException;

/**
 * 日志服务类
 *
 * @author E-Kevin <e-kevin@qq.com>
 */
class LogService extends Service
{
    
    /**
     * @var string|array|callable|Action 行为模型
     */
    public $actionModel = '\wocenter\backend\modules\action\models\Action';
    
    /**
     * @var string|array|callable|ActionLog 行为日志模型
     */
    public $actionLogModel = '\wocenter\backend\modules\log\models\ActionLog';
    
    /**
     * @var string|array|callable|UserScoreType 用户积分类型模型
     */
    public $userScoreTypeModel = '\wocenter\backend\modules\data\models\UserScoreType';
    
    /**
     * @var string|array|callable|\wocenter\backend\modules\log\models\UserScoreLog 用户奖罚日志模型
     */
    public $userScoreLogModel = '\wocenter\modules\log\models\UserScoreLog';
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 'log';
    }
    
    /**
     * 记录行为日志，并执行该行为的规则
     *
     * @param string $action 行为标识
     * @param string $modelClassName 触发行为的模型名
     * @param integer $recordId 触发行为的记录id
     * @param integer $actionUserId 触发操作的用户id，默认为系统触发
     *
     * @return boolean
     * @throws InvalidParamException
     * @throws InvalidValueException
     */
    public function create($action, $modelClassName, $recordId = 0, $actionUserId = 1)
    {
        // 禁用日志功能
        if ($this->disabled) {
            return $this->_status;
        }
        
        // 查询行为，判断是否存在
        /** @var Action $actionModel */
        $actionModel = $this->actionModel;
        $actionInfo = $actionModel::find()->select('id,rule,status,type')->where(['name' => $action])->asArray()->one();
        if (!$actionInfo) {
            throw new InvalidValueException(Yii::t('wocenter/app', 'Action indicator: {action} does not exist.', [
                'action' => $action,
            ]));
        } elseif ($actionInfo['status'] != 1) {
            // 如果行为被禁用，则终止后续操作
            $this->_info = Yii::t('wocenter/app', 'Action: {action} is disabled.', ['action' => $action]);
            
            return $this->_status;
        }
        
        // 添加日志
        /** @var ActionLog $actionLog */
        $actionLog = new $this->actionLogModel();
        $data = [
            'action_id' => $actionInfo['id'],
            'user_id' => $actionUserId,
            'record_id' => $recordId,
            'model' => $modelClassName,
            'created_type' => $actionInfo['type'],
        ];
        $actionLog->load($data, '');
        $this->_status = $actionLog->save(false);
        
        // 执行行为规则
        if ($this->_status && !empty($actionInfo['rule'])) {
            $this->_executeAction($actionInfo['rule'], $modelClassName, $actionUserId, $actionLog->getAttributes());
        }
        
        return $this->_status;
    }
    
    /**
     * 删除记录行为日志
     *
     * @param string $action 行为标识
     * @param string $modelClassName 触发行为的模型名
     * @param integer $recordId 触发行为的记录id
     * @param integer $actionUserId 触发行为的用户id，默认为系统触发
     *
     * @throws InvalidParamException
     */
    public function delete($action = '', $modelClassName = '', $recordId = 0, $actionUserId = 1)
    {
        if (empty($action) || empty($modelClassName)) {
            throw new InvalidParamException(Yii::t('wocenter/app', 'Empty parameters.'));
        }
        
        /** @var ActionLog $actionLogModel */
        $actionLogModel = $this->actionLogModel;
        /** @var Action $actionModel */
        $actionModel = $this->actionModel;
        $actionLogModel::deleteAll([
            'action_id' => $actionModel::find()->select('id')->where(['name' => $action])->scalar(),
            'model' => $modelClassName,
            'user_id' => $actionUserId,
            'record_id' => $recordId,
        ]);
    }
    
    /**
     * 执行行为规则
     *
     * @param string $rules 行为规则 [type, rule, cycle, max]
     * @param string $modelClassName 触发行为的模型名
     * @param integer $actionUserId 执行的用户id
     * @param object $logInfo 记录信息数组
     *
     * @return boolean false 失败 ， true 成功
     */
    private function _executeAction($rules = '', $modelClassName = '', $actionUserId = 0, $logInfo = null)
    {
        // 没有触发用户或系统触发则不执行
        if (empty($actionUserId) || $actionUserId === 1) {
            return false;
        }
        $this->_parseActionRule($rules);
        if (empty($rules)) {
            return false;
        }
        
        foreach ((array)$rules as $rule) {
            if ($this->_checkActionRule($rule, $modelClassName, $actionUserId)) {
                Wc::$service->getAccount()->updateUserScore($actionUserId, $rule['rule'], $rule['type'], $logInfo);
            }
        }
        
        return true;
    }
    
    /**
     * 解析行为规则
     *
     * 规则定义 type:$type,rule:$rule[,cycle:$cycle,max:$max][;......]
     * 规则字段解释：
     * - type:积分类型
     * - rule:对字段进行的具体操作，目前为需要操作的积分数值
     * - cycle:执行周期，单位（小时），表示$cycle小时内最多执行$max次
     * - max:单个周期内的最大执行次数
     * 单个规则后可加 [;] 连接其他规则
     *
     * @param string &$rules 行为规则
     */
    private function _parseActionRule(&$rules = '')
    {
        if (empty($rules)) {
            return;
        }
        
        $tmp = [];
        foreach (unserialize($rules) as $key => &$rule) {
            foreach (explode(',', $rule) as $fields) {
                $field = empty($fields) ? [] : explode(':', $fields);
                if (!empty($field)) {
                    $tmp[$key][$field[0]] = $field[1];
                }
            }
            if (!array_key_exists('cycle', $tmp[$key])) {
                $tmp[$key]['cycle'] = 1;
            }
            if (!array_key_exists('max', $tmp[$key])) {
                $tmp[$key]['max'] = 1;
            }
        }
        $rules = $tmp;
        unset($tmp);
    }
    
    /**
     * 判断行为规则是否可以执行
     *
     * @param array $rule 行为规则
     * @param string $modelClassName 触发行为的模型名
     * @param integer $actionUserId 执行的用户id
     *
     * @return boolean 是否可以执行
     */
    private function _checkActionRule(array $rule, $modelClassName = '', $actionUserId = 0)
    {
        // 如果积分类型为余额，则跳过执行，行为记录不直接处理用户余额数据
        $userScoreTypeModel = $this->userScoreTypeModel;
        if ($userScoreTypeModel::TYPE_YUE == $rule['type']) {
            return false;
        }
        // 检查执行周期
        /** @var \wocenter\backend\modules\log\models\UserScoreLog $userScoreLogModel */
        $userScoreLogModel = $this->userScoreLogModel;
        $exec_count = $userScoreLogModel::find()->where([
            'type' => $rule['type'],
            'model' => $modelClassName,
            'uid' => $actionUserId,
        ])->andWhere('created_at >= :created_at', [
            ':created_at' => DateTimeHelper::getTimeAgo($rule['cycle'], DateTimeHelper::HOUR),
        ])->count();
        
        // 如果执行记录次数大于最大执行次数，则跳过执行
        return $exec_count < $rule['max'];
    }
    
}
