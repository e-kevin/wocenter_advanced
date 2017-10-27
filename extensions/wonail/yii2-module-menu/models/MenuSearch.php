<?php

namespace wocenter\backend\modules\menu\models;

use wocenter\backend\core\ActiveDataProvider;

/**
 * MenuSearch represents the model behind the search form about `wocenter\backend\modules\menu\models\Menu`.
 */
class MenuSearch extends Menu
{
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'parent_id'], 'required'],
            [['parent_id'], 'integer'],
            [['category_id'], 'safe'],
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
        $query = Menu::find()->orderBy('sort_order ASC, category_id');
        
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $this->load($params);
        
        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        
        $query->where([
            'parent_id' => $this->parent_id,
            'category_id' => $this->category_id,
        ]);
        
        return $dataProvider;
    }
    
    /**
     * @inheritdoc
     */
    public function load($data, $formName = null)
    {
        parent::load($data, $formName);
        if (!$this->parent_id) {
            $this->parent_id = isset($data[$this->breadcrumbParentParam])
                ? $data[$this->breadcrumbParentParam]
                : 0;
        }
        if (!$this->category_id) {
            $this->category_id = isset($data['category'])
                ? $data['category']
                : null;
        }
        
        return true;
    }
    
}
