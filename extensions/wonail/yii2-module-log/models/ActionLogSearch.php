<?php

namespace wocenter\backend\modules\log\models;

use backend\core\ActiveDataProvider;
use wocenter\libs\Constants;

/**
 * ActionLogSearch represents the model behind the search form about `wocenter\backend\modules\log\models\ActionLog`.
 */
class ActionLogSearch extends ActionLog
{
    
    public $created_type = Constants::UNLIMITED;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['action_id', 'user_id', 'created_type'], 'integer'],
            [['model'], 'safe'],
            ['action_ip', 'string'],
            ['action_ip', 'trim'],
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
        $query = ActionLog::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query->joinWith('action')->orderBy('id DESC'),
        ]);
        
        $this->load($params);
        
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $query->andFilterWhere([
            'action_id' => $this->action_id,
            'user_id' => $this->user_id,
            'action_ip' => $this->action_ip ? ip2long($this->action_ip) : null,
            'created_type' => $this->created_type != Constants::UNLIMITED ? $this->created_type : null,
        ]);
        
        $query->andFilterWhere(['like', 'action_location', $this->action_location])
            ->andFilterWhere(['like', 'model', $this->model]);
        
        return $dataProvider;
    }
    
}
