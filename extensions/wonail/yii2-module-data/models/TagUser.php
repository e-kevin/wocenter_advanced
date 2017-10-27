<?php

namespace wocenter\backend\modules\data\models;

use wocenter\core\ActiveRecord;
use wocenter\backend\modules\account\models\BaseUser;

/**
 * This is the model class for table "{{%viMJHk_tag_user}}".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $tag_id
 *
 * @property Tag $tag
 * @property BaseUser $user
 */
class TagUser extends ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_tag_user}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'tag_id'], 'required'],
            [['uid', 'tag_id'], 'integer'],
            [['tag_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tag::className(), 'targetAttribute' => ['tag_id' => 'id']],
            [['uid'], 'exist', 'skipOnError' => true, 'targetClass' => BaseUser::className(), 'targetAttribute' => ['uid' => 'id']],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '用户ID',
            'tag_id' => '标签ID',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTag()
    {
        return $this->hasOne(Tag::className(), ['id' => 'tag_id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(BaseUser::className(), ['id' => 'uid']);
    }
    
}
