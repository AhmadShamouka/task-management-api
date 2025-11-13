<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Task;
use App\Notifications\TaskCommentAdded;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    /**
     * Store a new comment on a task.
     *
     * POST /api/tasks/{task}/comments
     */
    public function store(StoreCommentRequest $request, Task $task)
    {
        $user = $request->user();
        $data = $request->validated();

        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            // Store file in s3 under "comments/" directory
            $attachmentPath = $request->file('attachment')
                ->store('comments', 's3');

            // Make sure it is publicly accessible if needed
            Storage::disk('s3')->setVisibility($attachmentPath, 'public');
        }

        $comment = Comment::create([
            'task_id'         => $task->id,
            'user_id'         => $user->id,
            'body'            => $data['body'],
            'attachment_path' => $attachmentPath,
        ]);

    
            $task->user->notify(new TaskCommentAdded($task, $comment));
        

        return new CommentResource(
            $comment->load(['user', 'task'])
        );
    }
}
