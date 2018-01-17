<?php

namespace wocenter\backend\modules\extension\models;

use wocenter\db\ActiveRecord;

/**
 * This is the model class for table "{{%viMJHk_theme}}".
 *
 * @property string $id
 * @property string $extension_name
 * @property integer $is_system
 * @property integer $status
 */
class Theme extends ActiveRecord
{
    
    /**
     * @var \wocenter\core\ThemeInfo 实例化主题扩展信息类
     */
    public $infoInstance;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_theme}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'extension_name'], 'required'],
            [['is_system', 'status'], 'integer'],
            [['id'], 'string', 'max' => 64],
            [['extension_name'], 'string', 'max' => 255],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'extension_name' => '扩展名称',
            'is_system' => '系统扩展',
            'status' => '状态',
        ];
    }
    
    /**
     * 获取已经安装的主题扩展
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getInstalled()
    {
        return self::find()->asArray()->indexBy('extension_name')->all();
    }
    
}
