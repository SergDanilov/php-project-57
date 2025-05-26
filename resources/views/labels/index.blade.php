<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.labels') }}
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
                            <a href="{{ route('labels.create') }}"
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('messages.create__label') }}
                            </a>
                        </div>
                    @endauth

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.name') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('messages.description') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-right">{{ __('messages.create__date') }}</th>
                                @auth
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-right">{{ __('messages.actions') }}</th>
                                @endauth
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($labels as $label)
                                <tr>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $label->id }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500"> {{ $label->name }}</td>

                                    <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-500">{{ $label->description }}</td>
                                    <td class="px-6 py-2 whitespace-nowrap text-right">
                                        {{ $label->created_at->format('d.m.Y') }}
                                    </td>
                                    <!-- @auth
                                        <td class="px-6 py-2 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('labels.edit', $label->id) }}"
                                               class="text-yellow-600 hover:text-yellow-900 mr-3">{{ __('messages.edit') }}
                                            </a>
                                            <form action="{{ route('labels.destroy', $label->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" data-confirm="Вы уверены?" data-method="delete"
                                                        class="text-red-600 hover:text-red-900"
                                                        onclick="return confirm('Вы уверены?')">
                                                    {{ __('messages.delete') }}
                                                </button>
                                            </form>
                                        </td>
                                    @endauth -->
                                    @auth
                                    <td class="px-6 py-2 whitespace-nowrap text-sm font-medium flex justify-end">
                                        <div class="flex space-x-3">
                                            <a href="{{ route('labels.edit', $label->id) }}"
                                            class="text-blue-600 hover:text-blue-900 mr-3">
                                                {{ __('messages.edit') }}
                                            </a>

                                            <a href="{{ route('labels.destroy', $label->id) }}"
                                            data-confirm="Вы уверены?"
                                            data-method="delete"
                                            class="text-red-600 hover:text-red-900">
                                                {{ __('messages.delete') }}
                                            </a>
                                        </div>
                                    </td>
                                    @endauth
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Пагинация -->
                    <div class="mt-4">
                        {{ $labels->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>