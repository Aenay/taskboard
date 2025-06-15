<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Create New Task') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('tasks.store') }}" method="POST">
                        @csrf

                        <!-- Project Selection (Only for admin) -->
                        @if(auth()->user()->hasRole('admin'))
                        <div class="mb-4">
                            <label for="project_id" class="block text-sm font-medium text-white">Project</label>
                            <select name="project_id" id="project_id" class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 text-white" required>
                                <option value="">Select Project</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                            @error('project_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        @else
                        <!-- For project manager, automatically use their first project -->
                        <input type="hidden" name="project_id" value="{{ $projects->first()->id }}">
                        @endif

                        <!-- Task Title -->
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-white">Title</label>
                            <input type="text" name="title" id="title" class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 text-white" required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Assign To -->
                        <div class="mb-4">
                            <label for="assigned_to" class="block text-sm font-medium text-white">Assign To</label>
                            <select name="assigned_to" id="assigned_to" class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 text-white" required>
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Due Date -->
                        <div class="mb-4">
                            <label for="due_date" class="block text-sm font-medium text-white">Due Date</label>
                            <input type="date" name="due_date" id="due_date" class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 text-white" required>
                            @error('due_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-white">Description</label>
                            <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 text-white"></textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Create Task
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
