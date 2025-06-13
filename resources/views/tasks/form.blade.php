
<div class="mb-4">
    <label for="assigned_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        Assign To
    </label>
    <select name="assigned_to" id="assigned_to"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        <option value="">Select a team member</option>
        @foreach($users as $user)
            <option value="{{ $user->id }}" {{ old('assigned_to', $task->assigned_to ?? '') == $user->id ? 'selected' : '' }}>
                {{ $user->name }} ({{ ucfirst($user->role) }})
            </option>
        @endforeach
    </select>
    @error('assigned_to')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
