<?php

namespace wocenter\backend\modules\account\models;

use wocenter\backend\core\ActiveDataProvider;
use wocenter\libs\Constants;

/**
 * ExtendProfileSearch represents the model behind the search form about
 * `wocenter\backend\modules\account\models\ExtendProfile`.
 */
class ExtendProfileSearch extends ExtendProfile
{
    
    public $status = Constants::UNLIMITED;
    public $visible = Constants::UNLIMITED;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'visible'], 'integer'],
            [['profile_name'], 'safe'],
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
        $query = ExtendProfile::find();
        
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
            'status' => $this->status != Constants::UNLIMITED ? $this->status : null,
            'visible' => $this->visible != Constants::UNLIMITED ? $this->visible : null,
        ]);
        
        $query->andFilterWhere(['like', 'profile_name', $this->profile_name]);
        
        return $dataProvider;
    }
}
