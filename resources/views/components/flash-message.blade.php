@props(['type' => 'success', 'message' => ''])

@if($message)
    <div class="fixed top-4 right-4 mb-4 p-4
        @if($type === 'success') bg-green-100 text-green-700 @endif
        @if($type === 'error') bg-red-100 text-red-700 @endif
        rounded shadow-lg z-50 transition-opacity duration-300"
        x-data="{ show: true }"
        x-show="show"
        x-transition>
        {{ $message }}
    </div>
@endif