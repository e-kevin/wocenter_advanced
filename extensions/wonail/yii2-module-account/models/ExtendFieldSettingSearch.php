<?php

namespace wocenter\backend\modules\account\models;

use backend\core\ActiveDataProvider;
use wocenter\libs\Constants;

/**
 * ExtendFieldSettingSearch represents the model behind the search form about
 * `wocenter\backend\modules\account\models\ExtendFieldSetting`.
 */
class ExtendFieldSettingSearch extends ExtendFieldSetting
{
    
    public $status = Constants::UNLIMITED;
    public $visible = Constants::UNLIMITED;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['profile_id', 'required'],
            [['visible', 'status', 'profile_id'], 'integer'],
            [['field_name'], 'safe'],
        ];
    }
    
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $queryParams $_POST或$_GET方式传入的参数
     *
     * @return ActiveDataProvider
     */
    public function search($queryParams)
    {
        $query = ExtendFieldSetting::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy('sort_order'),
        ]);
        
        $this->load($queryParams);
        
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $query->where([
            'profile_id' => $this->profile_id,
        ]);
        
        $query->andFilterWhere([
            'visible' => $this->visible != Constants::UNLIMITED ? $this->visible : null,
            'status' => $this->status != Constants::UNLIMITED ? $this->status : null,
        ]);
        
        $query->andFilterWhere(['like', 'field_name', $this->field_name]);
        
        return $dataProvider;
    }
    
    /**
     * @inheritdoc
     */
    public function load($data, $formName = null)
    {
        parent::load($data, $formName);
        if (!$this->profile_id) {
            $this->profile_id = isset($data['profile_id']) && !empty($data['profile_id'])
                ? $data['profile_id']
                : null;
        }
        
        return true;
    }
    
}
