<?php
/**
 * Storage.php
 *
 * Хранилище данных приложения (JSON flat-file storage).
 * Выступает в роли Model.
 *
 * @author     ca74224497
 * @copyright  2018 ca74224497@gmail.com
 * @version    1.0
 */

namespace app;

class Storage
{
  /**
   * Storage constructor.
   */
  function __construct()
  {
    /**
     * Пытаемся создать файл хранилища, если он отсутствует.
     */
    if (!file_exists(Config::STORAGE_FILE)) {
      try {
        file_put_contents(
          Config::STORAGE_FILE, '', LOCK_EX
        );
      } catch (\ErrorException $e) {
        /**
         * Чтобы не отображать лишние сведения пользователю,
         * кидаем исключение типа Error со своим текстом.
         */
        throw new \Error('Нет доступа к файлу хранилища!');
      }
    }
  }

  /**
   * Добавляет задачу в хранилище.
   * @param array $data
   * @return bool
   */
  public function addTask(array $data)
  {
    try {
      $tasks = file_get_contents(Config::STORAGE_FILE);

      if (strlen($tasks)) {
        $tasks = json_decode($tasks, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
          /**
           * Обнуляем данные хранилища, если структура испорчена.
           */
          $tasks = [];
        }
      } else {
        /**
         * Файл пуст, задаем начальное значение.
         */
        $tasks = [];
      }

      /**
       * Статус по умолчанию.
       */
      $data['status'] = 0;

      $tasks[count($tasks) + 1] = $data;

      /**
       * Сохраняем новые данные в хранилище.
       */
      file_put_contents(
        Config::STORAGE_FILE,
        json_encode($tasks, JSON_HEX_APOS | JSON_HEX_QUOT),
        LOCK_EX
      );

      return true;

    } catch (\ErrorException $e) {
      return false;
    }
  }

  /**
   * Получаем список всех задач.
   * @return array
   */
  public function getTasks()
  {
    try {
      $tasks = json_decode(
        file_get_contents(Config::STORAGE_FILE), true
      );

      if (json_last_error() !== JSON_ERROR_NONE) {
        unset($tasks);
      }
    } catch (\ErrorException $e) {
      /**
       * Не удалось получить данные из хранилища.
       */
    }
    return $tasks ?? [];
  }

  /**
   * Редактирование задачи с ID = $id.
   *
   * @param int $id
   * @param string $text
   * @param int $status
   * @return bool
   */
  public function editTask(int $id, string $text, int $status)
  {
    try {
      $tasks = json_decode(
        file_get_contents(Config::STORAGE_FILE), true
      );

      if (json_last_error() === JSON_ERROR_NONE && isset($tasks[$id])) {

        $tasks[$id]['task']   = $text;
        $tasks[$id]['status'] = $status;

        /**
         * Сохраняем измененные данные в хранилище.
         */
        file_put_contents(
          Config::STORAGE_FILE,
          json_encode($tasks, JSON_HEX_APOS | JSON_HEX_QUOT),
          LOCK_EX
        );
      }

      return true;

    } catch (\ErrorException $e) {
      return false;
    }
  }
}