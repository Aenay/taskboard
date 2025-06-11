<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $projects = auth()->user()->hasRole('admin')
            ? Project::with(['manager', 'tasks'])->latest()->get()
            : auth()->user()->projects()->with(['tasks'])->latest()->get();

        return view('dashboard', compact('projects'));
    }
}
