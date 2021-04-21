<?php
require_once './vendor/autoload.php';

use soury\googletasks\factories\TaskFactory;


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

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

$response = null;

switch ($route) {
  case 'task-lists':
    switch ($requestMethod) {
      case 'GET':
        if($firstId) {
          $response = TaskFactory::getListTaskList($firstId);
        } else {
          $response = TaskFactory::getListTaskLists(0);
        }
        break;
      case 'POST':
        $postData = (array) json_decode(file_get_contents('php://input'), TRUE);
        $response = TaskFactory::createTaskList($postData);
        break;
      case 'PUT':
      case 'PATCH':
        $postData = (array) json_decode(file_get_contents('php://input'), TRUE);
        $response = TaskFactory::updateTaskList($firstId, $postData);
        break;
      case 'DELETE':
        $response = TaskFactory::deleteTaskList($firstId);
        break;
    }
    break;
  case 'tasks':
    switch ($requestMethod) {
      case 'GET':
        if($lastId) {
          $response = TaskFactory::getTask($firstId, $lastId);
        } else {
          $response = TaskFactory::getTaskLists($firstId);
        }
        break;
      case 'POST':
        $postData = (array) json_decode(file_get_contents('php://input'), TRUE);
        $response = TaskFactory::createTask($firstId, $postData);
        break;
      case 'PUT':
      case 'PATCH':
        $postData = (array) json_decode(file_get_contents('php://input'), TRUE);
        $response = TaskFactory::updateTask($firstId, $lastId, $postData);
        break;
      case 'DELETE':
        if($lastId) {
          $response = TaskFactory::deleteTask($firstId, $lastId);
        } else {
          $response = TaskFactory::clearListTasks($firstId);
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

//https://developers.google.com/resources/api-libraries/documentation/tasks/v1/php/latest/index.html
?>