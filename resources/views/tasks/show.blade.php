<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                Task: {{ $task->title }}
            </h2>
            <div class="flex space-x-4">
                <!-- Member Buttons and Documentation Form -->
                @if(auth()->id() === $task->assigned_to)
                    @if($task->status === 'in_progress')
                        <button type="button"
                            onclick="document.getElementById('documentationForm').classList.toggle('hidden')"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add Documentation
                        </button>
                    @endif
                @endif

                <!-- Project Manager Review Buttons -->
                @if(auth()->id() === $task->project->manager_id)
                    @if($task->status === 'review')
                        <div class="flex space-x-2">
                            <form action="{{ route('tasks.update-status', $task) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit"
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Approve & Complete
                                </button>
                            </form>

                            <form action="{{ route('tasks.update-status', $task) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="in_progress">
                                <button type="submit"
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                    Return for Revision
                                </button>
                            </form>
                        </div>
                    @endif
                @endif

                <!-- Back Button -->
                <a href="{{ url()->previous() }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back
                </a>
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

            <!-- Task Details Card -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-white mb-4">Task Information</h3>
                            <div class="space-y-3">
                                <p class="text-white"><span class="text-gray-400">Project:</span> {{ $task->project->name }}</p>
                                <p class="text-white"><span class="text-gray-400">Manager:</span> {{ $task->project->manager->name }}</p>
                                <p class="text-white"><span class="text-gray-400">Assigned To:</span> {{ $task->assignedTo->name }}</p>
                                <p class="text-white"><span class="text-gray-400">Due Date:</span> {{ $task->due_date->format('M d, Y') }}</p>
                                <p class="text-white"><span class="text-gray-400">Status:</span>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $task->status === 'completed' ? 'bg-green-100 text-green-800' :
                                           ($task->status === 'review' ? 'bg-yellow-100 text-yellow-800' :
                                           ($task->status === 'in_progress' ? 'bg-blue-100 text-blue-800' :
                                           'bg-gray-100 text-gray-800')) }}">
                                        {{ ucfirst($task->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white mb-4">Description</h3>
                            <p class="text-white">{{ $task->description ?: 'No description provided.' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documentation Form - Hidden by default -->
            @if(auth()->id() === $task->assigned_to && $task->status === 'in_progress')
            <div id="documentationForm" class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6 hidden">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Submit Documentation</h3>
                    <form action="{{ route('tasks.submit-documentation', $task) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="documentation" class="block text-sm font-medium text-white mb-2">
                                Documentation
                            </label>
                            <textarea
                                name="documentation"
                                id="documentation"
                                rows="5"
                                class="w-full rounded-md border-gray-700 bg-gray-900 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            >{{ old('documentation', $task->documentation) }}</textarea>
                            @error('documentation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button"
                                onclick="document.getElementById('documentationForm').classList.add('hidden')"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </button>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Submit & Mark for Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <!-- Submitted Documentation - Visible to assigned user and project manager -->
            @if($task->documentation)
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Submitted Documentation</h3>
                    <div class="bg-gray-900 p-4 rounded">
                        <p class="text-white whitespace-pre-wrap">{{ $task->documentation }}</p>
                    </div>
                    @if($task->documentation_submitted_at)
                    <div class="mt-2 text-sm text-gray-400">
                        Submitted by {{ $task->assignedTo->name }} on {{ $task->documentation_submitted_at->format('M d, Y H:i') }}
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
