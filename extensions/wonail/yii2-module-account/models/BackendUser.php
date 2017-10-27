<?php

namespace wocenter\backend\modules\account\models;

use wocenter\core\ActiveRecord;
use wocenter\helpers\ArrayHelper;
use wocenter\helpers\StringHelper;
use wocenter\backend\modules\account\models\BaseUser;
use Yii;

/**
 * This is the model class for table "{{%viMJHk_backend_user}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $status
 *
 * @property BaseUser $user
 *
 */
class BackendUser extends ActiveRecord
{
    
    /**
     * 更新场景
     */
    const SCENARIO_UPDATE = 'update';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_backend_user}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'status'], 'integer'],
            [['user_id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => BaseUser::className(), 'targetAttribute' => ['user_id' => 'id']],
            ['status', 'validateStatus', 'on' => self::SCENARIO_UPDATE],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return ArrayHelper::merge(parent::scenarios(), [
            self::SCENARIO_UPDATE => ['status'],
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => Yii::t('wocenter/app', 'UID'),
            'status' => Yii::t('wocenter/app', 'Status'),
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [
            'user_id' => '请输入需要添加的用户ID',
            'status' => '是否允许管理员登录后台管理系统',
        ];
    }
    
    /**
     * 验证管理员状态合法性
     *
     * @param string $attribute
     */
    public function validateStatus($attribute)
    {
        if ($this->getOldAttribute('status') != $this->status) {
            if ($this->user_id == 1) {
                $this->addError($attribute, '无法更改系统默认管理员状态');
            }
            if ($this->user_id == Yii::$app->getUser()->getId()) {
                $this->addError($attribute, '无法更改自己的状态');
            }
        }
    }
    
    /**
     * 解除管理员
     *
     * @param integer $uid 要解除的用户id
     *
     * @return bool
     */
    public function relieve($uid)
    {
        $uid = StringHelper::parseIds($uid);
        if (empty($uid)) {
            $this->message = Yii::t('wocenter/app', 'Select the data to be operated.');
            
            return false;
        }
        if (count($uid) > 1) {
            $uid = $uid[0];
        }
        if (in_array(1, $uid)) {
            $this->message = '无法解除系统默认管理员';
            
            return false;
        }
        if (in_array(Yii::$app->getUser()->getId(), $uid)) {
            $this->message = Yii::t('wocenter/app', "Can't do yourself.");
            
            return false;
        }
        
        return self::deleteAll(['id' => $uid]);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(BaseUser::className(), ['id' => 'user_id']);
    }
    
}
