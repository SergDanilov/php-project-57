<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Status') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('task_statuses.update', $task_status->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                            <div class="w-full">
                                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">{{ __('messages.name') }}:</label>
                                <input type="text"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="name"
                                    name="name"
                                    value="{{ old('name', $task_status->name) }}"
                                    required>
                                @error('name')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            @if(isset($task_status->created_at))
                            <div class="w-full sm:w-auto">
                                <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('messages.create') }}:</label>
                                <div class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-50">
                                    {{ $task_status->created_at->format('d.m.Y') }}
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="flex items-center justify-between">
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                {{ __('messages.update') }}
                            </button>
                            <a href="{{ route('task_statuses.index') }}"
                               class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                                {{ __('messages.cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>