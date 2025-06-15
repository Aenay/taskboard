<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Activity Log') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-gray-800 rounded-lg shadow-md p-6 mb-6">
                <form method="GET" class="flex gap-4">
                    <div>
                        <label for="action" class="block text-sm font-medium text-white">Action</label>
                        <select name="action" id="action" class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 text-white">
                            <option value="">All Actions</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                    {{ ucfirst($action) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="user" class="block text-sm font-medium text-white">User</label>
                        <select name="user" id="user" class="mt-1 block w-full rounded-md border-gray-700 bg-gray-900 text-white">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Activity List -->
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-700 text-left text-xs font-medium text-white uppercase">Time</th>
                                    <th class="px-6 py-3 bg-gray-700 text-left text-xs font-medium text-white uppercase">User</th>
                                    <th class="px-6 py-3 bg-gray-700 text-left text-xs font-medium text-white uppercase">Action</th>
                                    <th class="px-6 py-3 bg-gray-700 text-left text-xs font-medium text-white uppercase">Details</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @forelse($activities as $activity)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                            {{ $activity->created_at->format('M d, Y H:i:s') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                            {{ $activity->user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                            {{ ucfirst($activity->action) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-white">
                                            {{ $activity->description }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-white">
                                            No activities found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $activities->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
