<?php

class Application extends SecureController {
	
	public static function index() {
        self::render();
    }

    public static function getTasks($id=null) {
        if (empty($id)) {
            self::renderJson(Task::findAll());
        } else {
            self::renderJson(Task::findById($id));
        }
    }

    public static function postTasks() {
        $task = new Task();
        $task->description = App::get()->request->description;
        $task->priority = App::get()->request->priority;
        $task->created = strftime('%Y-%m-%d %H:%M:%S');
        $task->done = 'no';
        $task->deleted = 'no';
        $task->save();
        self::renderJson(array($task));
    }

    public static function putTasks($id) {
        $task = Task::findById($id);
        if (is_object($task)) {
            $task->description = App::get()->request->description;
            $task->done = App::get()->request->done;
            $task->deleted = App::get()->request->deleted;
            $task->priority = App::get()->request->priority;
            self::renderJson($task->save());
        } else {
            self::renderJson(array("result" => "error"));
        }
    }

    public static function deleteTasks($id) {
        Task::findById($id)->delete();
        self::renderJson(array('result' => 'ok'));
    }

}