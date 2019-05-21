<?php

namespace app\controllers;

use app\models\Product;
use app\models\Cart;
use app\models\Order;
use app\models\OrderItems;
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
        //Количество товара
        $qty = (int)Yii::$app->request->get('qty');
        //Проверка на наличие
        $qty = ($qty == 0) ? 1 : $qty;
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
        $cart->addToCart($product, $qty);

        //Если данные получены не методом Ajax
        if (!Yii::$app->request->isAjax) {
            //Возврат на адрес с которого пришел пользователь
            return $this->redirect(Yii::$app->request->referrer);
        }

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

    public function actionDelItem()
    {
        //Получение идентификатора товара
        $id = Yii::$app->request->get('id');
        //Старт сессии
        $session = Yii::$app->session;
        //Открытие сессии
        $session->open();
        $cart = new Cart();
        $cart->recalc($id);
        $this->layout = false;
        return $this->render('cart-modal', compact('session'));
    }

    public function actionShow()
    {
        //Получение идентификатора товара
        $session = Yii::$app->session;
        //Открытие сессии
        $session->open();
        $this->layout = false;
        return $this->render('cart-modal', compact('session'));
    }

    public function actionView()
    {
        //Старт сессии
        $session = Yii::$app->session;
        //Открытие сессии
        $session->open();
        $this->setMeta('Корзина');
        $order = new Order();

        //Загрузка данных пришедших с формы
        if ($order->load(Yii::$app->request->post())) {
            $order->qty = $session['cart.qty'];
            $order->qty = $session['cart.sum'];
            if ($order->save()) {
                $this->saveOrderItems($session['cart'], $order->id);
                Yii::$app->session->setFlash('success', 'Заказ принят');

                //Отправка почты
                Yii::$app->mailer->compose('order', ['session' => $session])
                    ->setFrom(['//@bk.ru'=>'yii2.loc'])
                    ->setTo($order->email)// То что пришло из формы
                    ->setSubject('Заказ')
                    ->send();

                //Очистка корзины
                $session->remove('cart');
                $session->remove('cart.qty');
                $session->remove('cart.sum');
                return $this->refresh();
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка оформления заказа');
            }
        }

        return $this->render('view', compact('session', 'order'));
    }

    protected function saveOrderItems($items, $order_id)
    {
        //Проход в цикле по массиву корзины
        foreach ($items as $id => $item) {
            $order_items = new OrderItems();
            $order_items->order_id = $order_id;
            $order_items->product_id = $id;
            $order_items->name = $item['name'];
            $order_items->price = $item['price'];
            $order_items->qty_item = $item['qty'];
            $order_items->sum_item = $item['qty'] * $item['price'];
            $order_items->save();
        }
    }
}