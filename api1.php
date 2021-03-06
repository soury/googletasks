<?php
require_once './vendor/autoload.php';
use soury\googletasks\factories\TaskFactory;
use soury\googletasks\helpers\GoogleHelper;

ini_set("log_errors", 1);
ini_set("error_log", "php-error.log");

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
$route = null;

if($lastId == 'task-lists' || $lastId == 'tasks') {
  $route = $lastId;
  $lastId = null;
} else {
  if($uri[count($uri) - 2] !== 'task-lists' && $uri[count($uri) - 2] !== 'tasks') {
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
try{
    switch ($route) {
        case 'task-lists':
        switch ($requestMethod) {
            case 'GET':
                $postData = count($postData) > 0 ? $postData : $_GET;
                if($postData && isset($postData['deleteList'])) {
                    $response = TaskFactory::deleteTaskList(null, $postData);
                } else if($postData && isset($postData['title'])) {
                    $response = TaskFactory::getListTaskList(null, $postData);
                } else {
                    $response = TaskFactory::getListTaskLists(0, $postData);
                }
                break;
            case 'POST':
                $postData = count($postData) > 0 ? $postData : $_POST;
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
                $postData = count($postData) > 0 ? $postData : $_GET;
                if($postData && isset($postData['deleteTask'])) {
                    $response = TaskFactory::deleteTask(null, null, $postData);
                } else if($postData && isset($postData['clearTask'])) {
                    $response = TaskFactory::clearListTasks(null, $postData);
                } else if ($postData && isset($postData['title'])) {
                    $postData['listTitle'] = $postData['title'];
                    $response = TaskFactory::getTask(null, null, $postData);
                } else {
                    $response = TaskFactory::getTaskLists(null, $postData);
                }
                break;
            case 'POST':
                $postData = count($postData) > 0 ? $postData : $_POST;
                if($postData && isset($postData['updateTask'])) {
                    $response = TaskFactory::updateTask(null, null, $postData);
                } else {
                    $response = TaskFactory::createTask(null, $postData);
                }
                break;
            }
        break;
    }
} catch (\Throwable $th) {
    if($th->getCode() == 401) {
        $client = GoogleHelper::getClient();
        $authUrl = $client->createAuthUrl();
        $to      = 'info@gmail.com';
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
                    'From: taskAPI@gmail.com'       . "\r\n" .
                    'Reply-To: taskAPI@gmail.com' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();
        mail($to, $subject, $message, $headers);
        $response = TaskFactory::tokenEpiredResponse();
    }
}
header($response['status_code_header']);
if ($response['body']) {
    echo $response['body'];
    exit;
}
  

?>