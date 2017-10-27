<?php

namespace wocenter\backend\modules\account\models;

use wocenter\backend\modules\log\models\ActionLog;
use wocenter\backend\modules\log\models\UserScoreLog;
use wocenter\backend\modules\operate\models\Invite;
use wocenter\backend\modules\operate\models\InviteBuyLog;
use wocenter\backend\modules\operate\models\InviteLog;
use wocenter\backend\modules\operate\models\InviteUserInfo;
use wocenter\backend\modules\operate\models\RankUser;

/**
 * User model
 *
 * @property ActionLog[] $actionLogs
 * @property BackendUser[] $backendUsers
 * @property ExtendFieldUser[] $extendFieldUsers
 * @property Invite[] $invites
 * @property InviteBuyLog[] $inviteBuyLogs
 * @property InviteLog[] $inviteLogs
 * @property InviteUserInfo[] $inviteUserInfos
 * @property RankUser[] $rankUsers
 * @property UserScoreLog[] $userScoreLogs
 */
class User extends BaseUser
{
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActionLogs()
    {
        return $this->hasMany(ActionLog::className(), ['user_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBackendUsers()
    {
        return $this->hasMany(BackendUser::className(), ['user_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtendFieldUsers()
    {
        return $this->hasMany(ExtendFieldUser::className(), ['uid' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvites()
    {
        return $this->hasMany(Invite::className(), ['uid' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInviteBuyLogs()
    {
        return $this->hasMany(InviteBuyLog::className(), ['uid' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInviteLogs()
    {
        return $this->hasMany(InviteLog::className(), ['inviter_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInviteUserInfos()
    {
        return $this->hasMany(InviteUserInfo::className(), ['uid' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRankUsers()
    {
        return $this->hasMany(RankUser::className(), ['uid' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserScoreLogs()
    {
        return $this->hasMany(UserScoreLog::className(), ['uid' => 'id']);
    }
    
}
