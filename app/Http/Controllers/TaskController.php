<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Folder;
use App\Task;
use App\Http\Requests\CreateTask;
use App\Http\Requests\EditTask;
use Illuminate\Http\Request;

class TaskController extends Controller
{
     public function index(int $id)
     {
         // ★ ユーザーのフォルダを取得する
        $folders = Auth::user()->folders()->get();

         // 選ばれたフォルダを取得する
         $current_folder = Folder::find($id);
         if (is_null($current_folder)) {
            abort(404);
        }

         // 選ばれたフォルダに紐づくタスクを取得する
         $tasks = $current_folder->tasks()->get();
         
         return view('tasks/index', [
        'folders' => $folders,
        'current_folder_id' => $current_folder->id,
        'tasks' => $tasks,
        ]);
    }
    /**
     * GET /folders/{id}/tasks/create
     */
    public function showCreateForm(int $id)
    {
        return view('tasks/create', [
        'folder_id' => $id
    ]);
    }
    
    public function create(int $id, CreateTask $request)
    {
        $current_folder = Folder::find($id);

        $task = new Task();
        $task->title = $request->title;
        $task->due_date = $request->due_date;

        $current_folder->tasks()->save($task);

        return redirect()->route('tasks.index', [
            'id' => $current_folder->id,
        ]);
    }

    /**
     * GET /folders/{id}/tasks/{task_id}/edit
     */
    public function showEditForm(int $id, int $task_id)
    {
        $task = Task::find($task_id);

        return view('tasks/edit', [
        'task' => $task,
    ]);
    }

    public function edit(int $id, int $task_id, EditTask $request)
    {
    // 1
    $task = Task::find($task_id);

    // 2
    $task->title = $request->title;
    $task->status = $request->status;
    $task->due_date = $request->due_date;
    $task->save();

    // 3
    return redirect()->route('tasks.index', [
        'id' => $task->folder_id,
    ]);
    }

}
