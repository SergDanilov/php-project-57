<div class="max-w-screen-xl px-4 pt-20 pb-8 mx-auto lg:py-16 lg:pt-28">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center">
        <!-- Блок ссылок (левый) -->
        <div class="mb-6 lg:mb-0 lg:mr-8">
            <h5 class="font-bold mb-3"><a href="/" class="text-dark hover:text-gray-600">{{ __('messages.home__link') }}</a></h5>
            <ul class="list-unstyled space-y-2">
                <li><a href="/tasks" class="text-dark hover:text-gray-600">{{ __('messages.tasks__link') }}</a></li>
                <li><a href="{{ route('task_statuses.index') }}" class="text-dark hover:text-gray-600">{{ __('messages.statuses__link') }}</a></li>
                <li><a href="/labels" class="text-dark hover:text-gray-600">{{ __('messages.tags__link') }}</a></li>
            </ul>
        </div>

        <!-- Блок контактов (правый) -->
        <div class="mb-6 lg:mb-0">
            <h5 class="font-bold mb-3">{{ __('messages.contacts') }}</h5>
            <ul class="list-unstyled space-y-2">
                <li>Email: info@example.com</li>
                <li>{{ __('messages.phone') }}: +7 (123) 456-7890</li>
            </ul>
        </div>
    </div>

    <!-- Копирайт (по центру) -->
    <div class="text-center mt-10 pt-6 border-t border-gray-200">
        <p class="text-gray-600 mb-0">&copy; {{ date('Y') }} {{ __('messages.copyrights') }}</p>
    </div>
</div>