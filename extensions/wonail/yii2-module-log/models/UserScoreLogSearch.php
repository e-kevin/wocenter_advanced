<?php

namespace wocenter\backend\modules\log\models;

use backend\core\ActiveDataProvider;
use wocenter\libs\Constants;

/**
 * UserScoreLogSearch represents the model behind the search form about
 * `wocenter\backend\modules\log\models\UserScoreLog`.
 */
class UserScoreLogSearch extends UserScoreLog
{
    
    public $type = Constants::UNLIMITED;
    public $action = Constants::UNLIMITED;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'type', 'action'], 'integer'],
            ['ip', 'string'],
            ['ip', 'trim'],
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
        $query = UserScoreLog::find();
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query->joinWith('typeValue')->orderBy('id DESC'),
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
            'uid' => $this->uid, /** todo:根据提交的用户昵称转换为用户ID进行搜索 */
            'ip' => $this->ip ? ip2long($this->ip) : null,
            'type' => $this->type != Constants::UNLIMITED ? $this->type : null,
            'action' => $this->action != Constants::UNLIMITED ? $this->action : null,
        ]);
        
        return $dataProvider;
    }
    
}
