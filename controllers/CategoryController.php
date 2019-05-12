<?php


namespace app\controllers;

use app\models\Category;
use app\models\Product;
use Yii;
use yii\data\Pagination;
use yii\web\HttpException;
use Symfony\Component\Console\Tests\Helper\AutocompleteValues;

class CategoryController extends AppController
{
    public function actionIndex()
    {
        //Выбор товаров являющихся хитами
        $hits = Product::find()->where(['hit' => '1'])->limit(6)->all();

        $this->setMeta('E_SHOPPER');

        return $this->render('index', compact('hits'));
    }

    public function actionView()
    {
        //Получили номер категории
        $id = Yii::$app->request->get('id');

        //Получили все данные из выбранной категории
        $category = Category::findOne($id);
        //Если массив категории пуст
        if (empty($category)) {
            throw new HttpException(404, 'Такой категории нет');
        }

        //Получили все продукты по заданной категории
        //$products = Product::find()->where(['category_id' => $id])->all();
        $query = Product::find()->where(['category_id' => $id]);
        //Количество товаров
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 3, 'forcePageParam' => false, 'pageSizeParam' => false]
        );
        //https://www.yiiframework.com/doc/guide/2.0/en/output-pagination
        $products = $query->offset($pages->offset)->limit($pages->limit)->all();
        //Устанавливаем метатеги
        $this->setMeta('E-SHOPPER | ' . $category->name, $category->keywords, $category->description);

        return $this->render('view', compact('products', 'pages', 'category'));
    }

    public function actionSearch()
    {
        //Получение запроса
        $q = Yii::$app->request->get('q');

        //Поиск по имени
        $query = Product::find()->where(['like', 'name', $q]);
        //Количество товаров
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 3, 'forcePageParam' => false, 'pageSizeParam' => false]
        );
        //https://www.yiiframework.com/doc/guide/2.0/en/output-pagination
        $products = $query->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('search', compact('products', 'pages', 'q'));
    }

}