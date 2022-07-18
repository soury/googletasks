<?php

namespace soury\googletasks\factories;

use soury\googletasks\helpers\GoogleHelper;
use soury\googletasks\objects\Task;

abstract class TaskFactory
{
    private static function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
       return $response;
    } 

    private static function tokenEpiredResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 401 Token Expired';
        $response['body'] = json_encode(["result" => false, "message" => "Token Expired"]);;
       return $response;
    } 

    public static function getListTaskLists($maxResults = 10)
    {
        $client = GoogleHelper::getClient();
        if(!$client === 'fetchAccessTokenWithRefreshToken') return TaskFactory::tokenEpiredResponse();
        $service = new \Google_Service_Tasks($client);
        $optParams = array(
            'maxResults' => $maxResults,
        );
        
        $results = $service->tasklists->listTasklists($optParams);
        $tasksArray = array();

        foreach ($results->getItems() as $tasklist) {
            $tasksArray[] = new Task([
                'id' => $tasklist->getId(),
                'etag' => $tasklist->getEtag(),
                'title' => $tasklist->getTitle()
            ]);
        }

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($tasksArray);
        return $response;
    }

    public static function deleteEmptyTaskLists() {
        $client = GoogleHelper::getClient();
        if(!$client === 'fetchAccessTokenWithRefreshToken') return TaskFactory::tokenEpiredResponse();
        $service = new \Google_Service_Tasks($client);
        $lists = $service->tasklists->listTasklists(['maxResults' => 0]);
        $results = ["deleted" => []];
        foreach ($lists->getItems() as $list) {
            $tasks = $service->tasks->listTasks($list->getId());
            if(count($tasks->getItems()) == 0) {
                $results["deleted"][] = $list->getTitle();
                $service->tasklists->delete($list->getId());
            }
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($results);
        return $response;
    }

    public static function getListByTitle($title) {
        $client = GoogleHelper::getClient();
        if(!$client === 'fetchAccessTokenWithRefreshToken') return TaskFactory::tokenEpiredResponse();
        $service = new \Google_Service_Tasks($client);
        $results = $service->tasklists->listTasklists(['maxResults'=> 0 ]);
        foreach ($results->getItems() as $tasklist) {
            if(trim($tasklist->getTitle()) == $title) {
                return new Task([
                    'id' => $tasklist->getId(),
                    'etag' => $tasklist->getEtag(),
                    'title' => $tasklist->getTitle()
                ]);
            }
        }
    }

    public static function getListTaskList($listId, $request = [])
    {
        if(!$listId && isset($request['title'])) {
            $list = TaskFactory::getListByTitle($request['title']);
            if(!$list) {
                $response['status_code_header'] = 'HTTP/1.1 409';
                $response['body'] = json_encode(["result" => false, "message" => "Lista non trovata"]);
                return $response;
            }
            $response['status_code_header'] = 'HTTP/1.1 200 OK';
            $response['body'] = json_encode($list);
            return $response;
        }
        $client = GoogleHelper::getClient();
        if(!$client === 'fetchAccessTokenWithRefreshToken') return TaskFactory::tokenEpiredResponse();
        $service = new \Google_Service_Tasks($client);
        $results = $service->tasklists->get($listId);
        $response['body'] = json_encode(new Task([
            'id' => $results->getId(),
            'etag' => $results->getEtag(),
            'title' => $results->getTitle()
        ]));
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        return $response;
    }

    public static function createTaskList($data = [])
    {
        $client = GoogleHelper::getClient();
        if(!$client === 'fetchAccessTokenWithRefreshToken') return TaskFactory::tokenEpiredResponse();
        $service = new \Google_Service_Tasks($client);
        $postData = new \Google_Service_Tasks_TaskList($data);
        $results = $service->tasklists->insert($postData);

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(new Task([
            'id' => $results->getId(),
            'etag' => $results->getEtag(),
            'title' => $results->getTitle()
        ]));
        return $response;
    }

    public static function updateTaskList($listId, $data = [])
    {
        if(!$listId && !isset($data['id']) && isset($data['listTitle'])) {
            $list = TaskFactory::getListByTitle($data['listTitle']);
            if($list) $data['id'] = $list->id;
        }
        $listId = $data['id'];
        if (!$listId) {
            $response['status_code_header'] = 'HTTP/1.1 409';
            $response['body'] = json_encode(["result" => false, "message" => "Lista non trovata"]);
            return $response;
        }
        $client = GoogleHelper::getClient();
        if(!$client === 'fetchAccessTokenWithRefreshToken') return TaskFactory::tokenEpiredResponse();
        $service = new \Google_Service_Tasks($client);
        $listId = $data['id'];
        $postData = new \Google_Service_Tasks_TaskList($data);
        $results = $service->tasklists->update($listId, $postData);

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(new Task([
            'id' => $results->getId(),
            'etag' => $results->getEtag(),
            'title' => $results->getTitle()
        ]));
        return $response;
    }

    public static function deleteTaskList($listId, $request = [])
    {
        if(!$listId && isset($request['title'])) {
            $list = TaskFactory::getListByTitle($request['title']);
            if($list) $listId = $list->id;
        }
        if (!$listId) {
            $response['status_code_header'] = 'HTTP/1.1 409';
            $response['body'] = json_encode(["result" => false, "message" => "Lista non trovata"]);
            return $response;
        }
        $client = GoogleHelper::getClient();
        if(!$client === 'fetchAccessTokenWithRefreshToken') return TaskFactory::tokenEpiredResponse();
        $service = new \Google_Service_Tasks($client);
        $results = $service->tasklists->delete($listId);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($results->getBody());
        return $response;
    }


    public static function getTaskLists($listId, $request = [])
    {
        if(!$listId && isset($request['listTitle'])) {
            $list = TaskFactory::getListByTitle($request['listTitle']);
            if($list) $listId = $list->id;
        }
        if (!$listId) {
            $response['status_code_header'] = 'HTTP/1.1 409';
            $response['body'] = json_encode(["result" => false, "message" => "Lista non trovata"]);
            return $response;
        }
        $client = GoogleHelper::getClient();
        if(!$client === 'fetchAccessTokenWithRefreshToken') return TaskFactory::tokenEpiredResponse();
        $service = new \Google_Service_Tasks($client);
        $results = $service->tasks->listTasks($listId);
        
        $tasksArray = array();
        foreach ($results->getItems() as $task) {
            $tasksArray[] = new Task([
                'id' => $task->id,
                'title' => $task->title,
                'etag' => $task->etag,
                'due' => $task->due,
                'completed' => $task->completed,
                'deleted' => $task->deleted,
                'hidden' => $task->hidden,
                'notes' => $task->notes,
                'status' => $task->status,
                'updated' => $task->updated,
            ]);
        }
        
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($tasksArray);
        return $response;
    }

    public static function getTaskByTitle($listId, $title) {
        $client = GoogleHelper::getClient();
        if(!$client === 'fetchAccessTokenWithRefreshToken') return TaskFactory::tokenEpiredResponse();
        $service = new \Google_Service_Tasks($client);
        $results = $service->tasks->listTasks($listId);
        foreach ($results->getItems() as $task) {
            if(trim($task->getTitle()) == $title) {
                return new Task([
                    'id' => $task->id,
                    'title' => $task->title,
                    'etag' => $task->etag,
                    'due' => $task->due,
                    'completed' => $task->completed,
                    'deleted' => $task->deleted,
                    'hidden' => $task->hidden,
                    'notes' => $task->notes,
                    'status' => $task->status,
                    'updated' => $task->updated,
                ]);
            }
        }
    }

    public static function getTask($listId, $taskId, $request = [])
    {
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        if(!$listId && isset($request['listTitle'])) {
            $list = TaskFactory::getListByTitle($request['listTitle']);
            if($list) $listId = $list->id;
        }
        if (!$listId) {
            $response['status_code_header'] = 'HTTP/1.1 409';
            $response['body'] = json_encode(["result" => false, "message" => "Lista non trovata"]);
            return $response;
        }
        if((!$taskId || strlen($taskId) < 3) && isset($request['title'])) {
            $task = TaskFactory::getTaskByTitle($listId, $request['title']);
            $response['body'] = json_encode($task);
            return $response;
        }
        $client = GoogleHelper::getClient();
        if(!$client === 'fetchAccessTokenWithRefreshToken') return TaskFactory::tokenEpiredResponse();
        $service = new \Google_Service_Tasks($client);
        $results = $service->tasks->get($listId, $taskId);

        $response['body'] = json_encode(new Task([
            'id' => $results->id,
            'title' => $results->title,
            'etag' => $results->etag,
            'due' => $task->due,
            'completed' => $results->completed,
            'deleted' => $results->deleted,
            'hidden' => $results->hidden,
            'notes' => $results->notes,
            'status' => $results->status,
            'updated' => $results->updated,
        ]));
        return $response;
    }

    public static function createTask($listId, $data = [])
    {
        if(!$listId && isset($data['listTitle'])) {
            $list = TaskFactory::getListByTitle($data['listTitle']);
            if($list) $listId = $list->id;
        }
        if (!$listId) {
            $response['status_code_header'] = 'HTTP/1.1 409';
            $response['body'] = json_encode(["result" => false, "message" => "Lista non trovata"]);
            return $response;
        }
        $client = GoogleHelper::getClient();
        if(!$client === 'fetchAccessTokenWithRefreshToken') return TaskFactory::tokenEpiredResponse();
        $service = new \Google_Service_Tasks($client);
        $postData = new \Google_Service_Tasks_Task($data);
        $results = $service->tasks->insert($listId, $postData);

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(new Task([
            'id' => $results->id,
            'title' => $results->title,
            'etag' => $results->etag,
            'due' => $results->due,
            'completed' => $results->completed,
            'deleted' => $results->deleted,
            'hidden' => $results->hidden,
            'notes' => $results->notes,
            'status' => $results->status,
            'updated' => $results->updated,
        ]));
        return $response;
    }

    public static function updateTask($listId, $taskId, $data)
    {
        if(!$listId && isset($data['listTitle'])) {
            $list = TaskFactory::getListByTitle($data['listTitle']);
            if($list) $listId = $list->id;
        }
        if (!$listId) {
            $response['status_code_header'] = 'HTTP/1.1 409';
            $response['body'] = json_encode(["result" => false, "message" => "Lista non trovata"]);
            return $response;
        }
        if(!$taskId && isset($data['taskTitle']) && !isset($data['id'])) {
            $task = TaskFactory::getTaskByTitle($listId, $data['taskTitle']);
            if($task) $data['id'] = $task->id;
        }
        $taskId = $data['id'];
        if (!$taskId) {
            $response['status_code_header'] = 'HTTP/1.1 409';
            $response['body'] = json_encode(["result" => false, "message" => "Task non trovata"]);
            return $response;
        }
        $client = GoogleHelper::getClient();
        if(!$client === 'fetchAccessTokenWithRefreshToken') return TaskFactory::tokenEpiredResponse();
        $service = new \Google_Service_Tasks($client);
        $postData = new \Google_Service_Tasks_Task($data);
        $results = $service->tasks->update($listId, $taskId, $postData);

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(new Task([
            'id' => $results->id,
            'title' => $results->title,
            'etag' => $results->etag,
            'due' => $task->due,
            'completed' => $results->completed,
            'deleted' => $results->deleted,
            'hidden' => $results->hidden,
            'notes' => $results->notes,
            'status' => $results->status,
            'updated' => $results->updated,
        ]));
        return $response;
    }

    public static function deleteTask($listId, $taskId, $request = [])
    {
        if(!$listId && isset($request['listTitle'])) {
            $list = TaskFactory::getListByTitle($request['listTitle']);
            if($list) $listId = $list->id;
        }
        if (!$listId) {
            $response['status_code_header'] = 'HTTP/1.1 409';
            $response['body'] = json_encode(["result" => false, "message" => "Lista non trovata"]);
            return $response;
        }
        if((!$taskId || strlen($taskId) < 5) && isset($request['title'])) {
            $task = TaskFactory::getTaskByTitle($listId, $request['title']);
            if($task) $taskId = $task->id;
        }
        if (!$taskId) {
            $response['status_code_header'] = 'HTTP/1.1 409';
            $response['body'] = json_encode(["result" => false, "message" => "Task non trovata"]);
            return $response;
        }
        $client = GoogleHelper::getClient();
        if(!$client === 'fetchAccessTokenWithRefreshToken') return TaskFactory::tokenEpiredResponse();
        $service = new \Google_Service_Tasks($client);
        $results = $service->tasks->delete($listId, $taskId);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($results);
        return $response;
    }

    public static function clearListTasks($listId, $request)
    {
        if(!$listId && isset($request['listTitle'])) {
            $list = TaskFactory::getListByTitle($request['listTitle']);
            if($list) $listId = $list->id;
        }
        if (!$listId) {
            $response['status_code_header'] = 'HTTP/1.1 409';
            $response['body'] = json_encode(["result" => false, "message" => "Lista non trovata"]);
            return $response;
        }
        $client = GoogleHelper::getClient();
        if(!$client === 'fetchAccessTokenWithRefreshToken') return TaskFactory::tokenEpiredResponse();
        $service = new \Google_Service_Tasks($client);
        $results = $service->tasks->clear($listId);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($results);
        return $response;
    }
}
