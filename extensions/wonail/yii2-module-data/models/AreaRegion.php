<?php

namespace wocenter\backend\modules\data\models;

use wocenter\behaviors\TreeBehavior;
use wocenter\core\ActiveRecord;
use wocenter\Wc;
use Yii;

/**
 * This is the model class for table "{{%viMJHk_area_region}}".
 *
 * @property integer $region_id
 * @property integer $parent_id
 * @property string $region_name
 * @property integer $region_type
 *
 * @property array $regionTypeList 区域类型列表
 *
 * 行为方法属性
 * @property string $breadcrumbParentParam
 * @method array getChildrenIds()
 * @method array getParentIds()
 * @method array getTreeSelectList($list, $root = 0)
 * @method array getBreadcrumbs($currentPid = 0, $defaultLabel = '列表', $url = '', $urlParams = [], $appendToTop = [], $append = [])
 */
class AreaRegion extends ActiveRecord
{
    
    const CACHE_ALL_REGION = 'area_region_all';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_area_region}}';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors[] = [
            'class' => TreeBehavior::className(),
            'showTitleField' => 'region_name',
            'showPkField' => 'region_id',
            'pkField' => 'region_id',
        ];
        
        return $behaviors;
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'region_type'], 'integer'],
            [['region_name'], 'string', 'max' => 12],
            [['region_name', 'region_type'], 'required'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'region_id' => 'ID',
            'parent_id' => '父节点',
            'region_name' => '地区',
            'region_type' => '级别',
            'regionTypeValue' => '级别',
        ];
    }
    
    /**
     * 获取区域类型列表
     *
     * @return array
     */
    public function getRegionTypeList()
    {
        return ['国家', '省份', '城市', '镇区'];
    }
    
    /**
     * 获取区域类型值
     *
     * @return mixed
     */
    public function getRegionTypeValue()
    {
        return $this->regionTypeList[$this->region_type];
    }
    
    /**
     * 获取所有区域数据
     *
     * @param boolean|integer $duration 缓存周期，默认缓存`60`秒
     *
     * @return mixed
     */
    public function getAll($duration = 60)
    {
        return Wc::getOrSet(self::CACHE_ALL_REGION, function () {
            return self::find()->asArray()->all() ?: [];
        }, $duration);
    }
    
    /**
     * @inheritdoc
     */
    public function clearCache()
    {
        Yii::$app->getCache()->delete(self::CACHE_ALL_REGION);
    }
    
}
