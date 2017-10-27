<?php

namespace wocenter\backend\modules\account\models;

use wocenter\backend\modules\operate\models\Rank;

/**
 * 头衔配置表单模型
 *
 * @property array $rankList 头衔列表
 */
class SettingRankForm extends SettingForm
{
    
    /**
     * @var array 头衔
     */
    public $rank;
    
    /**
     * @var string 头衔颁发原因
     */
    public $reason;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['rank', 'exist', 'skipOnError' => true,
                'targetClass' => Rank::className(),
                'allowArray' => true,
                'targetAttribute' => 'id',
                'message' => '所选头衔不存在，请刷新后重新选择',
            ],
            ['reason', function ($attribute) {
                if (!empty($this->rank) && empty($this->$attribute)) {
                    $this->addError($attribute, "{$this->attributeLabels()[$attribute]}不能为空");
                }
            }, 'skipOnEmpty' => false],
            ['reason', 'string', 'max' => 140],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            parent::SCENARIO_DEFAULT => ['rank', 'reason'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rank' => '头衔',
            'reason' => '头衔颁发原因',
        ];
    }
    
    /**
     * 验证头衔合法性
     *
     * @param $attribute
     */
    public function validateRanks($attribute)
    {
        $rankList = $this->getRankList();
        foreach ($this->$attribute as $rank) {
            if (!in_array($rank, array_keys($rankList))) {
                $this->addError($attribute, "所选头衔不存在，请刷新后重新选择");
            }
        }
    }
    
    /**
     * 获取头衔列表数据
     * TODO 是否只显示用户可申请的头衔
     *
     * @return array
     */
    public function getRankList()
    {
        return (new Rank())->getSelectList();
    }
    
    /**
     * @inheritdoc
     */
    protected function formatConfigValue()
    {
        return json_encode(['data' => $this->rank, 'reason' => $this->reason]);
    }
    
}
