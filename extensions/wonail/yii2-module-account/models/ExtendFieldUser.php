<?php

namespace wocenter\backend\modules\account\models;

use wocenter\core\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%viMJHk_extend_field_user}}".
 *
 * @property integer $id
 * @property integer $profile_id
 * @property integer $uid
 * @property integer $field_setting_id
 * @property string $field_data
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property BaseUser $user
 * @property ExtendProfile $profile
 * @property ExtendFieldSetting $fieldSetting
 */
class ExtendFieldUser extends ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_extend_field_user}}';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                ],
            ],
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['profile_id', 'uid', 'field_setting_id', 'field_data', 'created_at', 'updated_at'], 'required'],
            [['profile_id', 'uid', 'field_setting_id', 'created_at', 'updated_at'], 'integer'],
            [['field_data'], 'string', 'max' => 1000],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => BaseUser::className(), 'targetAttribute' => ['uid' => 'id']],
            [['profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendProfile::className(), 'targetAttribute' => ['profile_id' => 'id']],
            [['field_setting_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendFieldSetting::className(), 'targetAttribute' => ['field_setting_id' => 'id']],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'profile_id' => '所属档案',
            'uid' => '所属用户',
            'field_setting_id' => 'Field ID',
            'field_data' => 'Field Data',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
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
    public function getProfile()
    {
        return $this->hasOne(ExtendProfile::className(), ['id' => 'profile_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFieldSetting()
    {
        return $this->hasOne(ExtendFieldSetting::className(), ['id' => 'field_setting_id']);
    }
    
}
