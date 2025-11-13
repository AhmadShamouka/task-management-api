<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // For now allow any authenticated user
        // (route is already behind auth:sanctum)
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:5000'],
            'attachment' => ['sometimes', 'file', 'mimes:jpg,png,pdf,doc,docx', 'max:2048'],
        ];
    }
}
