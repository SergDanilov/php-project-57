<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.edit__task') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4 flex flex-col sm:flex-col sm:items-end sm:justify-between gap-4">
                            <div class="w-full">
                                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">{{ __('messages.name') }}:</label>
                                <input type="text"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="name"
                                    name="name"
                                    value="{{ old('name', $task->name) }}"
                                    required>
                                @error('name')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="w-full">
                                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">{{ __('messages.description') }}:</label>
                                <textarea
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="description"
                                    name="description">{{ old('description', $task->description ?? '') }}
                                </textarea>
                                @error('description')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="w-full">
                                <label for="status_id" class="block text-gray-700 text-sm font-bold mb-2">{{ __('messages.status') }}:</label>
                                <select name="status_id" id="status_id" class="form-control shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                    <option value="{{ $task->status->id }}">{{ $task->status->name }}</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ old('status', $status->id) }}">{{ $status->name }}</option>
                                    @endforeach
                                </select>
                                @error('status_id')
                                    <div class="text-danger text-red-500 text-xs italic">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="w-full">
                                <label for="assigned_to_id" class="block text-gray-700 text-sm font-bold mb-2">{{ __('messages.executor') }}:</label>
                                <select name="assigned_to_id" id="assigned_to_id" class="form-control shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="{{ $task->assignee->id }}">{{ $task->assignee->name }}</option>
                                    @foreach($users as $user)
                                        <option value="{{ old('user', $user->id)}}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('assigned_to_id')
                                    <div class="text-danger text-red-500 text-xs italic">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="w-full">
                                <label for="labels" class="block text-gray-700 text-sm font-bold mb-2">Метки</label>
                                <select name="labels[]" id="labels" multiple class="form-control shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    @foreach($labels as $label)
                                        <option value="{{ $label->id }}"
                                            @selected(collect(old('labels', $task->labels->pluck('id')->toArray()))->contains($label->id))>
                                            {{ $label->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('labels')
                                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            @if(isset($task->created_at))
                            <div class="w-full sm:w-auto">
                                <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('messages.create') }}:</label>
                                <div class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-50">
                                    {{ $task->created_at->format('d.m.Y') }}
                                </div>
                            </div>
                            @endif
                        </div>
                    @can('update', $task)
                        <div class="flex items-center justify-between">
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                {{ __('messages.update') }}
                            </button>
                            <a href="{{ route('tasks.index') }}"
                               class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                                {{ __('messages.cancel') }}
                            </a>
                        </div>
                    @endcan
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>