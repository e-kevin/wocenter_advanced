<?php

namespace wocenter\backend\modules\account\models;

use wocenter\backend\core\ActiveDataProvider;
use wocenter\backend\modules\account\models\BaseUser;
use yii\db\ActiveQuery;

/**
 * BackendUserSearch represents the model behind the search form about
 * `wocenter\backend\modules\account\models\BackendUser`.
 */
class BackendUserSearch extends BackendUser
{
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'status'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => BaseUser::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }
    
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $queryParams $_POST或$_GET方式传入的参数
     *
     * @return ActiveDataProvider
     */
    public function search($queryParams)
    {
        $query = BackendUser::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query->joinWith([
                'user' => function (ActiveQuery $query) {
                    $query->select('id, username')->joinWith([
                        'userProfile' => function (ActiveQuery $query) {
                            $query->select('uid, reg_time, login_count, last_login_time, last_login_ip, last_location');
                        },
                    ]);
                },
            ]),
        ]);
        
        $this->load($queryParams);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
//             $query->where('0=1');
            return $dataProvider;
        }
        
        $query->andFilterWhere([
            'user_id' => $this->user_id,
            'status' => $this->status,
        ]);
        
        return $dataProvider;
    }
    
}
