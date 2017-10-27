<?php

namespace wocenter\backend\modules\operate\models;

use wocenter\core\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%viMJHk_rank}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $logo
 * @property integer $created_at
 * @property integer $allow_user_apply  0-否 1-是
 * @property string $label
 * @property string $label_color
 * @property string $label_bg
 *
 * @property RankUser[] $rankUsers
 */
class Rank extends ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_rank}}';
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
            [['name', 'label'], 'required'],
            ['name', 'unique'],
            [['logo', 'created_at', 'allow_user_apply'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['label', 'label_color', 'label_bg'], 'string', 'max' => 10],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'logo' => '图标头衔',
            'created_at' => '创建时间',
            'allow_user_apply' => '允许用户申请',
            'label' => '文字头衔',
            'label_color' => '文字颜色',
            'label_bg' => '背景色',
        ];
    }
    
    /**
     * 获取身份筛选列表
     *
     * @param string $key 待返回的数组键名，默认为id，可选值为 ['id', 'label']
     * @param string $value 待返回的数组值字段名，默认为label
     *
     * @return array 身份列表 [$key => $value [,...]]
     */
    public function getSelectList($key = 'id', $value = 'label')
    {
        return ArrayHelper::map($this->getList(), $key, $value);
    }
    
    /**
     * 获取身份列表
     * 包含字段：id, name, logo, label
     *
     * @return array 身份列表
     */
    public function getList()
    {
        return self::find()->select('id, name, logo, label')->asArray()->all();
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRankUsers()
    {
        return $this->hasMany(RankUser::className(), ['rank_id' => 'id']);
    }
    
}
