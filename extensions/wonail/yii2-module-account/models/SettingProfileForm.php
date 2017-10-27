<?php

namespace wocenter\backend\modules\account\models;

use yii\db\ActiveQuery;

/**
 * 扩展档案配置表单模型
 */
class SettingProfileForm extends SettingForm
{
    
    /**
     * @var array 档案字段
     */
    public $fields;
    
    /**
     * @var Identity 身份模型
     */
    public $identityModel;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['fields', 'exist', 'skipOnError' => true,
                'targetClass' => ExtendFieldSetting::className(),
                'allowArray' => true,
                'targetAttribute' => 'id',
                'message' => '所选字段不存在，请刷新后重新选择',
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            parent::SCENARIO_DEFAULT => ['fields'],
        ];
    }
    
    /**
     * 获取字段列表数据
     *
     * @return array
     * [
     *      'profile_id',
     *      'profile_name',
     *      'field_list' => [
     *          $id => $name
     *      ]
     * ]
     */
    public function getFieldList()
    {
        $fieldList = [];
        $extendFields = $this->identityModel->getProfiles()->select('id, profile_name')->with([
            'extendFieldSettings' => function (ActiveQuery $query) {
                $query->select('id, field_name, profile_id')->indexBy('id');
            },
        ])->asArray()->indexBy('id')->all();
        
        // 格式化字段数据
        if ($extendFields) {
            foreach ($extendFields as $key => $val) {
                $fieldList[$key]['profile_id'] = $val['id'];
                $fieldList[$key]['profile_name'] = $val['profile_name'];
                $fieldList[$key]['field_list'] = [];
                if ($val['extendFieldSettings']) {
                    foreach ($val['extendFieldSettings'] as $k => $v) {
                        $fieldList[$key]['field_list'][$k] = $v['field_name'];
                    }
                }
            }
        }
        
        return $fieldList;
    }
    
    /**
     * @inheritdoc
     */
    protected function formatConfigValue()
    {
        return $this->fields ? implode(',', $this->fields) : '0';
    }
    
}
