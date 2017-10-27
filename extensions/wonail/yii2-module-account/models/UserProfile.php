<?php

namespace wocenter\backend\modules\account\models;

use Yii;
use wocenter\core\ActiveRecord;

/**
 * This is the model class for table "{{%viMJHk_user_profile}}".
 *
 * @property integer $uid
 * @property string $nickname
 * @property string $realname
 * @property integer $gender
 * @property string $birthday
 * @property string $question
 * @property string $answer
 * @property string $signature
 * @property integer $login_count
 * @property string $reg_ip
 * @property integer $reg_time
 * @property string $last_login_ip
 * @property integer $last_login_time
 * @property string $last_location
 * @property integer $last_login_identity
 * @property integer $status
 * @property integer $default_identity
 * @property integer $score_1
 * @property integer $score_2
 * @property integer $score_3
 * @property integer $score_4
 *
 * @property User $u
 * @property Identity $lastLoginIdentity
 */
class UserProfile extends ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_user_profile}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'signature', 'reg_ip', 'last_login_ip', 'last_login_time', 'last_location'], 'required'],
            [['uid', 'gender', 'login_count', 'reg_time', 'last_login_time', 'last_login_identity', 'default_identity', 'status'], 'integer'],
            [['birthday'], 'safe'],
            [['score_1', 'score_2', 'score_3', 'score_4'], 'number'],
            [['nickname'], 'string', 'max' => 16],
            [['realname', 'last_location'], 'string', 'max' => 20],
            [['question', 'answer', 'signature'], 'string', 'max' => 50],
            [['reg_ip', 'last_login_ip'], 'string', 'max' => 15],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['uid' => 'id']],
            [['last_login_identity'], 'exist', 'skipOnError' => true, 'targetClass' => Identity::className(), 'targetAttribute' => ['last_login_identity' => 'id']],
        ];
        
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'uid' => Yii::t('wocenter/app', 'Uid'),
            'nickname' => Yii::t('wocenter/app', 'Nickname'),
            'realname' => Yii::t('wocenter/app', 'Realname'),
            'gender' => Yii::t('wocenter/app', 'Gender'),
            'birthday' => Yii::t('wocenter/app', 'Birthday'),
            'question' => Yii::t('wocenter/app', 'Question'),
            'answer' => Yii::t('wocenter/app', 'Answer'),
            'signature' => Yii::t('wocenter/app', 'Signature'),
            'login_count' => Yii::t('wocenter/app', 'Login Count'),
            'reg_ip' => Yii::t('wocenter/app', 'Reg IP'),
            'reg_time' => Yii::t('wocenter/app', 'Reg Time'),
            'last_login_ip' => Yii::t('wocenter/app', 'Last Login IP'),
            'last_login_time' => Yii::t('wocenter/app', 'Last Login Time'),
            'last_location' => Yii::t('wocenter/app', 'Last Location'),
            'status' => Yii::t('wocenter/app', 'Status'),
            'score_1' => Yii::t('wocenter/app', 'Score'),
            'score_2' => '威望',
            'score_3' => '贡献',
            'score_4' => '余额',
            'default_identity' => Yii::t('wocenter/app', 'The default display of identity'),
            'last_login_identity' => Yii::t('wocenter/app', 'The last login identity'),
        ];
    }
    
    /**
     * 获取用户积分信息
     *
     * @param integer $uid 用户ID
     *
     * @return array
     */
    public function getUserScore($uid)
    {
        return self::find()->select('score_1, score_2, score_3, score_4')->where(['uid' => $uid])->asArray()->one();
    }
    
    /**
     * 获取用户最后一次的登录身份
     *
     * @param integer $uid
     *
     * @return string 登录身份
     */
    public function getLoginIdentity($uid)
    {
        $userProfileModel = self::find()->select('last_login_identity, default_identity')->where(['uid' => $uid])->asArray()->one();
        
        return $userProfileModel['last_login_identity'] ?: $userProfileModel['default_identity'];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getU()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLastLoginIdentity()
    {
        return $this->hasOne(Identity::className(), ['id' => 'last_login_identity']);
    }
    
}
