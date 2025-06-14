<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-800 leading-tight">
            {{ __('messages.tasks') }}
        </h2>
    </x-slot>
    @can('create', App\Models\Task::class)
        @auth
            <div class="mb-4">
                <a href="{{ route('tasks.create') }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('messages.create__task') }}
                </a>
            </div>
        @endauth
    @endcan
    <!-- Фильтры -->
    <div class="mb-6 bg-gray-50 p-0 rounded-lg">
        <form method="GET" action="{{ route('tasks.index') }}" class="space-y-4 md:space-y-0 md:flex md:space-x-1">
            <!-- Фильтр по статусу -->
            <div class="relative">
                <select name="filter[status_id]" class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                    <option value="">{{ __('messages.status') }}</option>
                    @foreach($statuses as $id => $name)
                        <option value="{{ $id }}" {{ request('filter.status_id') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Фильтр по автору -->
            <div class="relative">
                <select name="filter[created_by_id]" class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                    <option value="">{{ __('messages.author') }}</option>
                    @foreach($users as $id => $name)
                        <option value="{{ $id }}" {{ request('filter.created_by_id') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Фильтр по исполнителю -->
            <div class="relative">
                <select name="filter[assigned_to_id]" class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                    <option value="">{{ __('messages.executor') }}</option>
                    @foreach($users as $id => $name)
                        <option value="{{ $id }}" {{ request('filter.assigned_to_id') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Кнопки -->
            <div class="flex space-x-2">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('messages.apply__filters') }}
                </button>
                <a href="{{ route('tasks.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('messages.cancel') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Таблица -->
    <div class="overflow-x-auto shadow-md sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.name') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.author') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.executor') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-right">{{ __('messages.create__date') }}</th>
                    @auth
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-right">{{ __('messages.actions') }}</th>
                    @endauth
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($tasks as $task)
                    <tr>
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $task->id }}</td>
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500"> {{ $task->status->name ?? 'Без статуса' }}</td>
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-blue-500">
                            <a href="{{ route('tasks.show', $task->id) }}">{{ $task->name }}</a>
                        </td>
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $task->creator->name }}</td>
                        <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $task->assignee->name ?? 'Не назначено' }}</td>
                        <td class="px-6 py-2 whitespace-nowrap text-right">
                            {{ $task->created_at->format('d.m.Y') }}
                        </td>
                        @can('update', $task)
                            <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-right">

                                    <a href="{{ route('tasks.edit', $task->id) }}"
                                    class="text-blue-600 hover:text-blue-900 mr-3">{{ __('messages.edit') }}
                                    </a>
                                @can('delete', $task)
                                    <a href="{{ route('tasks.destroy', $task->id) }}"
                                        data-confirm="Вы уверены?"
                                        data-method="delete"
                                        class="text-red-600 hover:text-red-900">
                                            {{ __('messages.delete') }}
                                    </a>
                                @endcan
                            </td>
                        @endcan
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Пагинация с сохранением параметров фильтрации -->
    <div class="mt-4">
        {{ $tasks->withQueryString()->links() }}
    </div>
</x-app-layout>