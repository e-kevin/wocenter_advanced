<?php

namespace wocenter\backend\modules\extension\models;

use wocenter\backend\modules\extension\behaviors\ExtensionBehavior;
use wocenter\db\ActiveRecord;

/**
 * This is the model class for table "{{%viMJHk_module_function}}".
 *
 * @property string $id
 * @property string $module_id
 * @property string $extension_name
 * @property string $controller_id
 * @property integer $is_system
 * @property integer $status
 * @property integer $run
 */
class ModuleFunction extends ActiveRecord
{
    
    /**
     * @var \wocenter\core\FunctionInfo 实例化功能扩展信息类
     */
    public $infoInstance;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_module_function}}';
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
            [['id', 'controller_id', 'extension_name'], 'required'],
            [['is_system', 'status', 'run'], 'integer'],
            [['id', 'module_id', 'controller_id'], 'string', 'max' => 64],
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
            'module_id' => '模块ID',
            'controller_id' => '控制器ID',
            'is_system' => '核心扩展',
            'status' => '状态',
            'run' => '运行模式',
        ];
    }
    
    /**
     * 获取已经安装的功能扩展
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getInstalled()
    {
        return self::find()->asArray()->indexBy('extension_name')->all();
    }
    
}
