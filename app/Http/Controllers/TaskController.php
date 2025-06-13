<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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
            ->latest()
            ->paginate(10);

        return view('tasks.index', compact('tasks'));
    }

    public function create(Request $request)
    {
        $this->authorize('create tasks');

        // Get project manager's projects or specific project if provided
        if (auth()->user()->hasRole('admin')) {
            $project = $request->project ? Project::findOrFail($request->project) : null;
        } else {
            // For project managers, either use specified project (if they manage it) or their first project
            if ($request->project) {
                $project = Project::where('manager_id', auth()->id())
                    ->findOrFail($request->project);
            } else {
                $project = Project::where('manager_id', auth()->id())->first();

                if (!$project) {
                    return redirect()->route('tasks.index')
                        ->with('error', 'You need to be assigned as a project manager to create tasks.');
                }
            }
        }

        $users = $this->getAssignableUsers();

        return view('tasks.create', compact('users', 'project'));
    }

    public function store(Request $request)
    {
        $this->authorize('create tasks');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:todo,in_progress,review,completed',
            'due_date' => 'required|date',
        ]);

        // Get the project based on user role
        if (auth()->user()->hasRole('admin')) {
            $project = Project::findOrFail($request->project_id);
        } else {
            $project = Project::where('manager_id', auth()->id())->first();
            if (!$project) {
                return redirect()->route('tasks.index')
                    ->with('error', 'No project found.');
            }
        }

        Task::create([
            ...$validated,
            'project_id' => $project->id,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Task created successfully.');
    }

    public function edit(Task $task)
    {
        $this->authorize('edit tasks');

        $projects = Project::all();
        $users = $this->getAssignableUsers();

        return view('tasks.edit', compact('task', 'projects', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('edit tasks');

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
        // Check if user is assigned to this task
        if (auth()->id() !== $task->assigned_to && !auth()->user()->hasAnyRole(['admin', 'project-manager'])) {
            abort(403, 'You can only update tasks assigned to you.');
        }

        // Get current status
        $currentStatus = $task->status;

        // Determine new status based on role and current status
        if (auth()->user()->hasAnyRole(['admin', 'project-manager'])) {
            // Admin and project manager can toggle between all statuses
            $newStatus = match ($currentStatus) {
                'todo' => 'in_progress',
                'in_progress' => 'review',
                'review' => 'completed',
                'completed' => 'todo',
            };
        } else {
            // Members can only toggle between todo, in_progress, and review
            $newStatus = match ($currentStatus) {
                'todo' => 'in_progress',
                'in_progress' => 'review',
                'review' => 'todo',
                'completed' => 'todo',
            };
        }

        $task->update([
            'status' => $newStatus,
            'completed_at' => $newStatus === 'completed' ? now() : null,
        ]);

        return back()->with('success', 'Task status updated successfully.');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete tasks');

        $task->delete();

        return redirect()->route('projects.{project_id}')
            ->with('success', 'Task deleted successfully.');
    }
}
