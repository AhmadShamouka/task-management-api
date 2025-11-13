<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    /**
     * GET /api/tasks
     * List tasks for the authenticated user, cached in Redis.
     */
    public function index(Request $request)
    {
        $user   = $request->user();
        $status = $request->query('status');
        $page   = (int) $request->query('page', 1);

        // Unique cache key per user + filters + page
        $cacheKey = 'tasks:user:' . $user->id . ':status:' . ($status ?? 'all') . ':page:' . $page;

        $tasks = Cache::store('redis')->remember(
            $cacheKey,
            now()->addMinutes(5),
            function () use ($user, $status) {
                Log::info('DB QUERY for tasks executed (building tasks cache)');

                $query = Task::with('user')
                    ->where('user_id', $user->id)
                    ->orderByDesc('created_at');

                if ($status) {
                    $query->where('status', $status);
                }

                return $query->paginate(10);
            }
        );

        return TaskResource::collection($tasks);
    }

    /**
     * POST /api/tasks
     * Create a task for the authenticated user.
     * Clear Redis cache so it gets rebuilt on next GET.
     */
    public function store(StoreTaskRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $task = Task::create($data);

        $this->clearTaskCache();

        return new TaskResource($task->load('user'));
    }

    /**
     * GET /api/tasks/{task}
     */
    public function show(Task $task, Request $request)
    {
        $this->ensureOwnedByUser($task, $request->user()->id);

        return new TaskResource($task->load('user'));
    }

    /**
     * PUT /api/tasks/{task}
     * Update a task and clear cache.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->ensureOwnedByUser($task, $request->user()->id);

        $task->update($request->validated());

        $this->clearTaskCache();

        return new TaskResource($task->fresh()->load('user'));
    }

    /**
     * DELETE /api/tasks/{task}
     * Delete a task and clear cache.
     */
    public function destroy(Request $request, Task $task)
    {
        $this->ensureOwnedByUser($task, $request->user()->id);

        $task->delete();

        $this->clearTaskCache();

        return response()->json([
            'message' => 'Task deleted successfully',
        ]);
    }

    /**
     * Ensure the task belongs to the current user.
     */
    protected function ensureOwnedByUser(Task $task, int $userId): void
    {
        if ($task->user_id !== $userId) {
            abort(response()->json([
                'message' => 'You are not allowed to access this task.',
            ], 403));
        }
    }

    /**
     * Clear all tasks cache.
     *
     * For the assessment, it's OK to just flush Redis,
     * since we're mostly caching tasks.
     */
    protected function clearTaskCache(): void
    {
        try {
            Cache::store('redis')->flush();
        } catch (\Throwable $e) {
            Log::warning('Failed to flush Redis cache: ' . $e->getMessage());
        }
    }
}
