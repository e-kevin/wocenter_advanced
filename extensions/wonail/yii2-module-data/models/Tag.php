<?php

namespace wocenter\backend\modules\data\models;

use wocenter\behaviors\TreeBehavior;
use wocenter\core\ActiveRecord;
use wocenter\helpers\ArrayHelper;
use wocenter\Wc;
use Yii;

/**
 * This is the model class for table "{{%viMJHk_tag}}".
 *
 * @property integer $id
 * @property string $title
 * @property integer $status
 * @property integer $parent_id
 * @property integer $sort_order
 *
 * @property TagUser[] $tagUsers
 *
 * 行为方法属性
 * @property string $breadcrumbParentParam
 * @method array getChildrenIds()
 * @method array getParentIds()
 * @method array getTreeSelectList($list, $root = 0)
 * @method array getBreadcrumbs($currentPid = 0, $defaultLabel = '列表', $url = '', $urlParams = [], $appendToTop = [], $append = [])
 */
class Tag extends ActiveRecord
{
    
    const CACHE_ALL_TAG = 'tag_all';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_tag}}';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors[] = [
            'class' => TreeBehavior::className(),
            'showTitleField' => 'title',
        ];
        
        return $behaviors;
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'status', 'parent_id', 'sort_order'], 'required'],
            [['status', 'parent_id', 'sort_order'], 'integer'],
            [['title'], 'string', 'max' => 25],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '名称',
            'status' => '状态',
            'parent_id' => '父节点',
            'sort_order' => '排序',
        ];
    }
    
    /**
     * 获取所有激活的标签筛选列表
     *
     * @return array 标签列表
     */
    public function getSelectList()
    {
        $list = ArrayHelper::listToTree($this->getList());
        $arr = [];
        foreach ($list as $row) {
            if (isset($row['_child'])) {
                foreach ($row['_child'] as $subRow) {
                    $arr[$row['title']][$subRow['id']] = $subRow['title'];
                }
            }
        }
        
        return $arr;
    }
    
    /**
     * 获取所有激活的标签列表
     * 包含字段：id, title
     *
     * @return array 标签列表
     */
    public function getList()
    {
        return self::find()->select('id, title, parent_id')->where(['status' => 1])->asArray()->all();
    }
    
    /**
     * 获取所有标签数据
     *
     * @param integer|boolean $duration 缓存周期，默认缓存`60`秒
     *
     * @return mixed
     */
    public function getAll($duration = 60)
    {
        return Wc::getOrSet(self::CACHE_ALL_TAG, function () {
            return self::find()->asArray()->all() ?: [];
        }, $duration);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTagUsers()
    {
        return $this->hasMany(TagUser::className(), ['tag_id' => 'id']);
    }
    
    
    /**
     * @inheritdoc
     */
    public function clearCache()
    {
        Yii::$app->getCache()->delete(self::CACHE_ALL_TAG);
    }
    
}