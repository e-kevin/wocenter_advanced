<?php

namespace wocenter\backend\modules\extension\models;

use wocenter\core\ActiveRecord;
use wocenter\Wc;
use Yii;

/**
 * This is the model class for table "{{%viMJHk_module}}".
 *
 * @property string $id
 * @property string $app
 * @property string $module_id
 * @property integer $is_system
 * @property integer $status
 * @property integer $run
 *
 * @property array $runList 获取运行模块列表
 * @property array $validRunList 获取有效的运行模块列表
 */
class Module extends ActiveRecord
{
    
    /**
     * @var integer 运行核心模块
     */
    const RUN_MODULE_CORE = 0;
    
    /**
     * @var integer 运行开发者模块
     */
    const RUN_MODULE_DEVELOPER = 1;
    
    /**
     * @var \wocenter\core\ModularityInfo 实例化模块信息类
     */
    public $infoInstance;
    
    /**
     * @var array 有效的运行模块列表
     */
    protected $_validRunModuleList;
    
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
    public function rules()
    {
        return [
            [['id', 'module_id'], 'required'],
            [['is_system', 'status', 'run'], 'integer'],
            [['id'], 'string', 'max' => 64],
            [['app', 'module_id'], 'string', 'max' => 15],
            [['app'], 'in', 'range' => array_keys(Yii::$app->params['appList'])],
            [['run'], 'in', 'range' => array_keys($this->getValidRunList())],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'app' => '所属应用',
            'module_id' => '模块ID',
            'is_system' => '系统模块',
            'status' => '状态',
            'run' => '运行版本',
        ];
    }
    
    /**
     * 获取当前应用已经安装的模块ID
     *
     * @return array 已经安装的模块ID
     */
    public function getInstalledModuleId()
    {
        return self::find()->select('id')->where(['app' => Yii::$app->id])->column();
    }
    
    /**
     * 获取当前应用已经安装的模块
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getInstalledModules()
    {
        return self::find()->where(['app' => Yii::$app->id])->asArray()->indexBy('id')->all();
    }
    
    /**
     * @inheritdoc
     */
    public function clearCache()
    {
        Wc::$service->getMenu()->syncMenus();
        Wc::$service->getExtension()->getModularity()->clearCache();
    }
    
    /**
     * 获取运行版本列表
     *
     * @return array
     */
    public function getRunList()
    {
        return [
            self::RUN_MODULE_DEVELOPER => '开发者模块',
            self::RUN_MODULE_CORE => '扩展模块',
        ];
    }
    
    /**
     * 获取有效的运行版本列表
     *
     * @return array
     */
    public function getValidRunList()
    {
        return $this->_validRunModuleList ?: $this->getRunList();
    }
    
    /**
     * 设置有效的运行版本列表
     *
     * @param $moduleList
     */
    public function setValidRunList($moduleList)
    {
        $this->_validRunModuleList = $moduleList;
    }
    
}
