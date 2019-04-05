<?php


namespace app\models;

use yii\db\ActiveRecord;


class Product extends ActiveRecord
{
    //Связь модели с таблицей в базе данных
    public static function tableName()
    {
        return 'product';
    }

    //Один продукт имеет только одну категорию
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }
}