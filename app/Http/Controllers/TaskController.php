<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = auth()->user()->hasRole('admin')
            ? Task::with(['project', 'assignedTo'])->latest()->get()
            : Task::where('assigned_to', auth()->id())->with(['project'])->latest()->get();

        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $projects = Project::all();
        $users = User::role(['member', 'project-manager'])->get();
        return view('tasks.create', compact('projects', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'required|exists:users,id',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:todo,in_progress,review,completed',
            'due_date' => 'required|date',
        ]);

        Task::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    public function edit(Task $task)
    {
        $projects = Project::all();
        $users = User::role(['member', 'project-manager'])->get();
        return view('tasks.edit', compact('task', 'projects', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'required|exists:users,id',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:todo,in_progress,review,completed',
            'due_date' => 'required|date',
        ]);

        $task->update($validated);

        if ($validated['status'] === 'completed' && !$task->completed_at) {
            $task->update(['completed_at' => now()]);
        }

        return redirect()->route('tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    public function toggleStatus(Task $task)
    {
        $task->update([
            'status' => $task->status !== 'completed' ? 'completed' : 'todo',
            'completed_at' => $task->status !== 'completed' ? now() : null
        ]);

        return back()->with('success', 'Task status updated.');
    }
}
