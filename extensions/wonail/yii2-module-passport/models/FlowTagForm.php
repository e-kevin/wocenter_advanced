<?php

namespace wocenter\backend\modules\passport\models;

use wocenter\core\Model;
use wocenter\backend\modules\data\models\TagUser;
use wocenter\backend\modules\account\models\UserIdentity;
use wocenter\backend\modules\data\models\Tag;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 * 注册流程 - 填写标签
 */
class FlowTagForm extends Model
{
    
    /**
     * @var integer 用户ID，数据来源在PassportForm()->login()里设置
     */
    public $uid;
    
    /**
     * @var integer 身份ID，数据来源在PassportForm()->login()里设置
     */
    public $identityId;
    
    /**
     * @var array 用户当前注册流程的进度数据，['step', 'nextStep']
     */
    public $userStep;
    
    /**
     * @var array 标签
     */
    public $tag = [];
    
    /**
     * @var array 标签列表数据
     */
    public $tagList;
    
    /**
     * @var array 数据库有效标签IDS
     */
    private $_tagIds = [];
    
    /**
     * @var TagUser[] 用户已经设置的标签数据
     */
    private $_tagUser;
    
    /**
     * @var array 用户已经设置的标签ID数据
     */
    private $_userTags = [];
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (empty($this->uid) || empty($this->identityId)) {
            throw new InvalidConfigException('身份验证信息已过期，请重新登录');
        }
        if (empty($this->userStep)) {
            throw new InvalidConfigException('缺少必要参数 {userStep}');
        }
        $this->tagList = (new Tag())->getSelectList();
        // 没有有效标签则跳过本流程
        if (is_null($this->tagList)) {
            (new UserIdentity())->updateStep($this->uid, $this->identityId, $this->userStep);
            
            return null;
        }
        foreach ($this->tagList as $row) {
            if (is_array($row) && !empty($row)) {
                foreach ($row as $key => $value) {
                    $this->_tagIds[] = $key;
                }
            }
        }
        // 获取用户已经拥有的标签信息
        $this->_tagUser = TagUser::find()->where(['uid' => $this->uid])->indexby('tag_id')->all() ?: [];
        // 设置标签默认值
        if (!empty($this->_tagUser)) {
            $this->tag = $this->_userTags = ArrayHelper::getColumn($this->_tagUser, 'tag_id');
        }
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [Model::SCENARIO_DEFAULT => ['tag']];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tag' => '标签',
        ];
    }
    
    /**
     * 保存用户-标签关联信息，包括更新已有，新建未有
     *
     * @param array $data
     * @param boolean $canSkip 步骤是否可以跳过
     *
     * @return boolean
     */
    public function save($data, $canSkip = false)
    {
        if ($canSkip) {
            return (new UserIdentity())->updateStep($this->uid, $this->identityId, $this->userStep);
        }
        // 加载数据，用于验证数据合法性
        $this->load($data);
        if (!$this->validate()) {
            return false;
        }
        
        $res = true;
        $add = [];
        if (count($this->_tagUser) > 0) {
            TagUser::deleteAll(['uid' => $this->uid]);
        }
        // 排除非法数据
        $this->tag = array_intersect($this->_tagIds, $this->tag ?: []);
        if ($this->tag) {
            foreach ($this->tag as $tag) {
                $add[] = [
                    'uid' => $this->uid,
                    'tag_id' => $tag,
                ];
            }
            $res = Yii::$app->getDb()->createCommand()->batchInsert(TagUser::tableName(), array_keys($add[0]), $add)->execute();
        }
        if ($res) {
            (new UserIdentity())->updateStep($this->uid, $this->identityId, $this->userStep);
        }
        
        return $res;
    }
    
}
