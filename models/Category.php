<?php

namespace app\models;

use yii\db\ActiveRecord;

class Category extends ActiveRecord
{
    //Связь модели с таблицей в базе данных
    public static function tableName()
    {
        return 'category';
    }

    //В одной категории содержится много товаров
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['category_id' => 'id']);
    }
}