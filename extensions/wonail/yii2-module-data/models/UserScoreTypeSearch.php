<?php

namespace wocenter\backend\modules\data\models;

use backend\core\ActiveDataProvider;
use wocenter\libs\Constants;

/**
 * UserScoreTypeSearch represents the model behind the search form about
 * `wocenter\backend\modules\data\models\UserScoreType`.
 */
class UserScoreTypeSearch extends UserScoreType
{
    
    public $status = Constants::UNLIMITED;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status'], 'integer'],
            [['name'], 'safe'],
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
        $query = UserScoreType::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
//            'pagination' => [
//                'pageSize' => 1
//            ]
        ]);
        
        $this->load($params);
        
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $query->andFilterWhere([
            'status' => $this->status !== Constants::UNLIMITED ? $this->status : null,
        ]);
        
        $query->andFilterWhere(['like', 'name', $this->name]);
        
        return $dataProvider;
    }
}
