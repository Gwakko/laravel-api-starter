<?php

namespace App\Http\Controllers;

use App\Events\TaskCompleted;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function index(Project $project): JsonResponse
    {
        $tasks = $project->tasks()
            ->when(request('status'), fn ($q, $s) => $q->where('status', $s))
            ->when(request('priority'), fn ($q, $p) => $q->where('priority', $p))
            ->latest()
            ->paginate(20);

        return response()->json($tasks);
    }

    public function store(StoreTaskRequest $request, Project $project): JsonResponse
    {
        $task = $project->tasks()->create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        return response()->json($task, 201);
    }

    public function show(Project $project, Task $task): JsonResponse
    {
        return response()->json($task);
    }

    public function update(UpdateTaskRequest $request, Project $project, Task $task): JsonResponse
    {
        $task->update($request->validated());

        if ($task->wasChanged('status') && $task->status === 'done') {
            $task->markCompleted();
            TaskCompleted::dispatch($task);
        }

        return response()->json($task->fresh());
    }

    public function destroy(Project $project, Task $task): JsonResponse
    {
        $task->delete();

        return response()->json(null, 204);
    }
}
