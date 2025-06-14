<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-800 leading-tight">
            {{ __('messages.statuses') }}
        </h2>
    </x-slot>
    @can('create', App\Models\TaskStatus::class)
        @auth
            <div class="mb-4">
                <a href="{{ route('task_statuses.create') }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('messages.create__status') }}
                </a>
            </div>
        @endauth
    @endcan
    <div class="overflow-x-auto shadow-md sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.name') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-right">{{ __('messages.create__date') }}</th>
                    @auth
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-right">{{ __('messages.actions') }}</th>
                    @endauth
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($statuses as $status)
                    <tr>
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $status->id }}</td>
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $status->name }}</td>
                        <td class="px-6 py-2 whitespace-nowrap text-right">
                            {{ $status->created_at->format('d.m.Y') }}
                        </td>
                        @auth
                        <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-right">
                                <a href="{{ route('task_statuses.edit', $status->id) }}"
                                class="text-blue-600 hover:text-blue-900 mr-3">
                                    {{ __('messages.edit') }}
                                </a>
                            @can('delete', $status)
                                <a href="{{ route('task_statuses.destroy', $status->id) }}"
                                data-confirm="Вы уверены?"
                                data-method="delete"
                                class="text-red-600 hover:text-red-900">
                                    {{ __('messages.delete') }}
                                </a>
                            @endcan
                        </td>
                        @endauth
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div class="mt-4">
        {{ $statuses->links() }}
    </div>
</x-app-layout>