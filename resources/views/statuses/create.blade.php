<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.create__status') }}
        </h2>
    </x-slot>
    <form action="{{ route('task_statuses.store') }}" method="POST">
        @csrf

        <div class="mb-4 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div class="w-full">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">{{ __('messages.name') }}:</label>
                <input type="text" class="form-control shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="name" name="name">
                @error('name')
                    <div class="text-danger text-red-500 text-xs italic">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                {{ __('messages.create__status') }}
            </button>
            <a href="{{ route('task_statuses.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                {{ __('messages.back') }}
            </a>
        </div>
    </form>
</x-app-layout>