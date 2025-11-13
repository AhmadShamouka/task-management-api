<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray($request): array
    {
        $attachmentUrl = null;
        if ($this->attachment_path) {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $s3 */
            $s3 = Storage::disk('s3');
            $attachmentUrl = $s3->url($this->attachment_path);
        }

        return [
            'id'        => $this->id,
            'body'      => $this->body,
            'task_id'   => $this->task_id,
            'user'      => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ],
            'attachment_url' => $attachmentUrl,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
