<?php

namespace wocenter\backend\modules\operate\models;

use wocenter\backend\modules\account\models\Identity;
use wocenter\backend\modules\account\models\BaseUser;
use wocenter\behaviors\ModifyTimestampBehavior;
use wocenter\core\ActiveRecord;
use wocenter\Wc;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%viMJHk_invite_log}}".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $inviter_id
 * @property integer $invite_type_id
 * @property string $remark
 * @property integer $created_at
 *
 * @property InviteType $inviteType
 * @property BaseUser $inviter
 */
class InviteLog extends ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_invite_log}}';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => ModifyTimestampBehavior::className(),
                'updatedAtAttribute' => false,
                // todo 添加注册时间搜索，按日期搜索
            ],
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'inviter_id', 'invite_type_id', 'remark', 'created_at'], 'required'],
            [['uid', 'inviter_id', 'invite_type_id', 'created_at'], 'integer'],
            [['remark'], 'string', 'max' => 200],
            [['invite_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => InviteType::className(), 'targetAttribute' => ['invite_type_id' => 'id']],
            [['inviter_id'], 'exist', 'skipOnError' => true, 'targetClass' => BaseUser::className(), 'targetAttribute' => ['inviter_id' => 'id']],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '注册者',
            'inviter_id' => '邀请人',
            'invite_type_id' => '邀请码类型',
            'remark' => '信息',
            'created_at' => '注册时间',
        ];
    }
    
    /**
     * 添加邀请注册成功日志
     *
     * @param Invite $codeInfo 邀请码详情
     *  - uid：邀请码所属用户id
     *  - id：邀请码id
     * @param integer $newUid 通过邀请方式新注册的用户ID
     * @param string|integer $registerIdentity 注册身份
     * 值为数字时，则为身份ID
     * 值为字符串时，则为注册身份名
     *
     * @return boolean
     */
    public function create(Invite $codeInfo, $newUid, $registerIdentity)
    {
        $inviterUser = Wc::$service->getAccount()->queryUser('nickname', $codeInfo->uid);
        $newUser = Wc::$service->getAccount()->queryUser('nickname', $newUid);
        $this->uid = $newUid;
        $this->inviter_id = $codeInfo->uid;
        $this->invite_type_id = $codeInfo->invite_type;
        if (is_integer($registerIdentity)) {
            $registerIdentity = (new Identity())->find()->select('title')->where(['id' => $registerIdentity])->scalar();
        }
        $this->remark = "{$newUser} 接受了 {$inviterUser} 的邀请，注册了 {$registerIdentity} 身份。";
        
        return $this->save(false);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInviter()
    {
        return $this->hasOne(BaseUser::className(), ['id' => 'inviter_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInviteType()
    {
        return $this->hasOne(InviteType::className(), ['id' => 'invite_type_id']);
    }
    
}
