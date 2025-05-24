<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.statuses') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @auth
                        <div class="mb-4">
                            <a href="{{ route('task_statuses.create') }}"
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('messages.create__status') }}
                            </a>
                        </div>
                    @endauth

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.name') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.create') }}</th>
                                @auth
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.actions') }}</th>
                                @endauth
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($statuses as $status)
                                <tr>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $status->id }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $status->name }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap">
                                        {{ $status->created_at->format('d.m.Y') }}
                                    </td>
                                    @auth
                                        <td class="px-6 py-2 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('task_statuses.edit', $status->id) }}"
                                               class="text-yellow-600 hover:text-yellow-900 mr-3">{{ __('messages.edit') }}</a>
                                            <form action="{{ route('task_statuses.destroy', $status->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-600 hover:text-red-900"
                                                        onclick="return confirm('Вы уверены?')">
                                                    {{ __('messages.delete') }}
                                                </button>
                                            </form>
                                        </td>
                                    @endauth
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Пагинация -->
                    <div class="mt-4">
                        {{ $statuses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>