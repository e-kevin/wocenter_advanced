<?php

namespace wocenter\backend\modules\account\models;

use wocenter\core\ActiveRecord;
use wocenter\Wc;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%viMJHk_follow}}".
 *
 * @property integer $id
 * @property integer $followWho
 * @property integer $whoFollow
 * @property integer $created_at
 * @property string $alias
 * @property integer $group_id
 */
class Follow extends ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_follow}}';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors[] = [
            'class' => TimestampBehavior::className(),
            'updatedAtAttribute' => false,
        ];
        
        return $behaviors;
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['follow_who', 'who_follow', 'created_at', 'alias', 'group_id'], 'required'],
            [['follow_who', 'who_follow', 'created_at', 'group_id'], 'integer'],
            [['alias'], 'string', 'max' => 40],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'follow_who' => '关注谁',
            'who_follow' => '谁关注',
            'created_at' => '创建时间',
            'alias' => '备注',
            'group_id' => '分组ID',
        ];
    }
    
    /**
     * 添加关注
     *
     * @param integer $whoFollow 谁关注
     * @param integer $followWho 关注谁
     * @param boolean $isInvite 是否邀请关注，默认为false
     *
     * @return boolean
     */
    public function addFollow($whoFollow, $followWho, $isInvite = false)
    {
        if ($whoFollow == $followWho) {
            $this->message = '关注与被关注不能为同一个用户';
            
            return false;
        }
        $data['who_follow'] = $whoFollow;
        $data['follow_who'] = $followWho;
        if (self::find()->where($data)->exists()) {
            $this->message = '已经关注成功';
            
            return false;
        }
        
        // 确保互相关注可以正常运行
        $model = new Follow();
        
        // 添加关注
        $model->load($data, '');
        
        if ($model->insert(false) == false) {
            $this->message = '关注失败';
            
            return false;
        }
        
        $nickname = Wc::$service->getAccount()->queryUser('nickname', $whoFollow);
        if ($isInvite) {
            if ($whoFollow < $followWho) {
                $content = "邀请人 {$nickname} 关注了你";
            } else {
                $content = "你邀请的用户 {$nickname} 关注了你";
            }
        } else {
            $content = "用户 {$nickname} 关注了你";
//            if ($whoFollow < $followWho) {
//                $content = "系统推荐用户 {$nickname} 关注了你";
//            } else {
//                $content = "用户 {$nickname} 关注了你";
//            }
        }
        
        // todo 添加粉丝数改变
        
        // 发送系统通知
        Wc::$service->getNotification()->getMessage()->send($followWho, $whoFollow, '粉丝数增加', $content);
        
        return true;
    }
    
    /**
     * 互相关注
     *
     * @param integer $user1 谁关注
     * @param integer $user2 关注谁
     * @param boolean $isInvite 是否邀请关注，默认为false
     */
    public function eachFollow($user1 = 0, $user2 = 0, $isInvite = false)
    {
        // 如果用户为系统默认管理员，则只单向关注，即系统管理员不关注任何人，其他用户单向关注系统管理员
        if ($user1 != 1) {
            $this->addFollow($user1, $user2, $isInvite);
        }
        $this->addFollow($user2, $user1, $isInvite);
    }
    
}
