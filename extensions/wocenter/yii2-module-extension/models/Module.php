<?php

namespace wocenter\backend\modules\extension\models;

use wocenter\backend\modules\extension\behaviors\ExtensionBehavior;
use wocenter\db\ActiveRecord;

/**
 * This is the model class for table "{{%viMJHk_module}}".
 *
 * @property string $id
 * @property string $extension_name
 * @property string $module_id
 * @property integer $is_system
 * @property integer $status
 * @property integer $run
 *
 */
class Module extends ActiveRecord
{
    
    /**
     * @var \wocenter\core\ModularityInfo 实例化模块信息类
     */
    public $infoInstance;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_module}}';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors[] = [
            'class' => ExtensionBehavior::className(),
        ];
        
        return $behaviors;
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'module_id', 'extension_name'], 'required'],
            [['is_system', 'status', 'run'], 'integer'],
            [['id'], 'string', 'max' => 64],
            [['extension_name'], 'string', 'max' => 255],
            [['module_id'], 'string', 'max' => 15],
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
            'module_id' => '模块ID',
            'is_system' => '核心扩展',
            'status' => '状态',
            'run' => '运行模式',
        ];
    }
    
    /**
     * 获取已经安装的模块
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getInstalled()
    {
        return self::find()->asArray()->indexBy('extension_name')->all();
    }
    
}
