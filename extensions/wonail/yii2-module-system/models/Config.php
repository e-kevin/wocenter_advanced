<?php

namespace wocenter\backend\modules\system\models;

use wocenter\db\ActiveRecord;
use wocenter\helpers\ArrayHelper;
use wocenter\Wc;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%viMJHk_config}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $type
 * @property string $title
 * @property string $category_group
 * @property string $extra
 * @property string $remark
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 * @property string $value
 * @property integer $sort_order
 * @property string $rule
 */
class Config extends ActiveRecord
{
    
    const TYPE_STRING = 1;
    const TYPE_TEXT = 2;
    const TYPE_SELECT = 3;
    const TYPE_CHECKBOX = 4;
    const TYPE_RADIO = 5;
    const TYPE_KANBAN = 6;
    const TYPE_DATETIME = 7;
    const TYPE_DATE = 8;
    const TYPE_TIME = 9;
    const CACHE_PREFIX = 'config_';
    
    /**
     * @var int 缓存时间
     */
    public static $cacheDuration = 86400;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_config}}';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            TimestampBehavior::className(),
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'title', 'rule'], 'required'],
            [['type', 'category_group', 'created_at', 'updated_at', 'status', 'sort_order'], 'integer'],
            [['value', 'rule'], 'string'],
            [['name'], 'string', 'max' => 30],
            [['title'], 'string', 'max' => 50],
            [['extra'], 'string', 'max' => 255],
            [['remark'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '标识',
            'type' => '类型',
            'title' => '标题',
            'category_group' => '分组',
            'extra' => '额外数据',
            'remark' => '描述',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
            'status' => '状态',
            'value' => '默认值',
            'sort_order' => '排序',
            'rule' => '验证规则',
        ];
    }
    
    public function attributeHints()
    {
        return [
            'name' => '只能使用英文且不能重复',
            'title' => '用于后台显示的配置标题',
            'sort_order' => '用于分组显示的顺序',
            'type' => '系统会根据不同类型解析配置数据',
            'category_group' => '不分组则不会显示在系统设置中',
            'extra' => '【下拉框、单选框、多选框】类型需要配置该项</br>多个可用英文符号 , ; 或换行分隔，如：</br>key:value, key1:value1; key2:value2',
            'value' => '默认值',
            'remark' => '配置详细说明',
            'rule' => '配置验证规则</br>多条规则用英文符号 ; 或换行分隔，如：</br>required;string,max:10,min:4;string,length:1-3',
        ];
    }
    
    /**
     * 获取指定分类的配置项
     *
     * @param string $categoryId 分类ID
     *
     * @return Config[]
     */
    public static function getConfigByCategory(string $categoryId)
    {
        return Config::find()->select(['id', 'title', 'name', 'extra', 'remark', 'value', 'type', 'category_group', 'rule'])
            ->where(['category_group' => $categoryId, 'status' => 1])
            ->indexBy('name')
            ->orderBy('sort_order')
            ->all();
    }
    
    /**
     * 获取所有配置项
     *
     * @return array
     */
    public static function getAllConfig(): array
    {
        return Wc::getOrSet(self::CACHE_PREFIX . 'all', function () {
            return Config::find()
                ->select(['name', 'value', 'extra'])
                ->where(['status' => 1])
                ->indexBy('name')
                ->asArray()
                ->all();
        }, self::$cacheDuration, null, 'commonCache');
    }
    
    /**
     * @inheritdoc
     */
    public function clearCache()
    {
        Wc::cache()->delete(self::CACHE_PREFIX . 'all');
    }
    
}
