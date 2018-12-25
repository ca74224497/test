/**
 * Список возможных статусов задачи.
 * @type {{"0": string, "1": string}}
 */
var statuses = {
  "0": "Не выполнена",
  "1": "Выполнена"
};

$(document).ready(function() {
  $("form").on("submit", function(e) {
    if (this.checkValidity() !== false) {
      var okMessage;
      var $form = $(this);

      if ($form.hasClass("add-task-form")) {
        okMessage = "Задача успешно добавлена!";
      } else if ($form.hasClass("edit-task-form")) {
        okMessage = "Задача успешно отредактирована!";
      } else {
        okMessage = "Вы успешно авторизованы!";
      }

      sendRequest($form, okMessage);
    }
    this.classList.add("was-validated");

    e.preventDefault();
    e.stopPropagation();
  });

  /**
   * Инициализируем DataTables.
   */
  var html = "";
  var $table = $(".data");
  var $tbody = $table.find("tbody");

  for (var i = 0; i < tasks.length; i++) {
    html += "<tr class='s" + tasks[i]["status"] + "'>" +
            "<td>" + tasks[i]["id"]     + "</td>"  +
            "<td>" + tasks[i]["user"]   + "</td>"  +
            "<td>" + tasks[i]["email"]  + "</td>"  +
            "<td>" + tasks[i]["task"]   + "</td>"  +
            "<td>" + statuses[tasks[i]["status"]]  + " " +
            "<a href='#' onclick='editTask(this)' class='" + setClass + "'>редактировать</a></td>" +
            "</tr>";
  }
  $tbody.html(html);

  $table.DataTable({
    columnDefs: [
      {
        "targets": 3,
        "orderable": false
      }
    ],
    pageLength: 3,
    bLengthChange: false,
    language: {
      "processing": "Подождите...",
      "search": "Поиск:",
      "lengthMenu": "Показать _MENU_ записей",
      "info": "Показаны записи с _START_ по _END_ (всего _TOTAL_ записей)",
      "infoEmpty": "Записи с 0 до 0 из 0 записей",
      "infoFiltered": "(отфильтровано из _MAX_ записей)",
      "infoPostFix": "",
      "loadingRecords": "Загрузка записей...",
      "zeroRecords": "Записи отсутствуют.",
      "emptyTable": "Список задач пуст, но вы всегда можете добавить задачу через панель управления.",
      "paginate": {
        "first": "Первая",
        "previous": "Предыдущая",
        "next": "Следующая",
        "last": "Последняя"
      },
      "aria": {
        "sortAscending": ": активировать для сортировки столбца по возрастанию",
        "sortDescending": ": активировать для сортировки столбца по убыванию"
      }
    }
  });
});

/**
 * AJAX-запрос.
 *
 * @param $form {jQuery|HTMLElement}
 * @param okMessage string Текстовое сообщение при успешном выполнении операции.
 */
function sendRequest($form, okMessage) {
  var $alert  = $form.find(".alert");
  var $submit = $form.find("button[type='submit']");

  $.post($form.attr("action"), $form.serialize())
    .done(function(response) {
      try {
        response = JSON.parse(response);

        if (response.status === "success") {
          $submit.attr("disabled", true);
          $alert
            .removeClass("alert-danger")
            .addClass("alert-success")
            .show()
            .text(okMessage)
            .fadeOut(3000, function() {
              location.reload();
            });
        } else {
          $alert
            .show()
            .text(response.message);
        }

      } catch (e) {
        $alert
          .show()
          .text(response);
      }
    })
    .fail(function() {
      $alert
        .show()
        .text("Не удалось выолнить запрос. Попробуйте еще раз!");
    });
}

/**
 * Редактирование задачи.
 */
function editTask(a) {
  /**
   * Получаем необходимые нам данные записи.
   */
  var row  = $(a).closest("tr");
  var tid  = parseInt(row.find("td:first-child").text());
  var txt  = row.find("td:nth-child(4)").text();
  var stat = row.hasClass("s1") ? 1 : 0;

  /**
   * Вызываемм диалоговое окно редактирования.
   */
  $("#edit-task")
    .modal("show")
    .find("textarea")
    .val(txt)
    .closest(".modal-body")
    .find("select")
    .val(stat)
    .next()
    .val(tid);
}