<?php

class Application extends Controller {
	
	public static function index() {
        self::render();
    }

    public static function getTasks($id=null) {
        if (empty($id)) {
            self::renderJson(Task::findAll(), new ModelBuilder());
        } else {
            self::renderJson(Task::findById($id), new ModelBuilder());
        }
    }

    public static function postTasks() {
        $task = new Task();
        $task->description = LVC::get()->request->description;
        $task->priority = LVC::get()->request->priority;
        $task->created = strftime('%Y-%m-%d %H:%M:%S');
        $task->done = 'no';
        $task->deleted = 'no';
        self::renderJson($task->save(), new ModelBuilder());
    }

    public static function putTasks($id) {
        $task = Task::findById($id);
        if (is_object($task)) {
            $task->description = LVC::get()->request->description;
            $task->done = LVC::get()->request->done;
            $task->deleted = LVC::get()->request->deleted;
            $task->priority = LVC::get()->request->priority;
            self::renderJson($task->save(), new ModelBuilder());
        } else {
            self::renderJson(array("result" => "error"));
        }
    }

    public static function deleteTasks($id) {
        if (Task::findById($id)->delete()) {
            self::renderJson(array('result' => 'ok'));
        } else {
            self::renderJson(array('result' => 'error'));
        }
    }

}