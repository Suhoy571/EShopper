<?php

namespace app\controllers;

use app\models\Product;
use app\models\Cart;
use Yii;

/*Array
(
    [1] => Array
    (
        [qty] => QTY
        [name] => NAME
        [price] => PRICE
        [img] => IMG
    )
    [10] => Array
    (
        [qty] => QTY
        [name] => NAME
        [price] => PRICE
        [img] => IMG
    )
)
    [qty] => QTY,
    [sum] => SUM
);*/

class CartController extends AppController
{
    public function actionAdd()
    {
        //Получение идентификатора товара
        $id = Yii::$app->request->get('id');
        //Получение продукта по id
        $product = Product::find()
            ->select(['name', 'price', 'img', 'id'])
            ->where(['id' => $id])
            ->one();

        if (empty($product))
            return false;

        //Старт сессии
        $session = Yii::$app->session;
        //Открытие сессии
        $session->open();

        $cart = new Cart();
        $cart->addToCart($product);
//        debug($session['cart']);
//        debug($session['cart.qty']);
//        debug($session['cart.sum']);
        $this->layout = false;
        return $this->render('cart-modal', compact('session'));
    }

    public function actionClear()
    {
        //Старт сессии
        $session = Yii::$app->session;
        //Открытие сессии
        $session->open();
        $session->remove('cart');
        $session->remove('cart.qty');
        $session->remove('cart.sum');
        $this->layout = false;
        return $this->render('cart-modal', compact('session'));
    }
}