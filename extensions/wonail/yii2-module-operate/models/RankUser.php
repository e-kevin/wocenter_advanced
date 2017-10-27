<?php

namespace wocenter\backend\modules\operate\models;

use wocenter\core\ActiveRecord;
use wocenter\backend\modules\account\models\BaseUser;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%viMJHk_rank_user}}".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $rank_id
 * @property string $reason
 * @property integer $is_show
 * @property integer $status
 * @property integer $created_at
 *
 * @property BaseUser $user
 * @property Rank $rank
 */
class RankUser extends ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_rank_user}}';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => TimestampBehavior::className(),
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
            [['uid', 'rank_id', 'reason', 'is_show', 'status', 'created_at'], 'required'],
            [['uid', 'rank_id', 'is_show', 'status', 'created_at'], 'integer'],
            [['reason'], 'string', 'max' => 255],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => BaseUser::className(), 'targetAttribute' => ['uid' => 'id']],
            [['rank_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rank::className(), 'targetAttribute' => ['rank_id' => 'id']],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'UID',
            'rank_id' => '头衔ID',
            'reason' => '申请理由',
            'is_show' => '是否显示在昵称右侧（必须有图片才可）',
            'status' => '状态',
            'created_at' => '创建时间',
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
    public function getRank()
    {
        return $this->hasOne(Rank::className(), ['id' => 'rank_id']);
    }
    
}
