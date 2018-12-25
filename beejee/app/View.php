<?php
/**
 * View.php
 *
 * Класс для работы с представлениями приложения.
 *
 * @author     ca74224497
 * @copyright  2018 ca74224497@gmail.com
 * @version    1.0
 */

namespace app;

class View
{
  /**
   * Отображение представления.
   *
   * @param string $view Имя представления.
   * @param array $params Параметры шаблона.
   * @param string $controller Имя контроллера.
   * @param bool $show Вывести на экран или вернуть содержимое?
   * @return string | void
   */
  public function render(string $view, array $params = [], string $controller = null, bool $show = true)
  {
    if (!is_string($view) || !strlen($view)) {
      throw new \Error('Недопустимое имя представления!');
    }

    if (!is_string($controller) || !strlen($controller)) {
      // Вычисляем текущий контроллер запроса.
      $controller = Core::app()->router->getRequestHandler()['controller'];
    }

    $viewFile = Config::VIEWS_PATH  . $controller .
                DIRECTORY_SEPARATOR . $view . '.' .
                Config::VIEWS_EXT;

    if (!file_exists($viewFile)) {
      throw new \Error('Не удалось найти файл представления!');
    }

    $content = file_get_contents($viewFile);

    // Подставляем параметры в шаблон.
    if (is_array($params) && count($params)) {
      foreach ($params as $k => $v) {
        $content = str_replace("%{$k}%", $v, $content);
      }
    }

    if ($show) {
      print($content);
    } else {
      return $content;
    }
  }
}