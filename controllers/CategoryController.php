<?php


namespace app\controllers;

use app\models\Category;
use app\models\Product;
use Yii;

class CategoryController extends AppController
{
    public function actionIndex()
    {
        //Выбор товаров являющихся хитами
        $hits = Product::find()->where(['hit' => '1'])->limit(6)->all();

        $this->setMeta('E_SHOPPER');

        return $this->render('index', compact('hits'));
    }

    public function actionView($id)
    {
        //Получили номер категории
        $id = Yii::$app->request->get('id');
        //Получили все продукты по заданной категории
        $products = Product::find()->where(['category_id' => $id])->all();
        //Получили все данные из выбранной категории
        $category = Category::findOne($id);
        //Устанавливаем метатеги
        $this->setMeta('E-SHOPPER | ' . $category->name, $category->keywords, $category->description);

        return $this->render('view', compact('products', 'category'));
    }

}