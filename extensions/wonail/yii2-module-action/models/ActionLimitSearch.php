<?php

namespace wocenter\backend\modules\action\models;

use wocenter\backend\core\ActiveDataProvider;
use wocenter\libs\Constants;

/**
 * ActionLimitSearch represents the model behind the search form about
 * `wocenter\backend\modules\action\models\ActionLimit`.
 */
class ActionLimitSearch extends ActionLimit
{
    
    public $status = Constants::UNLIMITED;
    public $send_notification = Constants::UNLIMITED;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['send_notification', 'status'], 'integer'],
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
        $query = ActionLimit::find();
        
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
            'send_notification' => $this->send_notification != Constants::UNLIMITED ? $this->send_notification : null,
            'status' => $this->status != Constants::UNLIMITED ? $this->status : null,
        ]);
        
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'title', $this->title]);
        
        return $dataProvider;
    }
    
}
