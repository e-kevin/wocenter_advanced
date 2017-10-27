<?php

namespace wocenter\backend\modules\menu\models;

use wocenter\backend\core\ActiveDataProvider;

/**
 * MenuCategorySearch represents the model behind the search form about
 * `wocenter\backend\modules\menu\models\MenuCategory`.
 */
class MenuCategorySearch extends MenuCategory
{
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name', 'description'], 'safe'],
        ];
    }
    
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = MenuCategory::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $this->load($params);
        
        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $query->andFilterWhere(['like', 'id', $this->id])
            ->andFilterWhere(['like', 'name', $this->name]);
        
        return $dataProvider;
    }
    
}
