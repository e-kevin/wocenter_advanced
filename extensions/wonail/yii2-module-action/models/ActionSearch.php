<?php

namespace wocenter\backend\modules\action\models;

use wocenter\backend\core\ActiveDataProvider;
use wocenter\libs\Constants;

/**
 * ActionSearch represents the model behind the search form about `wocenter\backend\modules\action\models\Action`.
 */
class ActionSearch extends Action
{
    
    public $status = Constants::UNLIMITED;
    public $type = Constants::UNLIMITED;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'type'], 'integer'],
            [['name', 'title'], 'safe'],
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
        $query = Action::find();
        
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
            'type' => $this->type != Constants::UNLIMITED ? $this->type : null,
            'status' => $this->status != Constants::UNLIMITED ? $this->status : null,
        ]);
        
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'title', $this->title]);
        
        return $dataProvider;
    }
    
}
