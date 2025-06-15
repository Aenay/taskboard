<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Notifications\TaskDocumentationSubmitted;

class TaskController extends Controller
{
    use AuthorizesRequests;

    private function getAssignableUsers()
    {
        if (auth()->user()->hasRole('admin')) {
            // Admin can assign to anyone
            return User::role(['admin', 'project-manager', 'member'])->get();
        } else {
            // Project manager can only assign to members
            return User::role('member')->get();
        }
    }

    public function index()
    {
        $tasks = Task::query()
            ->with(['project.manager', 'assignedTo', 'creator'])
            ->when(auth()->user()->hasRole('project-manager'), function ($query) {
                $query->whereHas('project', function ($q) {
                    $q->where('manager_id', auth()->id());
                });
            })
            ->when(auth()->user()->hasRole('member'), function ($query) {
                $query->where('assigned_to', auth()->id());
            })
            ->latest()
            ->paginate(10);

        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        // Check if user is admin or project manager
        if (!auth()->user()->hasAnyRole(['admin', 'project-manager'])) {
            abort(403);
        }

        // Get projects based on role
        if (auth()->user()->hasRole('admin')) {
            $projects = Project::all();
        } else {
            $projects = Project::where('manager_id', auth()->id())->get();
        }

        if ($projects->isEmpty()) {
            return redirect()->route('tasks.index')
                ->with('error', 'You need to have projects assigned to create tasks.');
        }

        // Get assignable users (members only)
        $users = User::role('member')->get();

        return view('tasks.create', compact('projects', 'users'));
    }

    public function store(Request $request)
    {
        // Check if user is admin or project manager
        if (!auth()->user()->hasAnyRole(['admin', 'project-manager'])) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|exists:projects,id',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'required|date|after:today',
        ]);

        // Verify project manager can only create tasks for their projects
        if (auth()->user()->hasRole('project-manager')) {
            $project = Project::findOrFail($validated['project_id']);
            if ($project->manager_id !== auth()->id()) {
                abort(403, 'You can only create tasks for your own projects.');
            }
        }

        $task = Task::create([
            ...$validated,
            'status' => 'in_progress',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task created successfully.');
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);

        $projects = Project::all();
        $users = $this->getAssignableUsers();

        return view('tasks.edit', compact('task', 'projects', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

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

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return view('tasks.show', compact('task'));
    }

    public function toggleStatus(Task $task)
    {
        if (auth()->id() !== $task->assigned_to) {
            abort(403);
        }

        $task->update([
            'status' => 'review'
        ]);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task marked for review successfully.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        if (auth()->id() !== $task->project->manager_id && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:completed,in_progress,review'
        ]);

        $task->update([
            'status' => $validated['status'],
            'completed_at' => $validated['status'] === 'completed' ? now() : null
        ]);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task status updated successfully.');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $projectId = $task->project_id;
        $task->delete();

        // Redirect back to the project view if coming from there
        if (url()->previous() === route('projects.show', $projectId)) {
            return redirect()->route('projects.show', $projectId)
                ->with('success', 'Task deleted successfully.');
        }

        // Otherwise return to tasks index
        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    public function submitDocumentation(Request $request, Task $task)
    {
        if (auth()->id() !== $task->assigned_to) {
            abort(403);
        }

        $validated = $request->validate([
            'documentation' => 'required|string|min:10',
        ]);

        $task->update([
            'documentation' => $validated['documentation'],
            'status' => 'review',
            'documentation_submitted_at' => now(),
        ]);

        return redirect()->route('tasks.show', $task)
            ->with('success', 'Documentation submitted and task marked for review.');
    }
}
