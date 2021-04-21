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

    public static function getListTaskLists($maxResults = 10)
    {
        $client = GoogleHelper::getClient();
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

    public static function getListTaskList($listId)
    {
        $client = GoogleHelper::getClient();
        $service = new \Google_Service_Tasks($client);
        $results = $service->tasklists->get($listId);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(new Task([
            'id' => $results->getId(),
            'etag' => $results->getEtag(),
            'title' => $results->getTitle()
        ]));
        return $response;
    }

    public static function createTaskList($data = [])
    {
        $client = GoogleHelper::getClient();
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
        $client = GoogleHelper::getClient();
        $service = new \Google_Service_Tasks($client);
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

    public static function deleteTaskList($listId)
    {
        $client = GoogleHelper::getClient();
        $service = new \Google_Service_Tasks($client);
        $results = $service->tasklists->delete($listId);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($results->getBody());
        return $response;
    }


    public static function getTaskLists($listId)
    {
        $client = GoogleHelper::getClient();
        $service = new \Google_Service_Tasks($client);
        $results = $service->tasks->listTasks($listId);
        $tasksArray = array();
        foreach ($results->getItems() as $task) {
            $tasksArray[] = new Task([
                'id' => $task->id,
                'title' => $task->title,
                'etag' => $task->etag,
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

    public static function getTask($listId, $taskId)
    {
        $client = GoogleHelper::getClient();
        $service = new \Google_Service_Tasks($client);
        $results = $service->tasks->get($listId, $taskId);

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(new Task([
            'id' => $results->id,
            'title' => $results->title,
            'etag' => $results->etag,
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
        $client = GoogleHelper::getClient();
        $service = new \Google_Service_Tasks($client);
        $postData = new \Google_Service_Tasks_Task($data);
        $results = $service->tasks->insert($listId, $postData);

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(new Task([
            'id' => $results->id,
            'title' => $results->title,
            'etag' => $results->etag,
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
        $client = GoogleHelper::getClient();
        $service = new \Google_Service_Tasks($client);
        $postData = new \Google_Service_Tasks_Task($data);
        $results = $service->tasks->update($listId, $taskId, $postData);

        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode(new Task([
            'id' => $results->id,
            'title' => $results->title,
            'etag' => $results->etag,
            'completed' => $results->completed,
            'deleted' => $results->deleted,
            'hidden' => $results->hidden,
            'notes' => $results->notes,
            'status' => $results->status,
            'updated' => $results->updated,
        ]));
        return $response;
    }

    public static function deleteTask($listId, $taskId)
    {
        $client = GoogleHelper::getClient();
        $service = new \Google_Service_Tasks($client);
        $results = $service->tasks->delete($listId, $taskId);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($tasksArray);
        return $response;
    }

    public static function clearListTasks($listId)
    {
        $client = GoogleHelper::getClient();
        $service = new \Google_Service_Tasks($client);
        $results = $service->tasks->clear($listId);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($tasksArray);
        return $response;
    }
}
