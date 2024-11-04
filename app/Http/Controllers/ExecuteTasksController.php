<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Studio\Totem\Task;
use Illuminate\Support\Facades\File;

class ExecuteTasksController extends Controller
{
    public function index(): Response
    {
        File::put(storage_path('tasks.json'), Task::findAll()->toJson());

        return response()
            ->download(storage_path('tasks.json'), 'tasks.json')
            ->deleteFileAfterSend(true);
    }
}
