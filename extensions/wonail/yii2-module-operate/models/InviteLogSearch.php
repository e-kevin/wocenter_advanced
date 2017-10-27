<?php

namespace wocenter\backend\modules\operate\models;

use wocenter\backend\core\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * InviteLogSearch represents the model behind the search form about
 * `wocenter\backend\modules\operate\models\InviteLog`.
 */
class InviteLogSearch extends InviteLog
{
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'inviter_id', 'invite_type_id', 'created_at'], 'integer'],
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
        $query = InviteLog::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query->with([
                'inviteType' => function (ActiveQuery $query) {
                    $query->select('id, title');
                },
            ])->orderBy(['uid' => SORT_DESC]),
        ]);
        
        $this->load($params);
        
        if (!$this->validate()) {
            return $dataProvider;
        }
        
        $query->andFilterWhere([
            'uid' => $this->uid,
            'inviter_id' => $this->inviter_id,
            'invite_type_id' => $this->invite_type_id,
            'created_at' => $this->created_at,
        ]);
        
        return $dataProvider;
    }
}
