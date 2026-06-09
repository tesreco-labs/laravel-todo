<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Display all todos
     */
    public function index(Request $request)
    {
        $query = Todo::query();

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'LIKE', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $todos = $query->orderBy('id')->latest()->paginate(10);

        return view('index', compact('todos'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('create');
    }

    /**
     * Store todo
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:pending,in_progress,completed,cancelled',
            'priority'    => 'required|in:low,medium,high,urgent',
            'due_date'    => 'nullable|date',
            'category'    => 'nullable|string|max:100',
            'tags'        => 'nullable|string|max:255',
        ]);

        // Auto-complete logic
        if ($validated['status'] === 'completed') {
            $validated['is_completed'] = true;
            $validated['completed_at'] = now();
        }

        // Optional auth
        if (auth()->check()) {
            $validated['user_id'] = auth()->id();
        }

        Todo::create($validated);
        return redirect()
            ->route('todos.index')
            ->with('success', 'Todo created successfully.');
    }

    /**
     * Show single todo
     */
    public function show($id)
    {
        $todo = Todo::findOrFail($id);

        return view('show', compact('todo'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $todo = Todo::findOrFail($id);

        return view('edit', compact('todo'));
    }

    /**
     * Update todo
     */
    public function update(Request $request, $id)
    {
        $todo = Todo::findOrFail($id);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:pending,in_progress,completed,cancelled',
            'priority'    => 'required|in:low,medium,high,urgent',
            'due_date'    => 'nullable|date',
            'category'    => 'nullable|string|max:100',
            'tags'        => 'nullable|string|max:255',
        ]);

        // Handle completed state
        if ($validated['status'] === 'completed') {
            $validated['is_completed'] = true;

            if (!$todo->completed_at) {
                $validated['completed_at'] = now();
            }
        } else {
            $validated['is_completed'] = false;
            $validated['completed_at'] = null;
        }

        $todo->update($validated);

        return redirect()
            ->route('todos.index')
            ->with('success', 'Todo updated successfully.');
    }

    /**
     * Delete todo
     */
    public function destroy($id)
    {
        $todo = Todo::findOrFail($id);

        $todo->delete();

        return redirect()
            ->route('todos.index')
            ->with('success', 'Todo deleted successfully.');
    }

    /**
     * Restore soft deleted todo
     */
    public function restore($id)
    {
        $todo = Todo::onlyTrashed()->findOrFail($id);

        $todo->restore();

        return redirect()
            ->route('todos.index')
            ->with('success', 'Todo restored successfully.');
    }

    /**
     * Mark completed
     */
    public function markCompleted($id)
    {
        $todo = Todo::findOrFail($id);

        $todo->update([
            'status'       => 'completed',
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Todo marked as completed.');
    }
}
