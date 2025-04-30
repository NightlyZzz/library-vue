<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class StoreNoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'book.google_id' => ['required', 'string'],
            'book.title' => ['required', 'string', 'max:255'],
            'book.author' => ['nullable', 'string', 'max:255'],
            'book.cover_url' => ['nullable', 'url', 'max:1024'],

            'note.started_at' => ['nullable', 'date'],
            'note.finished_at' => ['nullable', 'date', 'after_or_equal:note.started_at'],
            'note.status' => ['required', 'integer', 'in:0,1,2'],
            'note.note' => ['nullable', 'string'],
        ];
    }
}
