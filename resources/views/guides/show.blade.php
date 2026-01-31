@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="{{ route('guides.index') }}" class="text-slate-500 hover:text-slate-700 mr-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-900">Guide Assignment</h1>
                        <p class="mt-2 text-sm text-slate-600">{{ $guide->name }} in {{ $guide->country }}</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('guides.edit', $guide) }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Assignment
                    </a>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Guide Name -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Guide</label>
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                                <span class="text-white font-semibold text-lg">
                                    {{ substr($guide->name, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-lg font-semibold text-slate-900">{{ $guide->name }}</p>
                                <p class="text-sm text-slate-500">Safari Guide</p>
                            </div>
                        </div>
                    </div>

                    <!-- Country -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Country</label>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-slate-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 6a1 1 0 011-1h.01a1 1 0 010 2H4a1 1 0 01-1-1zM4 8a1 1 0 000 2h.01a1 1 0 000-2H4zM6 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zM7 10a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ $guide->country }}
                            </span>
                        </div>
                    </div>

                    <!-- From Date -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">From Date</label>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-slate-900 font-medium">{{ $guide->date_from->format('F j, Y') }}</span>
                        </div>
                    </div>

                    <!-- To Date -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">To Date</label>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-slate-900 font-medium">{{ $guide->date_to->format('F j, Y') }}</span>
                        </div>
                    </div>

                    <!-- Duration -->
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Duration</label>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-slate-900">
                                {{ $guide->date_from->diffInDays($guide->date_to) + 1 }} days
                                <span class="text-slate-500 ml-2">
                                    ({{ $guide->date_from->format('M j') }} - {{ $guide->date_to->format('M j, Y') }})
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                @if($guide->notes)
                <div class="mt-8 pt-6 border-t border-slate-200">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Notes</label>
                    <div class="bg-slate-50 rounded-md p-4">
                        <p class="text-slate-700 whitespace-pre-wrap">{{ $guide->notes }}</p>
                    </div>
                </div>
                @endif

                <!-- Assignment Timeline -->
                <div class="mt-8 pt-6 border-t border-slate-200">
                    <h3 class="text-lg font-medium text-slate-900 mb-4">Assignment Timeline</h3>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-400 rounded-full mr-3"></div>
                            <div>
                                <p class="text-sm font-medium text-slate-900">Assignment Start</p>
                                <p class="text-sm text-slate-500">{{ $guide->date_from->format('l, F j, Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-orange-400 rounded-full mr-3"></div>
                            <div>
                                <p class="text-sm font-medium text-slate-900">Assignment End</p>
                                <p class="text-sm text-slate-500">{{ $guide->date_to->format('l, F j, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Metadata -->
                <div class="mt-8 pt-6 border-t border-slate-200">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-slate-500">Created</p>
                            <p class="text-slate-900 font-medium">{{ $guide->created_at->format('M j, Y g:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-slate-500">Last Updated</p>
                            <p class="text-slate-900 font-medium">{{ $guide->updated_at->format('M j, Y g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
