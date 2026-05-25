<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Note;

class NoteController extends Controller
{
    /**
     * Display a listing of the notes.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Query only authenticated user's notes
        $query = $request->user()->notes();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Sort by the latest updated_at
        $notes = $query->orderBy('updated_at', 'desc')
                       ->get();

        // Calculate statistics for the authenticated user only
        $stats = [
            'total' => $request->user()->notes()->count(),
            'colors' => $request->user()->notes()->selectRaw('color, count(*) as count')
                            ->groupBy('color')
                            ->pluck('count', 'color')
                            ->toArray()
        ];

        return view('notes.index', compact('notes', 'search', 'stats'));
    }

    /**
     * Store a newly created note in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'color' => 'nullable|string|in:amber,emerald,blue,rose,violet,slate',
        ]);

        $validated['color'] = $validated['color'] ?? 'amber';

        // Associate note with active user
        $request->user()->notes()->create($validated);

        return redirect()->route('notes.index')->with('success', 'Catatan berhasil dibuat!');
    }

    /**
     * Update the specified note in storage.
     */
    public function update(Request $request, Note $note)
    {
        // Enforce authorization
        abort_if($note->user_id !== $request->user()->id, 403, 'Anda tidak memiliki akses ke catatan ini.');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'color' => 'nullable|string|in:amber,emerald,blue,rose,violet,slate',
        ]);

        $validated['color'] = $validated['color'] ?? 'amber';

        $note->update($validated);

        return redirect()->route('notes.index')->with('success', 'Catatan berhasil diperbarui!');
    }

    /**
     * Remove the specified note from storage.
     */
    public function destroy(Request $request, Note $note)
    {
        // Enforce authorization
        abort_if($note->user_id !== $request->user()->id, 403, 'Anda tidak memiliki akses ke catatan ini.');

        $note->delete();

        return redirect()->route('notes.index')->with('success', 'Catatan berhasil dihapus!');
    }
}
