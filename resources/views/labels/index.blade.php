<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-800 leading-tight">
            {{ __('messages.labels') }}
        </h2>
    </x-slot>
    @can('create', App\Models\Label::class)
        @auth
            <div class="mb-4">
                <a href="{{ route('labels.create') }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('messages.create__label') }}
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
                        <td class="px-6 py-2 whitespace-wrap text-sm text-gray-500">{{ $label->description }}</td>
                        <td class="px-6 py-2 whitespace-nowrap text-right">
                            {{ $label->created_at->format('d.m.Y') }}
                        </td>
                        @auth
                        <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-right">
                            <div class="space-x-3">
                                <a href="{{ route('labels.edit', $label->id) }}"
                                class="text-blue-600 hover:text-blue-900 mr-3">
                                    {{ __('messages.edit') }}
                                </a>
                            @can('delete', $label)
                                <a href="{{ route('labels.destroy', $label->id) }}"
                                data-confirm="Вы уверены?"
                                data-method="delete"
                                class="text-red-600 hover:text-red-900">
                                    {{ __('messages.delete') }}
                                </a>
                            @endcan
                            </div>
                        </td>
                        @endauth
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div class="mt-4">
        {{ $labels->links() }}
    </div>
</x-app-layout>