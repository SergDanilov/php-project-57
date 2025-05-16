<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.show__task') }}: {{ $task->name }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('tasks.store') }}" method="POST">
                        @csrf

                        <div class="mb-4 flex flex-col sm:flex-col sm:items-end sm:justify-between gap-4">
                            <div class="w-full">
                                <p class="block text-gray-700 text-sm font-bold mb-2">{{ __('messages.name') }}: <span class="font-normal"> {{ $task->name }}</span></p>
                            </div>
                            <div class="w-full">
                                <p class="block text-gray-700 text-sm font-bold mb-2">{{ __('messages.description') }}:</p>
                                <p>{{ $task->description }}</p>
                            </div>

                            <!-- статус -->
                            <div class="w-full">
                                <p class="block text-gray-700 text-sm font-bold mb-2">{{ __('messages.status') }}: <span    class="font-normal">{{ $task->status->name }}</span>
                                </p>
                            </div>

                            <!-- исполнитель -->
                            <div class="w-full">
                                <p class="block text-gray-700 text-sm font-bold mb-2">{{ __('messages.executor') }}: <span    class="font-normal">{{ $task->assignee->name }}</span>
                                </p>
                            </div>

                            <div class="w-full">
                                <p class="block text-gray-700 text-sm font-bold mb-2">
                                    {{ __('messages.labels') }}:
                                    @forelse($task->labels as $label)
                                        @php
                                            $labelClasses = match(strtolower($label->name)) {
                                                'срочно!' => 'bg-red-100 text-red-800',
                                                'новая' => 'bg-green-100 text-green-800',
                                                default => 'bg-blue-100 text-blue-800'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $labelClasses }} mr-1">
                                            <!-- Иконка метки -->
                                            <svg class="w-3 h-3 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.5 3A2.5 2.5 0 003 5.5v9A2.5 2.5 0 005.5 17h9a2.5 2.5 0 002.5-2.5v-9A2.5 2.5 0 0014.5 3h-9zM6 7a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H7z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $label->name }}
                                        </span>
                                    @empty
                                        <span class="text-gray-500 text-sm italic">{{ __('messages.no_labels') }}</span>
                                    @endforelse
                                </p>
                            </div>

                        </div>

                        <div>

                            <p class="px-6 py-4 whitespace-nowrap">

                            </p>

                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 rounded focus:outline-none focus:shadow-outline">
                                <a href="{{ route('tasks.edit', $task->id) }}"
                                    class="text-white-600 hover:text-white-900 py-2 px-4">{{ __('messages.edit') }}
                                </a>
                            </button>
                            <a href="{{ route('tasks.index') }}"
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