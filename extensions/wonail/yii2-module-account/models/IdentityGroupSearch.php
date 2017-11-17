<?php

namespace wocenter\backend\modules\account\models;

use backend\core\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * IdentityGroupSearch represents the model behind the search form about
 * `wocenter\models\IdentityGroup`.
 */
class IdentityGroupSearch extends IdentityGroup
{
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'safe'],
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
        $query = IdentityGroup::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query->with([
                'identities' => function (ActiveQuery $query) {
                    $query->select('title, identity_group')->asArray();
                },
            ]),
        ]);
        
        $this->load($params);
        
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $query->andFilterWhere(['like', 'title', $this->title]);
        
        return $dataProvider;
    }
    
}
