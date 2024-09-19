<?php

namespace App\Http\Requests;

use App\Enums\ReactionType;
use Illuminate\Foundation\Http\FormRequest;

class StoreReactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'post_id' => 'nullable|exists:posts,id|required_without:comment_id',
            'comment_id' => 'nullable|exists:comments,id|required_without:post_id',
            'type' => 'required|string|in:'.implode(',', ReactionType::values()),
        ];
    }

    public function messages(): array
    {
        return [
            'post_id.required_without' => 'Either post_id or comment_id must be provided.',
            'comment_id.required_without' => 'Either post_id or comment_id must be provided.',
        ];
    }
}
