<?php
require_once './vendor/autoload.php';
use soury\googletasks\helpers\GoogleHelper;
$client = GoogleHelper::getClient();
?>

<!DOCTYPE html>
<html>
  <head>
    <!-- Custom styles. -->
    <style>
      #menuTop,
      #menuTop ul {
        cursor: default;
        list-style-type: circle
      }

      #menuTop .m {
        list-style-type: disc
      }

      #menuTop ul {
        display: none
      }
    </style>
    <script>
      function showHide(ev) {
        ev = ev || window.event
        var elem = (ev.target || ev.srcElement).getElementsByTagName("UL")[0]
        if (!elem) return;
        elem = elem.style;
        elem.display = (elem.display != 'block') ? 'block' : 'none'
      }
    </script>
  </head>

  <body>
    <h2>google tasks API</h2>
    <ul onclick="showHide(event)" id="menuTop">
      <li class="m">Task Lists #task-lists
        <ul>
          <li class="m">List Task Lists #getListTaskLists()
          </li>
          <li class="m">Get Task List #getListTaskList($listId)
            <ul>
              <div class="m">listId: String</div>
            </ul>
          </li>
          <li class="m">Create Task List #createTaskList($postData = array())
            <ul>
              <div class="m">
                postData: array() (example: [
                  "title" => "task list title"
                ])
              </div>
            </ul>
          </li>
          <li class="m">Update Task List #updateTaskList($listId, $postData = array())
            <ul>
              <div class="m">
                listId: String <br>
                postData: getListTaskList (must also contain the id of the original object)
              </div>
            </ul>
          </li>
          <li class="m">Delete Task Lists #deleteTaskList($listId)
            <ul>
              <div class="m">listId: String</div>
            </ul>
          </li>
        </ul>
      </li>

      <li class="m">Tasks #tasks
        <ul>
          <li class="m">List Task #getTaskLists($listId)
            <ul>
              <div class="m">
                listId: String
              </div>
            </ul>
          </li>
          <li class="m">Get Task #getTask($listId, $taskId)
            <ul>
              <div class="m">
                listId: String <br>
                taskId: String <br>
              </div>
            </ul>
          </li>
          <li class="m">Create Task #createTask($listId, $data = array())
            <ul>
              <div class="m">
                listId: String <br>
                postData: getTask (must also contain the id of the original object)
              </div>
            </ul>
          </li>
          <li class="m">Update Task #updateTask($listId, $taskId, $data = array())
            <ul>
              <div class="m">
                listId: String <br>
                taskId: String <br>
                postData: array() (example: [
                    "title" => "task list title",
                    "notes" => "task list notes"
                  ])
              </div>
            </ul>
          </li>
          <li class="m">Delete Task #deleteTask($listId, $taskId)
            <ul>
              <div class="m">
                listId: String <br>
                taskId: String
              </div>
            </ul>
          </li>
          <li class="m">Clear Tasks #clearListTasks($listId)
            <ul>
              <div class="m">
                listId: String
              </div>
            </ul>
          </li>
        </ul>
      </li>
    </ul>
  </body>
</html>
