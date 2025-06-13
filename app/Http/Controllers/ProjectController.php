<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $projects = auth()->user()->hasRole('admin')
            ? Project::with('manager')->latest()->get()
            : Project::where('manager_id', auth()->id())->latest()->get();

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $managers = User::role('project-manager')->get();
        return view('projects.create', compact('managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'manager_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:planned,in_progress,completed,on_hold',
        ]);

        Project::create($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function edit(Project $project)
    {
        $this->authorize('edit projects');

        $managers = User::role('project-manager')->get();
        return view('projects.edit', compact('project', 'managers'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('edit projects');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'manager_id' => 'required|exists:users,id',
            'status' => 'required|in:planned,in_progress,completed,on_hold',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    public function show(Project $project)
    {
        $project->load(['manager', 'tasks.assignedTo']);
        return view('projects.show', compact('project'));
    }

    public function updateStatus(Project $project, Request $request)
    {
        $request->validate([
            'status' => 'required|in:planned,in_progress,completed,on_hold'
        ]);

        $project->update(['status' => $request->status]);
        return back()->with('success', 'Project status updated successfully');
    }
}
