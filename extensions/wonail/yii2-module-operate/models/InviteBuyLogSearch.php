<?php

namespace wocenter\backend\modules\operate\models;

use yii\base\Model;
use wocenter\backend\core\ActiveDataProvider;

/**
 * InviteBuyLogSearch represents the model behind the search form about
 * `wocenter\backend\modules\operate\models\InviteBuyLog`.
 */
class InviteBuyLogSearch extends InviteBuyLog
{
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'invite_type', 'uid', 'num', 'created_at'], 'integer'],
            [['content'], 'safe'],
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
        $query = InviteBuyLog::find();
        
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
            'id' => $this->id,
            'invite_type' => $this->invite_type,
            'uid' => $this->uid,
            'num' => $this->num,
            'created_at' => $this->created_at,
        ]);
        
        $query->andFilterWhere(['like', 'content', $this->content]);
        
        return $dataProvider;
    }
}
