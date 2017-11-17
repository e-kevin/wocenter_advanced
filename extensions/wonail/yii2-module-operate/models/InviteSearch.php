<?php

namespace wocenter\backend\modules\operate\models;

use backend\core\ActiveDataProvider;
use wocenter\libs\Constants;
use yii\db\ActiveQuery;

/**
 * InviteSearch represents the model behind the search form about `wocenter\backend\modules\operate\models\Invite`.
 */
class InviteSearch extends Invite
{
    
    public $status = Constants::UNLIMITED;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invite_type', 'uid', 'status'], 'integer'],
            [['code'], 'string'],
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
        $query = Invite::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query->with([
                'inviteType' => function (ActiveQuery $query) {
                    $query->select('id, title');
                },
                'user' => function (ActiveQuery $query) {
                    $query->select('id, username');
                },
            ]),
        ]);
        
        $this->load($params);
        
        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        switch ($this->status) {
            case Constants::UNLIMITED:
                $this->status = null;
                break;
            case parent::CODE_EXPIRED:
                $this->status = null;
                $query->andWhere(['<', 'expired_at', time()]);
                break;
        }
        
        $query->andFilterWhere([
            'invite_type' => $this->invite_type,
            'uid' => $this->uid,
            'status' => $this->status,
        ]);
        
        $query->andFilterWhere(['like', 'code', $this->code]);
        
        return $dataProvider;
    }
}
