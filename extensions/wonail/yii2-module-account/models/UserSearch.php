<?php

namespace wocenter\backend\modules\account\models;

use backend\core\ActiveDataProvider;
use wocenter\libs\Constants;
use wocenter\backend\modules\passport\models\PassportForm;
use yii\base\InvalidParamException;

/**
 * UserSearch represents the model behind the search form about `wocenter\backend\modules\account\models\User`.
 */
class UserSearch extends User
{
    
    public $gender = Constants::UNLIMITED;
    public $created_by = Constants::UNLIMITED;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'gender', 'created_by'], 'integer'],
            [['username', 'email'], 'trim'],
            [['username', 'email'], 'safe'],
            ['username', 'string', 'length' => [PassportForm::USERNAME_LENGTH_MIN, PassportForm::USERNAME_LENGTH_MAX]],
            ['username', 'match', 'pattern' => '/^[A-Za-z]+\w+$/',
                'message' =>
                    \Yii::t('wocenter/app', 'The user name must begin with a letter, and only in English, figures and underscores.'),
            ],
            ['email', 'email'],
            ['email', 'string', 'length' => [PassportForm::EMAIL_LENGTH_MIN, PassportForm::EMAIL_LENGTH_MAX]],
        ];
    }
    
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $queryParams $_POST或$_GET方式传入的参数
     * @param array $params 额外查询参数
     *
     * @return ActiveDataProvider
     */
    public function search($queryParams, $params)
    {
        if (!isset($params['status'])) {
            throw new InvalidParamException('The "status" param must be set.');
        }
        $query = User::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query->joinWith('userProfile')->where(['{{%viMJHk_user}}.status' => $params['status']])
                ->andWhere(['not in', 'id', 1]),
        ]);
        // 设置默认排序
        if (empty($dataProvider->getSort()->getOrders())) {
            $dataProvider->query->orderBy(['{{%viMJHk_user_profile}}.reg_time' => SORT_DESC]);
        }
        
        $this->load($queryParams);
        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
//             $query->where('0=1');
            return $dataProvider;
        }
        
        $query->andFilterWhere([
            'id' => $this->id,
            'created_by' => $this->created_by != Constants::UNLIMITED ? $this->created_by : null,
            'gender' => $this->gender != Constants::UNLIMITED ? $this->gender : null,
            //'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,
        ]);
        
        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email]);
        
        return $dataProvider;
    }
    
}
