<?php

namespace wocenter\backend\modules\menu\models;

use wocenter\db\ActiveRecord;

/**
 * This is the model class for table "{{%viMJHk_menu_category}}".
 *
 * @property string $id
 * @property string $name
 * @property string $description
 */
class MenuCategory extends ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%viMJHk_menu_category}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id', 'name'], 'string', 'max' => 64],
            [['description'], 'string', 'max' => 512],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '标识',
            'name' => '名称',
            'description' => '描述',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if (Menu::findOne(['category_id' => $this->id])) {
                $this->message = '删除该数据前请删除或转移其下的子类数据';
                
                return false;
            }
            
            return true;
        }
        
        return false;
    }
    
}
