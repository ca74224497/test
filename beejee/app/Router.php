<?php
/**
 * Router.php
 *
 * Router приложения (менеджеров запросов к приложению).
 *
 * Вообще, можно было использовать уже готовый роутер, как например: FastRoute (https://github.com/nikic/FastRoute),
 * но реализуем свой (ультимативно простой, удовлетворяющий требованиям тестового задания).
 *
 * @author     ca74224497
 * @copyright  2018 ca74224497@gmail.com
 * @version    1.0
 */

namespace app;

class Router
{
  /**
   * @var Обработчик маршрута.
   */
  protected $_routeHandler;

  /**
   * Router constructor.
   */
  function __construct()
  {
    /**
     * Инициализация роутера.
     */
    $this->_init();
  }

  protected function _init()
  {
    if (!count(Config::ROUTES)) {
      throw new \Error('Нет конечных точек сопоставления (маршруты не заданы)!');
    }

    $request = $_SERVER['REQUEST_URI'];
    if ($request !== '/') {
      $request = trim($_SERVER['REQUEST_URI'], '/');
    }

    foreach (Config::ROUTES as $k => $v) {
      if ($request === $k) {
        $this->_routeHandler = $v;
        break;
      }
    }

    if (empty($this->_routeHandler)) {
      throw new \Error('Ошибка маршрутизации!');
    }
  }

  /**
   * @return array Возвращает обработчика запроса.
   */
  public function getRequestHandler()
  {
    return $this->_routeHandler;
  }
}