<?php


namespace app\components;

use Yii;
use yii\base\Widget;
use app\models\Category;

class MenuWidget extends Widget
{
    public $tpl;
    public $data; //Массив данных категориий из базы
    public $tree; //Результат работы функции, построение дерава категорий
    public $menuHtml; //Готовый HTML-код дерева

    public function init()
    {
        parent::init();
        if ($this->tpl === null) {
            $this->tpl = 'menu';
        }
        $this->tpl .= '.php';
    }

    public function run()
    {
        //Проверка на наличие кэша
        $menu = Yii::$app->cache->get('menu');
        //Данные есть - возвращаем
        if ($menu)
            return $menu;
        //Данных в кэше нет - формируем его
        $this->data = Category::find()->indexBy('id')->asArray()->all();
        $this->tree = $this->getTree();
        $this->menuHtml = $this->getMenuHtml($this->tree);

        //set cache
        Yii::$app->cache->set('menu', $this->menuHtml, 60 * 60);
        return $this->menuHtml;
    }

    //Проход в цикле по массиву и стоит дерево
    protected function getTree()
    {
        $tree = [];
        foreach ($this->data as $id => &$node) {
            if (!$node['parent_id'])
                $tree[$id] = &$node;
            else
                $this->data[$node['parent_id']]['childs'][$node['id']] = &$node;
        }
        return $tree;
    }

    protected function getMenuHtml($tree)
    {
        $str = '';
        foreach ($tree as $category) {
            $str .= $this->catToTemplate($category);
        }
        return $str;
    }

    protected function catToTemplate($category)
    {
        //Буферизация
        ob_start();
        include __DIR__ . '/menu_tpl/' . $this->tpl;
        return ob_get_clean();
    }
}