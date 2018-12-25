<?php
/**
 * TaskController.php
 *
 * Контроллер TaskController.
 *
 * @author     ca74224497
 * @copyright  2018 ca74224497@gmail.com
 * @version    1.0
 */

namespace app\controllers;

use app\Controller;
use app\Core;

class TaskController extends Controller
{
  public function actionList()
  {
    $data = Core::app()->storage->getTasks();

    $tmp = [];
    foreach ($data as $k => $v) {
      $tmp[] = $v + ['id' => $k];
    }

    if ($data !== $tmp) {
      $data = $tmp;
    }

    Core::app()->view->render('list', [
      'data'  => json_encode($data, JSON_HEX_APOS | JSON_HEX_QUOT),
      'class' => empty($_SESSION['is_admin']) ? "" : "admin"
    ]);
  }

  public function actionAdd()
  {
    $result = [
      'status'  => 'error',
      'message' => ''
    ];

    if (!empty($_POST['user'])  &&
        !empty($_POST['email']) &&
        !empty($_POST['task'])) {
      $data = [
        'user'  => $_POST['user'],
        'email' => $_POST['email'],
        'task'  => $_POST['task']
      ];
      if (Core::app()->storage->addTask($data)) {
        $result['status'] = 'success';
      } else {
        $result['message'] = 'Не удалось добавить задачу (проблема с хранилищем)!';
      }
    } else {
      $result['message'] = 'Не удалось получить все входные параметры!';
    }

    die(json_encode($result));
  }

  public function actionEdit()
  {
    $result = [
      'status'  => 'error',
      'message' => ''
    ];

    if (isset(
      $_POST['e-id'],
      $_POST['e-task'],
      $_POST['e-status'])        &&
      is_numeric($_POST['e-id']) &&
      is_numeric($_POST['e-status'])) {

      if (Core::app()->storage->editTask(
        $_POST['e-id'],
        $_POST['e-task'],
        $_POST['e-status'])
      ) {
        $result['status'] = 'success';
      } else {
        $result['message'] = 'Не удалось записать данные в хранилище!';
      }
    } else {
      $result['message'] = 'Неверные входные данные!';
    }

    die(json_encode($result));
  }
}