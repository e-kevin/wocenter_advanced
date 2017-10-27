<?php

namespace wocenter\backend\modules\log\models;

use wocenter\backend\modules\account\models\User;
use wocenter\core\ActiveRecord;
use wocenter\backend\modules\action\models\Action;
use wocenter\libs\Utils;
use Yii;

/**
 * This is the model class for table "{{%viMJHk_action_log}}".
 *
 * @property integer $id
 * @property integer $action_id
 * @property integer $user_id
 * @property integer $action_ip
 * @property string $action_location
 * @property string $model
 * @property integer $record_id
 * @property integer $created_at
 * @property integer $created_type
 * @property string $request_url
 *
 * @property Action $action
 * @property User $user
 */
class ActionLog extends ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_action_log}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['action_id', 'user_id', 'action_ip', 'record_id', 'created_at', 'created_type'], 'integer'],
            [['user_id', 'action_ip', 'action_location', 'created_at', 'request_url'], 'required'],
            [['action_location', 'model'], 'string', 'max' => 50],
            [['request_url'], 'string', 'max' => 512],
            [['action_id'], 'exist', 'skipOnError' => true, 'targetClass' => Action::className(), 'targetAttribute' => ['action_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
        
        
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'action_id' => '行为',
            'user_id' => '执行用户',
            'action_ip' => '操作IP',
            'action_location' => '操作地理位置',
            'model' => '触发行为的表',
            'record_id' => '触发行为的数据id',
            'created_at' => '执行时间',
            'created_type' => '日志类型',
            'request_url' => '请求地址',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->action_ip = Utils::getClientIp(1);
                $this->action_location = Utils::getIpLocation();
                $this->created_at = time();
                $this->request_url = urldecode(Yii::$app->getRequest()->getUrl());
            }
            
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAction()
    {
        return $this->hasOne(Action::className(), ['id' => 'action_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
}
