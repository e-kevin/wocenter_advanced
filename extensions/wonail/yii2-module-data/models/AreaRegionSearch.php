<?php

namespace wocenter\backend\modules\data\models;

use backend\core\ActiveDataProvider;

/**
 * AreaRegionSearch represents the model behind the search form about `wocenter\backend\modules\data\models\AreaRegion`.
 */
class AreaRegionSearch extends AreaRegion
{
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['parent_id', 'required'],
            [['parent_id'], 'integer'],
            [['region_name'], 'safe'],
        ];
    }
    
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params $_POST或$_GET方式传入的参数
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = AreaRegion::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $this->load($params);
        
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $query->where([
            'parent_id' => $this->parent_id,
        ]);
        
        $query->andFilterWhere(['like', 'region_name', $this->region_name]);
        
        return $dataProvider;
    }
    
    /**
     * @inheritdoc
     */
    public function load($data, $formName = null)
    {
        parent::load($data, $formName);
        if (!$this->parent_id) {
            $this->parent_id = isset($data[$this->breadcrumbParentParam])
                ? $data[$this->breadcrumbParentParam]
                : 0;
        }
        
        return true;
    }
}
