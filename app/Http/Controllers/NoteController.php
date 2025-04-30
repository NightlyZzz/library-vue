<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\Book;
use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

final class NoteController extends Controller
{
    public function store(StoreNoteRequest $request): JsonResponse
    {
        $data = $request->validated();

        $book = Book::firstOrCreate(
            ['google_id' => $data['book']['google_id']],
            [
                'title' => $data['book']['title'],
                'author' => $data['book']['author']  ?? null,
                'cover_url' => $data['book']['cover_url'] ?? null,
            ]
        );

        $exists = Note::where('user_id', Auth::id())->where('book_id', $book->id)->exists();
        if ($exists) {
            return response()->json([
                'errors' => ['note' => ['Заметка по этой книге уже существует.']]
            ], 422);
        }

        $note = Note::create([
            'user_id' => Auth::id(),
            'book_id' => $book->id,
            'started_at' => $data['note']['started_at'] ?? null,
            'finished_at' => $data['note']['finished_at'] ?? null,
            'status' => $data['note']['status'],
            'note' => $data['note']['note'] ?? null,
        ]);

        return response()->json(['note_id' => $note->id], 201);
    }


    public function update(UpdateNoteRequest $request, Note $note): Response
    {
        $data = $request->validated();
        $note->update($data);
        return response()->noContent();
    }
}
