<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.create__task') }}
        </h2>
    </x-slot>
    <form action="{{ route('tasks.store') }}" method="POST">
        @csrf

        <div class="mb-4 flex flex-col sm:flex-col sm:items-end sm:justify-between gap-4">
            <div class="w-full">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">{{ __('messages.name') }}:</label>
                <input type="text" class="form-control shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="name" name="name"  value="{{ old('name', $task->name ?? '') }}">
                @error('name')
                    <div class="text-danger text-red-500 text-xs italic">{{ $message }}</div>
                @enderror
            </div>
            <div class="w-full">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">{{ __('messages.description') }}:</label>
                <textarea class="form-control shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline h-32 text-left align-top resize-none"
                id="description" name="description" style="text-align: left; white-space: pre-wrap; word-wrap: break-word;">{{ old('description', $task->description ?? '') }}</textarea>
                @error('description')
                    <div class="text-danger text-red-500 text-xs italic">{{ $message }}</div>
                @enderror
            </div>

            <!-- Добавляем селект для статуса -->
            <div class="w-full">
                <label for="status_id" class="block text-gray-700 text-sm font-bold mb-2">{{ __('messages.status') }}:</label>
                <select name="status_id" id="status_id" class="form-control shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="" selected="selected">-- {{ __('messages.select_status') }} --</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->id }}"
                            @if(old('status_id', $task->status_id ?? '') == $status->id) selected @endif>
                            {{ $status->name }}
                        </option>
                    @endforeach
                </select>
                @error('status_id')
                    <div class="text-danger text-red-500 text-xs italic">{{ $message }}</div>
                @enderror
            </div>

            <!-- Добавляем селект для исполнителя -->
            <div class="w-full">
                <label for="assigned_to_id" class="block text-gray-700 text-sm font-bold mb-2">{{ __('messages.executor') }}:</label>
                <select name="assigned_to_id" id="assigned_to_id" class="form-control shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="" selected="selected">-- {{ __('messages.select_executor') }} --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}"
                            @if(old('assigned_to_id', $task->assigned_to_id ?? '') == $user->id) selected @endif>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
                @error('assigned_to_id')
                    <div class="text-danger text-red-500 text-xs italic">{{ $message }}</div>
                @enderror
            </div>

            <div class="w-full">
                <label for="labels" class="block text-gray-700 text-sm font-bold mb-2">{{ __('messages.labels') }}:</label>
                <select name="labels[]" id="labels" multiple  class="form-control shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @foreach($labels as $label)
                        <option value="{{ $label->id }}"
                            @if(in_array($label->id, old('labels', $task->labels->pluck('id')->toArray() ?? []))) selected @endif>
                            {{ $label->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    @can('create', $task)
        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                {{ __('messages.create') }}
            </button>
            <a href="{{ route('tasks.create') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                {{ __('messages.cancel') }}
            </a>
        </div>
    @endcan
    </form>
</x-app-layout>