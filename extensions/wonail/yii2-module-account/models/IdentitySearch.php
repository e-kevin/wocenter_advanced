<?php

namespace wocenter\backend\modules\account\models;

use backend\core\ActiveDataProvider;
use wocenter\libs\Constants;
use yii\db\ActiveQuery;

/**
 * IdentitySearch represents the model behind the search form about `wocenter\models\Identity`.
 */
class IdentitySearch extends Identity
{
    
    public $status = Constants::UNLIMITED;
    public $is_invite = Constants::UNLIMITED;
    public $is_audit = Constants::UNLIMITED;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['identity_group', 'is_invite', 'is_audit', 'status'], 'integer'],
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
        $query = Identity::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query->with([
                'identityGroup',
                'profiles' => function (ActiveQuery $query) {
                    $query->select('id, profile_name')->asArray();
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
            'identity_group' => $this->identity_group,
            'is_invite' => $this->is_invite != Constants::UNLIMITED ? $this->is_invite : null,
            'is_audit' => $this->is_audit != Constants::UNLIMITED ? $this->is_audit : null,
            'status' => $this->status != Constants::UNLIMITED ? $this->status : null,
        ]);
        
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'title', $this->title]);
        
        return $dataProvider;
    }
    
}
