<?php
require_once './vendor/autoload.php';
ini_set("log_errors", 1);
ini_set("error_log", "php-error.log");
use soury\googletasks\factories\TaskFactory;
use soury\googletasks\helpers\GoogleHelper;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");

@header('Access-Control-Allow-Origin: *'); 
@header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');

header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

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
$postData = (array) json_decode(file_get_contents('php://input'), TRUE);
try {
  switch ($route) {
    case 'task-lists':
      switch ($requestMethod) {
        case 'GET':
          $response = TaskFactory::getListTaskLists(0, $postData);
          break;
        case 'POST':
          $response = TaskFactory::createTaskList($postData);
          break;
        case 'PUT':
        case 'PATCH':
          $postData = (array) json_decode(file_get_contents('php://input'), TRUE);
          $response = TaskFactory::updateTaskList($firstId, $postData);
          break;
        case 'DELETE':
          $response = TaskFactory::deleteTaskList($firstId, $postData);
          break;
      }
      break;
    case 'tasks':
      switch ($requestMethod) {
        case 'GET':
          if($lastId) {
            $response = TaskFactory::getTask($firstId, $lastId, $postData);
          } else {
            $response = TaskFactory::getTaskLists($firstId, $postData);
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
            $response = TaskFactory::deleteTask($firstId, $lastId, $postData);
          } else {
            $response = TaskFactory::clearListTasks($firstId, $postData);
          }
          break;
      }
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
    mail("sourazou@hotmail.com", $subject, '<p>'.($th->getMessage()).'</p><div>stack error: '.$th.'</div>', $headers);
    throw $th;
    $response = TaskFactory::tokenEpiredResponse();
  } else {
    $response['status_code_header'] = 'HTTP/1.1 '.$th->getCode();
    $response['body'] = json_encode(["result" => false, "message" => $th->getMessage()]);
  }
}

header($response['status_code_header']);
if ($response['body']) {
    echo $response['body'];
    exit;
}

//https://developers.google.com/resources/api-libraries/documentation/tasks/v1/php/latest/index.html
?>