<?php

namespace wocenter\backend\modules\account\models;

use wocenter\backend\modules\data\models\Tag;

/**
 * 标签配置表单模型
 *
 * @property array $tagList 标签列表
 */
class SettingTagForm extends SettingForm
{
    
    /**
     * @var array 标签
     */
    public $tag;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['tag', 'exist', 'skipOnError' => true,
                'targetClass' => Tag::className(),
                'allowArray' => true,
                'targetAttribute' => 'id',
                'message' => '所选标签不存在，请刷新后重新选择',
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            parent::SCENARIO_DEFAULT => ['tag'],
        ];
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
     * 获取标签列表数据
     *
     * @return array
     */
    public function getTagList()
    {
        return (new Tag())->getSelectList();
    }
    
    /**
     * @inheritdoc
     */
    protected function formatConfigValue()
    {
        return implode(',', $this->tag);
    }
    
}
