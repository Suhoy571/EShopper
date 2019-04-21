<?php


namespace app\controllers;

use app\models\Category;
use app\models\Product;
use Yii;

class ProductController extends AppController
{
    public function actionView($id)
    {
        $id = Yii::$app->request->get('id');
        //Ленивая загрузка, получаем 1 продукт по идентификатору
        $product = Product::findOne($id);
        //Жадная загрузка
        //$product = Product::find()->with('category')->where(['id' => $id])->limit(1)->one();
        return $this->render('view', compact('product'));
    }
}