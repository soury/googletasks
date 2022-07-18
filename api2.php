<?php
require_once './vendor/autoload.php';
use soury\googletasks\factories\TaskFactory;

if (isset($_GET['title']))
{
    echo $_GET['title'];
    $response = TaskFactory::getListTaskLists(0, $_GET);
    if ($response['body']) {
        echo $response['body'];
        exit;
    }
}



/*
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
*/
/*
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
$requestMethod = $_SERVER["REQUEST_METHOD"];
$firstId = null;
$lastId = $uri[count($uri) - 1];
$route = null;

if($lastId == 'task-lists' || $lastId == 'tasks') {
  $route = $lastId;
  $lastId = null;
} else {
  if($uri[count($uri) - 2] !== 'task-lists' && $uri[count($uri) - 2] !== 'tasks') {
    $firstId = $uri[count($uri) - 2];
    $route = $uri[count($uri) - 3];
  } else {
    $route = $uri[count($uri) - 2];
    $firstId = $lastId;
    $lastId = null;
  }
}

if ($route !== 'task-lists' && $route !== 'tasks') {
    header("HTTP/1.1 404 Not Found");
    exit();
}

$postData = (array) json_decode(file_get_contents('php://input'), TRUE);

header($response['status_code_header']);
$response['body'] = json_encode($postData);
if ($response['body']) {
    echo $response['body'];
    exit;
}

$response = null;


/*
switch ($route) {
    case 'task-lists':
    
       switch ($requestMethod) {        
        case 'GET':
            if($postData && isset($postData['deleteList'])) {
                $response = TaskFactory::deleteTaskList(null, $postData);
            } else if($postData && isset($postData['title'])) {
                echo "siamo  nel caso di ricerca per titolo<br>";
                $response = TaskFactory::getListTaskList(null, $postData);
            } else {
                $response = TaskFactory::getListTaskLists(0, $postData);
            }
            break;
        case 'POST':
            if($postData && isset($postData['updateList'])) {
                $response = TaskFactory::updateTaskList(null, $postData);
            } else {
                $response = TaskFactory::createTaskList($postData);
            }
            break;
      }
    
      break;
    case 'tasks':
        switch ($requestMethod) {
        case 'GET':
            if($postData && isset($postData['deleteTask'])) {
                $response = TaskFactory::deleteTask(null, null, $postData);
            } else if($postData && isset($postData['clearTask'])) {
                $response = TaskFactory::clearListTasks(null, $postData);
            } else if ($postData && isset($postData['title'])) {
                $response = TaskFactory::getTask(null, null, $postData);
            } else {
                $response = TaskFactory::getTaskLists(null, $postData);
            }
            break;
        case 'POST':
            if($postData && isset($postData['updateTask'])) {
                $response = TaskFactory::updateTask(null, null, $postData);
            } else {
                $response = TaskFactory::createTask(null, $postData);
            }
            break;
        }
      break;
}
  
header($response['status_code_header']);
if ($response['body']) {
    echo $response['body'];
    exit;
}
  */

?>