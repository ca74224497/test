<?php
/**
 * UserController.php
 *
 * Контроллер UserController.
 *
 * @author     ca74224497
 * @copyright  2018 ca74224497@gmail.com
 * @version    1.0
 */

namespace app\controllers;

use app\Config;
use app\Controller;

class UserController extends Controller
{
  public function actionLogin()
  {
    $result = [
      'status'  => 'error',
      'message' => ''
    ];

    if (isset($_POST['login'], $_POST['password'])) {
      if (isset(Config::USERS[$_POST['login']])) {
        if (Config::USERS[$_POST['login']] === $_POST['password']) {
          $result['status'] = 'success';
          $_SESSION['is_admin'] = true;
        } else {
          $result['message'] = 'Вы ввели неправильный пароль!';
        }
      } else {
        $result['message'] = 'Не удалось найти пользователя с таким логином!';
      }
    } else {
      $result['message'] = 'Не удалось получить все входные параметры!';
    }

    die(json_encode($result));
  }
}