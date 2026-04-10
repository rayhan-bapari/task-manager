<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $tasks = Task::query()
            ->latest()
            ->paginate(10);
        $users = $this->userLookup();
        $stats = $this->taskStats();

        return view('pages.tasks.index', compact('tasks', 'users', 'stats'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $task = Task::query()->create($this->validatedTaskData($request));

        return response()->json([
            'status' => 'success',
            'message' => 'Task created successfully.',
            'task' => [
                'id' => $task->id,
                'html' => $this->renderTaskCard($task),
            ],
            'stats' => $this->taskStats(),
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $task->update($this->validatedTaskData($request));

        return response()->json([
            'status' => 'success',
            'message' => 'Task updated successfully.',
            'task' => [
                'id' => $task->id,
                'html' => $this->renderTaskCard($task->fresh()),
            ],
            'stats' => $this->taskStats(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        $taskId = $task->id;
        $task->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Task deleted successfully.',
            'task' => [
                'id' => $taskId,
            ],
            'stats' => $this->taskStats(),
        ]);
    }

    private function validatedTaskData(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:pending,in_progress,completed'],
            'priority' => ['required', 'in:low,medium,high'],
            'due_date' => ['nullable', 'date'],
            'assigned_users' => ['nullable', 'array'],
            'assigned_users.*' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $validated['assigned_users'] = collect($validated['assigned_users'] ?? [])
            ->filter()
            ->map(static fn (mixed $userId): int => (int) $userId)
            ->values()
            ->all();

        return $validated;
    }

    private function renderTaskCard(Task $task): string
    {
        return (string) view('pages.tasks.partials.task-card', [
            'task' => $task,
            'users' => $this->userLookup(),
        ])->render();
    }

    private function userLookup(): Collection
    {
        return User::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get()
            ->keyBy('id');
    }

    /**
     * @return array{total:int,in_progress:int,completed:int,progress_percentage:int}
     */
    private function taskStats(): array
    {
        $total = Task::query()->count();
        $inProgress = Task::query()
            ->where('status', 'in_progress')
            ->count();
        $completed = Task::query()
            ->where('status', 'completed')
            ->count();

        return [
            'total' => $total,
            'in_progress' => $inProgress,
            'completed' => $completed,
            'progress_percentage' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
        ];
    }
}
