<?php
/**
 * index.php
 *
 * Точка входа в наше самописное MVC-приолжение.
 *
 * @author     ca74224497
 * @copyright  2018 ca74224497@gmail.com
 * @version    1.0
 */

/**
 * Уровень протоколирования ошибок.
 */
error_reporting(0 /* скрываем ошибки от пользователя */);

/**
 * Преобразуем E_WARNING в исключение типа ErrorException.
 */
set_error_handler(function ($severity, $message, $file, $line) {
  throw new \ErrorException($message, $severity, $severity, $file, $line);
});

/**
 * Ф-ция автозагрузки классов (без нее прищлось бы делать импорты вручную).
 * Также можно использовать автозагрузку PSR-4 через Composer:
 * {
 *   "autoload": {
 *     "psr-4": {"": "src/"}
 *   }
 * }
 */
spl_autoload_register(function($class) {
  $file = str_replace('\\', '/', $class . '.php');
  if (file_exists($file)) {
    require_once($file);
  }
});

session_start();

use app\Core;

// Стартовый код приложения.
//==============================================================================

try {
  /**
   * Запускаем приложение.
   */
  Core::app()->run();

} catch (\Error $e) {
  die('Ошибка приложения: ' . $e->getMessage());
}

//==============================================================================