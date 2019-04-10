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

        return $this->render('index', compact('hits'));
    }


}