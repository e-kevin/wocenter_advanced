<?php

namespace wocenter\backend\modules\extension\models;

use wocenter\core\ActiveRecord;
use wocenter\Wc;
use Yii;

/**
 * This is the model class for table "{{%viMJHk_module_function}}".
 *
 * @property string $id
 * @property string $app
 * @property string $module_id
 * @property string $controller_id
 * @property integer $is_system
 * @property integer $status
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
    public function rules()
    {
        return [
            [['id', 'app', 'controller_id'], 'required'],
            [['is_system', 'status'], 'integer'],
            [['id', 'module_id', 'controller_id'], 'string', 'max' => 64],
            [['app'], 'string', 'max' => 15],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'module_id' => '模块ID',
            'controller_id' => '控制器ID',
            'app' => '所属应用',
            'is_system' => '系统扩展',
            'status' => '状态',
        ];
    }
    
    /**
     * 获取当前应用已经安装的功能扩展
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getInstalledControllers()
    {
        return self::find()->where(['app' => Yii::$app->id])->asArray()->indexBy('id')->all();
    }
    
    /**
     * @inheritdoc
     */
    public function clearCache()
    {
        Wc::$service->getMenu()->syncMenus();
        Wc::$service->getExtension()->getController()->clearCache();
    }
    
}
