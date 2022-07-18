<?php
ini_set('max_execution_time', 0);
ini_set("log_errors", 1);
ini_set("error_log", "php-error.log");

require_once './vendor/autoload.php';
use soury\googletasks\helpers\GoogleHelper;
$client = GoogleHelper::getClient();
use soury\googletasks\factories\TaskFactory;

$method = "";
if(isset($_GET['method'])) $method = $_GET['method'];
$response = null;
try{
  switch ($method) {
    case 'Liste':
      $response = TaskFactory::getListTaskLists(0);
      break;
    case 'Lista':
      $response = TaskFactory::getListTaskList($_GET['id_lista']);
      break;
    case 'CreaLista':
      $postData = $_GET;
      unset($postData['method']);
      $response = TaskFactory::createTaskList($postData);
      break;
    case 'AggiornaLista':
      $postData = $_GET;
      $idLista = $postData['id'];
      unset($postData['method']);
      $response = TaskFactory::updateTaskList($idLista, $postData);
      break;
    case 'EliminaLista':
      $response = TaskFactory::deleteTaskList($_GET['id_lista']);
      break;
    case 'PuliziaListe':
      $response = TaskFactory::deleteEmptyTaskLists();
      break;
    case 'ListaTasks':
      $response = TaskFactory::getTask($_GET['id_lista']);
      break;
    case 'Task':
      $response = TaskFactory::getTask($_GET['id_lista'], $_GET['id_task']);
      break;
    case 'CreaTask':
      $postData = $_GET;
      $idLista = $postData['id_lista'];
      unset($postData['id_lista']);
      unset($postData['method']);
      $response = TaskFactory::createTask($idLista, $postData);
      break;
    case 'AggiornaTask':
      $postData = $_GET;
      $idLista = $postData['id_lista'];
      unset($postData['id_lista']);
      unset($postData['method']);
      $response = TaskFactory::updateTask($idLista, $_GET['id'], $postData);
      break;
    case 'EliminaTask':
      $response = TaskFactory::deleteTask($_GET['id_lista'], $_GET['id_task']);
      break;
    case 'PulisciLista':
      $response = TaskFactory::clearListTasks($_GET['id_lista']);
      break;
  }
} catch (\Throwable $th) {
  if($th->getCode() == 401) {
    $client = GoogleHelper::getClient();
    $authUrl = $client->createAuthUrl();
    $to      = 'taskAPI@ma-ced.it';
    $subject = 'Google task API - Token expired';
    $message = '
        <html>
            <head>
                <title>Google task API - Token expired</title>
            </head>
            <body>
                <p>You can Authenticate <a href="'.$authUrl.'">here</a></p>
            </body>
        </html>
    ';
    $headers = 'MIME-Version: 1.0'       . "\r\n" .
                'Content-type: text/html; charset=iso-8859-1'       . "\r\n" .
                'From: taskAPI@ma-ced.it'       . "\r\n" .
                'Reply-To: taskAPI@ma-ced.it' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
    mail($to, $subject, $message, $headers);
    $response = TaskFactory::tokenEpiredResponse();
  }
}
if ($method && $response['body']) {
    header($response['status_code_header']);
    echo $response['body'];
    exit;
}
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
    <h2>google tasks API api.php</h2>
    <ul onclick="showHide(event)" id="menuTop">
      <li class="m">Task Lists #task-lists
        <ul>
          <li class="m">List Task Lists #[GET] getListTaskLists()</li>
          <li class="m">Get Task List #[GET] getListTaskList($listId)
            <ul href="././index.php/lista">
              <div class="m">listId: String</div>
            </ul>
          </li>
          <li class="m">Create Task List #[POST] createTaskList($postData = array())
            <ul>
              <div class="m">
                postData: array() (example: [
                  "title" => "task list title"
                ])
              </div>
            </ul>
          </li>
          <li class="m">Update Task List #[PATCH][PUT] updateTaskList($listId, $postData = array())
            <ul>
              <div class="m">
                listId: String <br>
                postData: getListTaskList (must also contain the id of the original object)
              </div>
            </ul>
          </li>
          <li class="m">Delete Task Lists #[DELETE] deleteTaskList($listId)
            <ul>
              <div class="m">listId: String</div>
            </ul>
          </li>
        </ul>
      </li>
      <li class="m">Tasks #tasks
        <ul>
          <li class="m">List Task #[GET] getTaskLists($listId)
            <ul>
              <div class="m">
                listId: String
              </div>
            </ul>
          </li>
          <li class="m">Get Task #[GET] getTask($listId, $taskId)
            <ul>
              <div class="m">
                listId: String <br>
                taskId: String <br>
              </div>
            </ul>
          </li>
          <li class="m">Create Task #[POST] createTask($listId, $data = array())
            <ul>
              <div class="m">
                listId: String <br>
                postData: getTask (must also contain the id of the original object)
              </div>
            </ul>
          </li>
          <li class="m">Update Task #[PATCH][PUT] updateTask($listId, $taskId, $data = array())
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
          <li class="m">Delete Task #[DELETE] deleteTask($listId, $taskId)
            <ul>
              <div class="m">
                listId: String <br>
                taskId: String
              </div>
            </ul>
          </li>
          <li class="m">Clear Tasks #[DELETE] clearListTasks($listId)
            <ul>
              <div class="m">
                listId: String
              </div>
            </ul>
          </li>
        </ul>
      </li>
    </ul>
    <br />
    <h2>google tasks Web ./index.php</h2>
    <ul onclick="showHide(event)" id="menuTop">
      <li class="m">Task Lists # ./index.php?
        <ul>
          <li class="m">List Task Lists # ./index.php?method=Liste</li>
          <li class="m">Get Task List # ./index.php?method=Lista&id_lista=listId
          </li>
          <li class="m">Create Task List # ./index.php?method=CreaLista&title=titolo
          </li>
          <li class="m">Update Task List # ./index.php?method=AggiornaLista&id=listId&title=titolo
          </li>
          <li class="m">Delete Task Lists # ./index.php?method=EliminaLista&id_lista=listId
          </li>
        </ul>
      </li>
      <li class="m">Tasks # ./index.php?
        <ul>
          <li class="m">List Task # ./index.php?method=ListaTasks&id_lista=listId
          </li>
          <li class="m">Get Task # ./index.php?method=Task&id_lista=listId&id_task=idTask
          </li>
          <li class="m">Create Task # ./index.php?method=CreaTask&id_lista=listId&title=titolo& notes=note
          </li>
          <li class="m">Update Task # ./index.php?method=AggiornaTask&id_lista=listId&id=idTask&title=titolo& notes=note
          </li>
          <li class="m">Delete Task # ./index.php?method=EliminaTask&id_lista=listId&id_task=idTask
          </li>
          <li class="m">Clear Tasks # ./index.php?method=PulisciLista&id_lista=listId
          </li>
        </ul>
      </li>
    </ul>
    <!--form method="POST" action="././index.php">
      <label for="fname">First name:</label><br>
      <input type="text" id="fname" name="fname" value="John"><br>
      <label for="lname">Last name:</label><br>
      <input type="text" id="lname" name="lname" value="Doe"><br><br>
      <input type="submit" value="Submit">
    </form--> 
  </body>
</html>
