<?php

namespace wocenter\backend\modules\notification\models;

use wocenter\backend\core\ActiveDataProvider;
use wocenter\libs\Constants;

/**
 * NotifySearch represents the model behind the search form about `wocenter\backend\modules\notification\models\Notify`.
 */
class NotifySearch extends Notify
{
    
    public $send_message = Constants::UNLIMITED;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['send_message'], 'integer'],
            [['node', 'email_sender'], 'safe'],
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
        $query = Notify::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $this->load($params);
        
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $query->andFilterWhere([
            'send_message' => $this->send_message != Constants::UNLIMITED ? $this->send_message : null,
        ]);
        
        $query->andFilterWhere(['like', 'node', $this->node]);
        $query->andFilterWhere(['like', 'email_sender', $this->email_sender]);
        
        return $dataProvider;
    }
    
}
