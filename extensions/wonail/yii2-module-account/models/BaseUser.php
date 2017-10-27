<?php

namespace wocenter\backend\modules\account\models;

use wocenter\behaviors\ModifyTimestampBehavior;
use wocenter\core\ActiveRecord;
use wocenter\backend\modules\data\models\TagUser;
use wocenter\Wc;
use Yii;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use wocenter\interfaces\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $mobile
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $is_audit
 * @property integer $is_active
 * @property integer $validate_email
 * @property integer $validate_mobile
 * @property integer $created_by 注册方式　0:普通注册 1:邀请注册 2:系统自动生成
 *
 * @property string $password
 *
 * @property Follow[] $whoFollows
 * @property Follow[] $followWho
 * @property TagUser[] $tagUsers
 * @property UserIdentity[] $userIdentities
 * @property UserProfile $userProfile
 *
 * 行为方法属性
 * @property boolean $modifyUpdatedAt
 * @property boolean $modifyCreatedAt
 * @property string $createdAtAttribute
 * @property string $updatedAtAttribute
 * @method ModifyTimestampBehavior createRules($rules)
 * @see ModifyTimestampBehavior::createRules()
 * @method ModifyTimestampBehavior createScenarios($scenarios)
 * @see ModifyTimestampBehavior::createScenarios()
 */
class BaseUser extends ActiveRecord implements IdentityInterface
{
    
    /**
     * @var integer 普通注册
     */
    const CREATED_BY_USER = 0;
    
    /**
     * @var integer 邀请注册
     */
    const CREATED_BY_INVITE = 1;
    
    /**
     * @var integer 系统自动生成
     */
    const CREATED_BY_SYSTEM = 2;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_user}}';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => ModifyTimestampBehavior::className(),
            ],
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['username', 'auth_key', 'email', 'password_hash'], 'required'],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED, self::STATUS_FORBIDDEN, self::STATUS_LOCKED]],
            [['status', 'created_at', 'updated_at', 'is_audit', 'validate_email', 'validate_mobile', 'is_active', 'created_by'], 'integer'],
            ['email', 'email'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['mobile'], 'string', 'max' => 15],
            [['password_reset_token', 'username', 'email'], 'unique'],
        ];
        
        return $this->createRules($rules);
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios['update'] = ['status'];
        
        return $this->createScenarios($scenarios);
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->generateAuthKey();
                $this->status = self::STATUS_ACTIVE;
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }
    
    /**
     * Finds user by identity
     *
     * @param string|integer $identity 用户标识 [手机，邮箱，用户名]
     * @param array $conditions 查询条件
     *
     * @return null|static
     */
    public static function findByIdentity($identity, $conditions = [])
    {
        $param = Wc::$service->getPassport()->getUcenter()->parseIdentity($identity);
        
        return static::findOne(array_merge([$param => $identity], $conditions));
    }
    
    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     *
     * @return User|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        
        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }
    
    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     *
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        
        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Wc::$service->getSystem()->getConfig()->get('PASSWORD_RESET_TOKEN_EXPIRE');
        
        return $timestamp + $expire >= time();
    }
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }
    
    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }
    
    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
    
    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }
    
    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }
    
    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }
    
    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('wocenter/app', 'ID'),
            'username' => Yii::t('wocenter/app', 'Username'),
            'mobile' => Yii::t('wocenter/app', 'Mobile'),
            'email' => Yii::t('wocenter/app', 'Email'),
            'password_hash' => Yii::t('wocenter/app', 'Password'),
            'password_reset_token' => Yii::t('wocenter/app', 'Password Reset Token'),
            'created_at' => Yii::t('wocenter/app', 'Created At'),
            'updated_at' => Yii::t('wocenter/app', 'Updated At'),
            'status' => Yii::t('wocenter/app', 'Status'),
            'auth_key' => Yii::t('wocenter/app', 'Auth Key'),
            'gender' => Yii::t('wocenter/app', 'Gender'),
            'is_audit' => Yii::t('wocenter/app', 'Audit'),
            'validate_email' => Yii::t('wocenter/app', 'Validate email'),
            'validate_mobile' => Yii::t('wocenter/app', 'Validate mobile'),
            'is_active' => Yii::t('wocenter/app', 'Active'),
            'created_by' => Yii::t('wocenter/app', 'Register Type'),
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = parent::fields();
        
        // 去掉一些包含敏感信息的字段
        unset($fields['auth_key'], $fields['password_hash'], $fields['password_reset_token']);
        
        return $fields;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWhoFollows()
    {
        return $this->hasMany(Follow::className(), ['who_follow' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFollowWho()
    {
        return $this->hasMany(Follow::className(), ['follow_who' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTagUsers()
    {
        return $this->hasMany(TagUser::className(), ['uid' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserIdentities()
    {
        return $this->hasMany(UserIdentity::className(), ['uid' => 'id']);
    }
    
    /**
     * 获取用户详情
     *
     * @param string $fields 需要查询的字段
     *
     * @return $this
     */
    public function getUserProfile($fields = '')
    {
        return $this->hasOne(UserProfile::className(), ['uid' => 'id'])->select($fields);
    }
    
}
