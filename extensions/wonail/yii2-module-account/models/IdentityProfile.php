<?php

namespace wocenter\backend\modules\account\models;

use wocenter\core\ActiveRecord;

/**
 * This is the model class for table "{{%viMJHk_identity_profile}}".
 *
 * @property integer $id
 * @property integer $identity_id
 * @property integer $profile_id
 *
 * @property ExtendProfile $profile
 * @property Identity $identity
 */
class IdentityProfile extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_identity_profile}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['identity_id', 'profile_id'], 'required'],
            [['identity_id', 'profile_id'], 'integer'],
            [['profile_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExtendProfile::className(), 'targetAttribute' => ['profile_id' => 'id']],
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
            'identity_id' => '身份ID',
            'profile_id' => '档案ID',
        ];
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
    public function getIdentity()
    {
        return $this->hasOne(Identity::className(), ['id' => 'identity_id']);
    }
}
