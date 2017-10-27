<?php

namespace wocenter\backend\modules\operate\models;

use wocenter\core\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\web\User;

/**
 * This is the model class for table "{{%viMJHk_invite_buy_log}}".
 *
 * @property integer $id
 * @property integer $invite_type
 * @property integer $uid
 * @property integer $num
 * @property string $content
 * @property integer $created_at
 *
 * @property User $user
 * @property InviteType $inviteType
 */
class InviteBuyLog extends ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_invite_buy_log}}';
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
                'updatedAtAttribute' => false,
            ],
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invite_type', 'uid', 'num', 'content', 'created_at'], 'required'],
            [['invite_type', 'uid', 'num', 'created_at'], 'integer'],
            [['content'], 'string', 'max' => 200],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'id']],
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
            'uid' => 'UID',
            'num' => '可邀请名额',
            'content' => '记录信息',
            'created_at' => '创建时间（做频率用）',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInviteType()
    {
        return $this->hasOne(InviteType::className(), ['id' => 'invite_type']);
    }
    
}
