<?php

namespace wocenter\backend\modules\extension\models;

use wocenter\core\ActiveRecord;
use wocenter\Wc;
use Yii;

/**
 * This is the model class for table "{{%viMJHk_theme}}".
 *
 * @property string $id
 * @property string $app
 * @property string $name
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
            [['id', 'app', 'name'], 'required'],
            [['is_system', 'status'], 'integer'],
            [['id'], 'string', 'max' => 64],
            [['app', 'name'], 'string', 'max' => 15],
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
            'name' => '主题名称',
            'is_system' => '系统扩展',
            'status' => '状态',
        ];
    }
    
    /**
     * 获取当前应用已经安装的主题扩展
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getInstalledThemes()
    {
        return self::find()->where(['app' => Yii::$app->id, 'status' => 1])->asArray()->indexBy('id')->all();
    }
    
    /**
     * @inheritdoc
     */
    public function clearCache()
    {
        Wc::$service->getExtension()->getTheme()->clearCache();
    }
    
}
