<?php

namespace wocenter\backend\modules\system\models;

use backend\core\ActiveDataProvider;
use wocenter\libs\Constants;

/**
 * ConfigSearch represents the model behind the search form about `wocenter\backend\modules\system\models\Config`.
 */
class ConfigSearch extends Config
{
    
    public $status = Constants::UNLIMITED;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_group', 'status', 'type'], 'integer'],
            [['name', 'title'], 'safe'],
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
        $query = Config::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy('category_group, sort_order'),
        ]);
        
        $this->load($params);
        
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $query->andFilterWhere([
            'category_group' => $this->category_group,
            'type' => $this->type,
            'status' => $this->status != Constants::UNLIMITED ? $this->status : null,
        ]);
        
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'title', $this->title]);
        
        return $dataProvider;
    }
    
}
