<?php

namespace wocenter\backend\modules\log\models;

use wocenter\core\ActiveRecord;
use wocenter\backend\modules\data\models\UserScoreType;
use wocenter\libs\Utils;
use wocenter\backend\modules\account\models\BaseUser;
use Yii;

/**
 * This is the model class for table "{{%viMJHk_user_score_log}}".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $ip
 * @property integer $type 奖励类型
 * @property integer $action 调整类型 0:加 1:减
 * @property double $value 积分变动
 * @property double $finally_value
 * @property integer $created_at
 * @property string $remark
 * @property string $model
 * @property integer $record_id
 * @property string $request_url
 *
 * @property array $actionList 操作类型列表
 *
 * @property BaseUser $user
 * @property UserScoreType $typeValue
 */
class UserScoreLog extends ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_user_score_log}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'type', 'ip', 'action', 'value', 'finally_value', 'created_at', 'remark', 'model', 'record_id', 'request_url'], 'required'],
            [['uid', 'type', 'ip', 'action', 'created_at', 'record_id'], 'integer'],
            [['value', 'finally_value'], 'number'],
            [['remark'], 'string', 'max' => 255],
            [['model'], 'string', 'max' => 20],
            [['request_url'], 'string', 'max' => 512],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => BaseUser::className(), 'targetAttribute' => ['uid' => 'id']],
            [['type'], 'exist', 'skipOnError' => true, 'targetClass' => UserScoreType::className(), 'targetAttribute' => ['type' => 'id']],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '执行用户',
            'ip' => '操作IP',
            'type' => '奖励类型',
            'action' => '调整类型',
            'actionValue' => '调整类型',
            'value' => '奖励变动',
            'finally_value' => '奖励最终值',
            'created_at' => '创建时间',
            'remark' => '变动描述',
            'model' => '触发模型',
            'record_id' => '触发记录ID',
            'request_url' => '请求地址',
        ];
    }
    
    /**
     * 获取操作类型列表
     *
     * @return array
     */
    public function getActionList()
    {
        return ['增加', '减少'];
    }
    
    /**
     * 获取操作类型值
     *
     * @return mixed
     */
    public function getActionValue()
    {
        return $this->actionList[$this->action];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->ip = ip2long(Utils::getClientIp());
                $this->created_at = time();
                $this->request_url = Yii::$app->getRequest()->getUrl();
            }
            
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTypeValue()
    {
        return $this->hasOne(UserScoreType::className(), ['id' => 'type']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(BaseUser::className(), ['id' => 'uid']);
    }
    
}
