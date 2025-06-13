<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $project->name }}
            </h2>
            <div class="flex space-x-2">
                @can('edit projects')
                <a href="{{ route('projects.edit', $project) }}"
                   class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Edit Project
                </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Project Details Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Project Details</h3>
                            <div class="space-y-4">
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Manager:</span>
                                    <span class="text-gray-900 dark:text-gray-100 ml-2">{{ $project->manager->name }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Timeline:</span>
                                    <span class="text-gray-900 dark:text-gray-100 ml-2">
                                        {{ $project->start_date->format('M d, Y') }} - {{ $project->end_date->format('M d, Y') }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Description:</span>
                                    <p class="text-gray-900 dark:text-gray-100 mt-2">
                                        {{ $project->description }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Project Status</h3>
                            <div class="grid grid-cols-2 gap-4">
                                @can('edit projects')
                                <form action="{{ route('projects.update-status', $project) }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <button type="submit" name="status" value="planned"
                                        class="w-full py-2 px-4 rounded {{ $project->status === 'planned'
                                            ? 'bg-gray-800 text-white'
                                            : 'bg-gray-200 text-gray-800 hover:bg-gray-300' }}">
                                        Planned
                                    </button>
                                </form>

                                <form action="{{ route('projects.update-status', $project) }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <button type="submit" name="status" value="in_progress"
                                        class="w-full py-2 px-4 rounded {{ $project->status === 'in_progress'
                                            ? 'bg-blue-600 text-white'
                                            : 'bg-blue-100 text-blue-800 hover:bg-blue-200' }}">
                                        In Progress
                                    </button>
                                </form>

                                <form action="{{ route('projects.update-status', $project) }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <button type="submit" name="status" value="completed"
                                        class="w-full py-2 px-4 rounded {{ $project->status === 'completed'
                                            ? 'bg-green-600 text-white'
                                            : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                                        Completed
                                    </button>
                                </form>

                                <form action="{{ route('projects.update-status', $project) }}" method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <button type="submit" name="status" value="on_hold"
                                        class="w-full py-2 px-4 rounded {{ $project->status === 'on_hold'
                                            ? 'bg-yellow-600 text-white'
                                            : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' }}">
                                        On Hold
                                    </button>
                                </form>
                                @else
                                <div class="col-span-2 text-center text-gray-500 dark:text-gray-400">
                                    Current Status: <span class="font-semibold">{{ ucfirst($project->status) }}</span>
                                </div>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Tasks -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Project Tasks</h3>
                        @can('create tasks')
                        <a href="{{ route('tasks.create', ['project' => $project->id]) }}"
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add Task
                        </a>
                        @endcan
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left">
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Title</span>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left">
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Assigned To</span>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left">
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</span>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left">
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Due Date</span>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right">
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($project->tasks as $task)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                        {{ $task->title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                        {{ $task->assignedTo->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' :
                                               ($task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' :
                                               'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($task->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 dark:text-gray-100">
                                        {{ $task->due_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @can('edit tasks')
                                        <a href="{{ route('tasks.edit', $task) }}"
                                           class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-4">
                                            Edit
                                        </a>
                                        @endcan

                                        @can('delete tasks')
                                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('Are you sure you want to delete this task?')"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                Delete
                                            </button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No tasks found for this project.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
