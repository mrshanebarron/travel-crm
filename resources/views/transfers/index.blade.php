<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Transfer Requests') }}
            </h2>
            <a href="{{ route('transfers.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                + New Transfer
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transfer #</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expenses</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($transfers as $transfer)
                                <tr>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <a href="{{ route('transfers.show', $transfer) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                            {{ $transfer->transfer_number }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $transfer->request_date->format('M j, Y') }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $transfer->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $transfer->status === 'sent' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $transfer->status === 'transfer_completed' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $transfer->status === 'vendor_payments_completed' ? 'bg-green-100 text-green-800' : '' }}">
                                            {{ str_replace('_', ' ', ucfirst($transfer->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-right font-medium text-gray-900">
                                        ${{ number_format($transfer->total_amount, 2) }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $transfer->expenses->count() }} items
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('transfers.show', $transfer) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                        <a href="{{ route('transfers.edit', $transfer) }}" class="text-gray-600 hover:text-gray-900">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        No transfer requests. <a href="{{ route('transfers.create') }}" class="text-indigo-600 hover:text-indigo-900">Create one</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $transfers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
