<?php

namespace wocenter\backend\modules\account\models;

use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * UserIdentitySearch represents the model behind the search form about
 * `wocenter\models\UserIdentity`.
 */
class UserIdentitySearch extends UserIdentity
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'identity_id', 'status'], 'integer'],
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
        $query = UserIdentity::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query->with([
                'identity' => function (ActiveQuery $query) {
                    $query->select(['id', 'title']);
                },
            ]),
        ]);
        
        $this->load($params);
        
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $query->andFilterWhere([
            'uid' => $this->uid,
            'identity_id' => $this->identity_id,
            'status' => $this->status,
        ]);
        
        return $dataProvider;
    }
}
