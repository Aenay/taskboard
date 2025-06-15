<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Tasks') }}
            </h2>
            @can('create tasks')
            <a href="{{ route('tasks.create') }}"
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create Task
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left">
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Title</span>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left">
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Project</span>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left">
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Manager</span>
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
                                @forelse($tasks as $task)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap dark:text-gray-300">
                                        {{ $task->title }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap  dark:text-gray-300">
                                        {{ $task->project->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap  dark:text-gray-300">
                                        {{ $task->project->manager->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <form action="{{ route('tasks.toggle-status', $task) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    {{ $task->status === 'completed' ? 'bg-green-100 text-green-800 hover:bg-green-200' :
                                                       ($task->status === 'review' ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' :
                                                       ($task->status === 'in_progress' ? 'bg-blue-100 text-blue-800 hover:bg-blue-200' :
                                                       'bg-gray-100 text-gray-800 hover:bg-gray-200')) }}">
                                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap  dark:text-gray-300">
                                        {{ $task->due_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if(auth()->user()->hasAnyRole(['admin', 'project-manager']) || $task->assigned_to === auth()->id())
                                            <a href="{{ route('tasks.show', $task) }}"
                                               class="text-indigo-400 hover:text-indigo-300">
                                                View Details
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-white">
                                        No tasks found
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
