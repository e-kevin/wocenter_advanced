<?php

namespace wocenter\backend\modules\operate\models;

use wocenter\core\ActiveRecord;
use wocenter\backend\modules\account\models\BaseUser;

/**
 * This is the model class for table "{{%viMJHk_invite_user_info}}".
 *
 * @property integer $id
 * @property integer $invite_type
 * @property integer $uid
 * @property integer $num
 * @property integer $already_num
 * @property integer $success_num
 *
 * @property BaseUser $user
 * @property InviteType $inviteType
 */
class InviteUserInfo extends ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_invite_user_info}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invite_type', 'uid', 'num', 'already_num', 'success_num'], 'required'],
            [['invite_type', 'uid', 'num', 'already_num', 'success_num'], 'integer'],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => BaseUser::className(), 'targetAttribute' => ['uid' => 'id']],
            [['invite_type'], 'exist', 'skipOnError' => true, 'targetClass' => InviteType::className(), 'targetAttribute' => ['invite_type' => 'id']],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invite_type' => '邀请类型id',
            'uid' => '邀请者',
            'num' => '可邀请名额',
            'already_num' => '已邀请名额',
            'success_num' => '成功邀请名额',
        ];
    }
    
    /**
     * (邀请注册成功后)增加邀请码用户的成功邀请名额
     *
     * @param integer $inviteType 邀请类型ID
     * @param integer $inviterUid 邀请者ID
     *
     * @return boolean
     */
    public function increaseSuccessNum($inviteType = 0, $inviterUid = 0)
    {
        $info = self::findOne(['uid' => $inviterUid, 'invite_type' => $inviteType]);
        if ($info == null) {
            return false;
        }
        
        return $info->updateCounters(['success_num']);
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
    public function getInviteType()
    {
        return $this->hasOne(InviteType::className(), ['id' => 'invite_type']);
    }
    
}
