<?php
/**
 * Config.php
 *
 * Файл конфигурации приложения.
 *
 * @author     ca74224497
 * @copyright  2018 ca74224497@gmail.com
 * @version    1.0
 */

namespace app;

class Config
{
  /**
   * Учетные записи пользователей.
   * Формат: %user% => %password%
   */
  const USERS = [
    'admin' => '123'
  ];

  /**
   * Описание маршрутов.
   */
  const ROUTES = [
    '/' => [
      'controller' => 'task',
      'action' => 'list'
    ],
    'task/list' => [
      'controller' => 'task',
      'action' => 'list'
    ],
    'task/add' => [
      'controller' => 'task',
      'action' => 'add'
    ],
    'task/edit' => [
      'controller' => 'task',
      'action' => 'edit'
    ],
    'user/login' => [
      'controller' => 'user',
      'action' => 'login'
    ]
  ];

  /**
   * Константы приложения.
   */
  const APP_DIR          = 'app';
  const VIEWS_EXT        = 'html';
  const VIEWS_DIR        = 'views';
  const STORAGE_DIR      = 'data';
  const CONTROLLERS_DIR  = 'controllers';
  const CONTROLLERS_PATH = __DIR__ .
                           DIRECTORY_SEPARATOR . Config::CONTROLLERS_DIR .
                           DIRECTORY_SEPARATOR;
  const VIEWS_PATH = __DIR__ .
                     DIRECTORY_SEPARATOR . Config::VIEWS_DIR .
                     DIRECTORY_SEPARATOR;
  const STORAGE_FILE = __DIR__ .
                       DIRECTORY_SEPARATOR . '..' .
                       DIRECTORY_SEPARATOR . Config::STORAGE_DIR .
                       DIRECTORY_SEPARATOR . 'storage.json';
}