<?php
namespace wocenter\backend\modules\operate\models;

use backend\core\ActiveDataProvider;

/**
 * RankSearch represents the model behind the search form about `wocenter\backend\modules\operate\models\Rank`.
 */
class RankSearch extends Rank
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['allow_user_apply'], 'integer'],
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
        $query = Rank::find();

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
            'allow_user_apply' => $this->allow_user_apply,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
