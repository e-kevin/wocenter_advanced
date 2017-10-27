<?php

namespace wocenter\backend\modules\data\models;

use wocenter\backend\modules\log\models\UserScoreLog;
use wocenter\core\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%viMJHk_user_score_type}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $status
 * @property string $unit
 *
 * @property string $selectList
 *
 * @property UserScoreLog[] $userScoreLogs
 */
class UserScoreType extends ActiveRecord
{
    
    const TYPE_JIFEN = 1;
    const TYPE_WEIWANG = 2;
    const TYPE_GONGXIANG = 3;
    const TYPE_YUE = 4;
    const ACTION_SUBTRACTION = 1;
    const ACTION_PLUS = 0;
    /**
     * 积分上限
     */
    const SCORE_MAX = 10;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_user_score_type}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'status', 'unit'], 'required'],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 10],
            ['name', 'unique'],
            [['unit'], 'string', 'max' => 20],
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
            'status' => '状态',
            'unit' => '单位',
        ];
    }
    
    /**
     * 修正积分数值，主要是限定积分上限
     *
     * @param string $score 积分数值，格式如：1, +1, -1
     *
     * @return array ['scoreFormat', 'score', 'operator']
     *  - scoreFormat：格式化后的积分数值，如：+1、-1
     *  - score：经过数值检测后需要操作的积分数值
     *  - operator：运算符 0-加法 1-减法
     */
    public static function parseScore($score = '')
    {
        $subtraction = strpos($score, '-') !== false;
        $plus = strpos($score, '+') !== false;
        if ($plus || $subtraction) {
            $score = substr($score, 1);
        }
        // 限制最大操作数值
        if ($score > self::SCORE_MAX) {
            $score = self::SCORE_MAX;
        }
        
        return ['scoreFormat' => empty($score) ? $score : (($subtraction ? '-' : '+') . $score), 'score' => $score, 'operator' => $subtraction ? self::ACTION_SUBTRACTION : self::ACTION_PLUS];
    }
    
    /**
     * 获取积分类型列表（所有数据）
     * 包含字段：id, name, status, unit
     *
     * @return array 积分类型列表
     */
    public function getList()
    {
        return self::find()->select('id,name,status,unit')->asArray()->all();
    }
    
    /**
     * 获取积分类型筛选列表（所有数据）
     *
     * @return array 积分类型筛选列表
     */
    public function getSelectList()
    {
        return ArrayHelper::map($this->getList(), 'id', 'name');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserScoreLogs()
    {
        return $this->hasMany(\wocenter\backend\modules\log\models\UserScoreLog::className(), ['type' => 'id']);
    }
    
}
