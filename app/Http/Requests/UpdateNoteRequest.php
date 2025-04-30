<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateNoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $note = $this->route('note');
        return auth()->check() && $note && $note->user_id === auth()->id();
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'started_at' => ['nullable','date'],
            'finished_at' => ['nullable','date','after_or_equal:started_at'],
            'status' => ['required','integer','in:0,1,2'],
            'note' => ['nullable','string'],
        ];
    }
}
