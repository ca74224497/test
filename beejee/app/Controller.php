<?php
/**
 * Controller.php
 *
 * Базовый контроллер приложения.
 *
 * @author     ca74224497
 * @copyright  2018 ca74224497@gmail.com
 * @version    1.0
 */

namespace app;

class Controller
{
  /**
   * Вызываем действие указанного контроллера.
   * @param array $handler
   */
  public function invokeAction(array $handler = [])
  {
    if (!isset($handler['controller'], $handler['action'])) {
      throw new \Error('Недопустимые входные данные для обработчика запроса!');
    }

    $controllerFile = Config::CONTROLLERS_PATH .
      ucfirst($handler['controller']) . 'Controller.php';

    $className = pathinfo($controllerFile, PATHINFO_FILENAME);

    if (!file_exists($controllerFile)) {
      throw new \Error("Не удалось найти контроллер {$className}!");
    }

    require_once($controllerFile);

    $classPath = Config::APP_DIR         . '\\' .
                 Config::CONTROLLERS_DIR . '\\' .
                 $className;

    if (!class_exists($classPath)) {
      throw new \Error("Не найден класс контроллера {$className}!");
    }

    $methodName = 'action' . ucfirst($handler['action']);

    if (!method_exists($classPath, $methodName)) {
      throw new \Error("Не найдено действие {$handler['action']}!");
    }

    $controllerInstance = new $classPath;
    $controllerInstance->$methodName();
  }
}