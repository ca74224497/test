<?php
/**
 * Core.php
 *
 * Ядро приложения (реализовано через паттерн Singleton).
 *
 * @author     ca74224497
 * @copyright  2018 ca74224497@gmail.com
 * @version    1.0
 */

namespace app;

use app\{
  View,
  Router,
  Controller,
  Storage
};

class Core {
  private $_view;
  private $_router;
  private $_storage;
  private $_controller;

  private static $_instance;

  /**
   * Core constructor.
   */
  private function __construct() {}
  private function __clone()     {}
  private function __wakeup()    {}

  public static function app()
  {
    if (empty(self::$_instance)) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function __get($property)
  {
    switch ($property) {
      case 'view':
        return $this->_view;
      case 'router':
        return $this->_router;
      case 'storage':
        return $this->_storage;
      case 'controller':
        return $this->_controller;
    }
  }

  public function run()
  {
    $this->_view       = new View;
    $this->_router     = new Router;
    $this->_storage    = new Storage;
    $this->_controller = new Controller;

    $this->_controller->invokeAction(
      $this->_router->getRequestHandler()
    );
  }
}