<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskTypeRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\TaskTypes;
use Illuminate\Http\Request;

class TaskTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Get all task types
        $taskTypes = TaskTypes::all();

        // Return task types index
        return view('task-types.index', compact('taskTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Return form
        return view('task-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskTypeRequest $request): RedirectResponse
    {
        // Validate the request
        $task = $request->validated();

        // Create the task
        $task = TaskTypes::create($task);

        return redirect()->route('task-types.index')
            ->with('success', 'Task created successfully.');
    }
}
