<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('messages.create_project') }}
            </h2>
            <div class="flex items-center space-x-4">
                <!-- Sélecteur de langue -->
                <div class="inline-flex rounded-md shadow-sm" role="group">
                    <a href="{{ route('lang.switch', 'en') }}" 
                       class="px-3 py-1 text-sm font-medium border border-gray-300 rounded-l-lg hover:bg-gray-100 {{ app()->getLocale() == 'en' ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-white text-gray-700' }}">
                       🇬🇧 EN
                    </a>
                    <a href="{{ route('lang.switch', 'pl') }}" 
                       class="px-3 py-1 text-sm font-medium border border-gray-300 rounded-r-lg hover:bg-gray-100 {{ app()->getLocale() == 'pl' ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-white text-gray-700' }}">
                       🇵🇱 PL
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-sm space-y-4">
                <div class="mb-4">
                    <a href="{{ route('projects.index') }}" class="text-gray-600 hover:text-gray-900 text-sm">
                        ← {{ __('messages.back') }}
                    </a>
                </div>

                <form action="{{ route('projects.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block font-medium text-sm text-gray-700">{{ __('messages.name') }}</label>
                        <input type="text" name="name" required class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">{{ __('messages.description') }}</label>
                        <textarea name="description" class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700">{{ __('messages.price') }} (€)</label>
                        <input type="number" step="0.01" name="price" required class="w-full border-gray-300 rounded-md shadow-sm">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">{{ __('messages.start_date') }}</label>
                            <input type="date" name="start_date" required class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label class="block font-medium text-sm text-gray-700">{{ __('messages.end_date') }}</label>
                            <input type="date" name="end_date" required class="w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium text-sm text-gray-700 mb-2">{{ __('messages.team') }}</label>
                        <select name="members[]" multiple class="w-full border-gray-300 rounded-md shadow-sm h-32">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">{{ __('messages.select_members_help') }}</p>
                    </div>

                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="done_jobs" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm">
                            <span class="ml-2 text-sm text-gray-600">{{ __('messages.done_jobs') }}</span>
                        </label>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            {{ __('messages.submit_create') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>