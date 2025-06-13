<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-gray-500 dark:text-gray-400 text-sm">My Tasks</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ auth()->user()->assignedTasks()->count() }}
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <x-nav-link :href="route('projects.index')" :active="request()->routeIs('index')">
                        <div class="p-6">
                            <div class="text-gray-500 dark:text-gray-400 text-sm">Projects</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ auth()->user()->hasRole('admin') ? \App\Models\Project::count() : auth()->user()->projects()->count() }}
                            </div>
                        </div>
                    </x-nav-link>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-gray-500 dark:text-gray-400 text-sm">Completed Tasks</div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ auth()->user()->assignedTasks()->where('status', 'completed')->count() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Projects Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($projects as $project)
                            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <div class="flex justify-between items-start mb-4">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $project->name }}
                                        </h3>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $project->status === 'completed' ? 'bg-green-100 text-green-800' :
                    ($project->status === 'in_progress' ? 'bg-blue-100 text-blue-800' :
                        'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($project->status) }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">
                                        {{ Str::limit($project->description, 100) }}
                                    </p>
                                    <div class="flex justify-between items-center text-sm text-gray-500 dark:text-gray-400">
                                        <div>
                                            <span class="font-medium">Due:</span>
                                            {{ $project->end_date->format('M d, Y') }}
                                        </div>
                                        <div>
                                            <span class="font-medium">Tasks:</span>
                                            {{ $project->tasks()->count() }}
                                        </div>
                                    </div>
                                    <div class="mt-4 flex justify-end space-x-2">
                                        <a href="{{ route('projects.show', $project) }}"
                                            class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
