<?php

namespace app\models;

use yii\db\ActiveRecord;

class Cart extends ActiveRecord
{
    public function addToCart($product, $qty = 1)
    {
        //Проверка есть ли в массиве сессии такой же продукт
        if (isset($_SESSION['cart'][$product->id])) {
            //Увеличим количество на 1
            $_SESSION['cart'][$product->id]['qty'] += $qty;
        } else {
            //Добавляем новый товар в корзину
            $_SESSION['cart'][$product->id] = [
                'qty' => $qty,
                'name' => $product->name,
                'price' => $product->price,
                'img' => $product->img
            ];
        }
        //Если товар есть в корзине, то прибавить, иначе товар новый
        $_SESSION['cart.qty'] = isset($_SESSION['cart.qty']) ? $_SESSION['cart.qty'] + $qty : $qty;
        //Тоже для цены
        $_SESSION['cart.sum'] = isset($_SESSION['cart.sum']) ? $_SESSION['cart.sum'] + $qty * $product->price : $qty * $product->price;
    }
}