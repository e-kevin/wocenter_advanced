<?php

namespace wocenter\backend\modules\account\models;

use yii\db\ActiveQuery;

/**
 * 注册配置表单模型
 */
class SettingSignupForm extends SettingForm
{
    
    /**
     * @var array 注册时需要填写的档案字段
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
        // 获取身份已关联的档案ID
        if (!$profileIds = $this->identityModel->getProfileId()) {
            return $fieldList;
        }
        // 档案配置里已经勾选的字段
        $extendFields = $this->identityModel->getIdentityConfigs()
            ->select('value')->where(['name' => 'profile'])->scalar();
        if ($extendFields) {
            $extendFieldList = ExtendFieldSetting::find()
                ->where(['id' => explode(',', $extendFields), 'profile_id' => $profileIds])
                ->select('id, field_name, profile_id')
                ->with([
                    'profile' =>
                        function (ActiveQuery $query) {
                            $query->select('id, profile_name');
                        },
                ])
                ->asArray()->indexBy('id')->all();
            
            // 格式化字段数据
            if ($extendFieldList) {
                foreach ($extendFieldList as $key => $val) {
                    $fieldList[$val['profile']['id']]['profile_id'] = $val['profile']['id'];
                    $fieldList[$val['profile']['id']]['profile_name'] = $val['profile']['profile_name'];
                    $fieldList[$val['profile']['id']]['field_list'][$key] = $val['field_name'];
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
