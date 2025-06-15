<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('view activities')) {
            abort(403);
        }

        // Get activities with relationships
        $activities = Activity::with(['user', 'subject'])
            ->latest()
            ->paginate(20);

        // Get unique actions for filter dropdown
        $actions = ['created', 'updated', 'deleted', 'submitted', 'reviewed', 'completed'];

        // Get users for filter dropdown
        $users = User::all();

        return view('admin.activities.index', compact('activities', 'actions', 'users'));
    }
}
