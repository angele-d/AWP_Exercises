<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('messages.my_projects') }}
            </h2>
            <div class="flex items-center space-x-4">
                <!-- Language selector EN / PL -->
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

                <a href="{{ route('projects.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    + {{ __('messages.new_project') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Section 1 : Projects Manager -->
            <div class="p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-bold mb-4 text-gray-900">{{ __('messages.managed_projects') }}</h3>
                @if($managedProjects->isEmpty())
                    <p class="text-gray-500">{{ __('messages.no_managed') }}</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left">{{ __('messages.name') }}</th>
                                    <th class="px-4 py-2 text-left">{{ __('messages.price') }}</th>
                                    <th class="px-4 py-2 text-left">{{ __('messages.dates') }}</th>
                                    <th class="px-4 py-2 text-left">{{ __('messages.status') }}</th>
                                    <th class="px-4 py-2 text-left">{{ __('messages.team') }}</th>
                                    <th class="px-4 py-2 text-left">{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($managedProjects as $project)
                                    <tr class="border-b">
                                        <td class="px-4 py-2 font-semibold">{{ $project->name }}</td>
                                        <td class="px-4 py-2">{{ number_format($project->price, 2) }} €</td>
                                        <td class="px-4 py-2 text-sm">{{ $project->start_date->format('Y-m-d') }} - {{ $project->end_date->format('Y-m-d') }}</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 rounded text-xs {{ $project->done_jobs ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $project->done_jobs ? __('messages.completed') : __('messages.in_progress') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            {{ $project->members->pluck('name')->join(', ') ?: __('messages.none') }}
                                        </td>
                                        <td class="px-4 py-2">
                                            <a href="{{ route('projects.edit', $project) }}" class="text-blue-600 hover:underline">{{ __('messages.edit_all') }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Section 2 : Projects Member -->
            <div class="p-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-bold mb-4 text-gray-900">{{ __('messages.member_projects') }}</h3>
                @if($memberProjects->isEmpty())
                    <p class="text-gray-500">{{ __('messages.no_member') }}</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left">{{ __('messages.name') }}</th>
                                    <th class="px-4 py-2 text-left">{{ __('messages.manager') }}</th>
                                    <th class="px-4 py-2 text-left">{{ __('messages.price') }}</th>
                                    <th class="px-4 py-2 text-left">{{ __('messages.status') }}</th>
                                    <th class="px-4 py-2 text-left">{{ __('messages.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($memberProjects as $project)
                                    <tr class="border-b">
                                        <td class="px-4 py-2 font-semibold">{{ $project->name }}</td>
                                        <td class="px-4 py-2">{{ $project->manager->name }}</td>
                                        <td class="px-4 py-2">{{ number_format($project->price, 2) }} €</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 rounded text-xs {{ $project->done_jobs ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $project->done_jobs ? __('messages.completed') : __('messages.in_progress') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2">
                                            <a href="{{ route('projects.edit', $project) }}" class="text-blue-600 hover:underline">{{ __('messages.edit_status') }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>