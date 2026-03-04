<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'comment' => ['required', 'string', 'min:1', 'max:2000'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'comment.required' => 'Comment is required',
            'comment.min' => 'Comment must be at least 1 character',
            'comment.max' => 'Comment cannot exceed 2000 characters',
        ];
    }
}
