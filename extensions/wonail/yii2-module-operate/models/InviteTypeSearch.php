<?php

namespace wocenter\backend\modules\operate\models;

use yii\base\Model;
use backend\core\ActiveDataProvider;

/**
 * InviteTypeSearch represents the model behind the search form about
 * `wocenter\backend\modules\operate\models\InviteType`.
 */
class InviteTypeSearch extends InviteType
{
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['length', 'cycle_num', 'pay_score', 'pay_score_type', 'increase_score', 'increase_score_type', 'each_follow', 'status'], 'integer'],
            [['title', 'identities', 'auth_groups'], 'safe'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
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
        $query = InviteType::find();
        
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
            'length' => $this->length,
            'cycle_num' => $this->cycle_num,
            'pay_score' => $this->pay_score,
            'pay_score_type' => $this->pay_score_type,
            'increase_score' => $this->increase_score,
            'increase_score_type' => $this->increase_score_type,
            'each_follow' => $this->each_follow,
            'status' => $this->status,
        ]);
        
        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'identities', $this->identities])
            ->andFilterWhere(['like', 'auth_groups', $this->auth_groups]);
        
        return $dataProvider;
    }
}
