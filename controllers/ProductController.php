<?php


namespace app\controllers;

use app\models\Category;
use app\models\Product;
use Yii;
use yii\web\HttpException;

class ProductController extends AppController
{
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        //Ленивая загрузка, получаем 1 продукт по идентификатору
        $product = Product::findOne($id);
        //Если продук пуст
        if(empty($product)) {
            throw new HttpException(404, 'Такого товара нет');
        }

        //Жадная загрузка
        //$product = Product::find()->with('category')->where(['id' => $id])->limit(1)->one();
        //Получение хитовых товаров
        $hits = Product::find()->where(['hit' => '1'])->limit(6)->all();
        $this->setMeta('E-SHOPPER | ' . $product->name, $product->keywords, $product->description);
        return $this->render('view', compact('product', 'hits'));
    }
}