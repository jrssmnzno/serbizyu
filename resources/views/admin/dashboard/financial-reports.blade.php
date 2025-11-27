<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Financial Reports') }}
            </h2>
            <a href="{{ route('admin.financial-reports') }}?generate=true" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                + Generate Report
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Generate New Report -->
            @if(request('generate'))
            <div class="mb-8 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Generate New Report</h3>
                <form action="{{ route('admin.financial-reports.generate') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                            <select name="month" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                        {{ now()->setMonth($m)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                            <select name="year" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @for($y = now()->year - 2; $y <= now()->year; $y++)
                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Generate Report
                        </button>
                        <a href="{{ route('admin.financial-reports') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
            @endif

            <!-- Reports List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Reports</h3>
                    
                    @if($reports->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-200">
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Period</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">GMV</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Platform Fees</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Seller Earnings</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Refunds</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Net Revenue</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports as $report)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ $report->getPeriodLabel() }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium text-purple-600">
                                            {{ $report->getFormattedGMV() }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium text-green-600">
                                            {{ $report->getFormattedFees() }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium text-blue-600">
                                            {{ $report->getFormattedEarnings() }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium text-red-600">
                                            {{ $report->getFormattedRefunds() }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-bold text-gray-900">
                                            {{ $report->getFormattedNetRevenue() }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="inline-block px-2 py-1 rounded text-xs font-semibold {{ $report->isComplete() ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($report->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <a href="{{ route('admin.financial-reports.export', $report) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                                Export CSV
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $reports->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-500 mb-4">No reports generated yet</p>
                            <a href="{{ route('admin.financial-reports') }}?generate=true" class="text-blue-600 hover:text-blue-800 font-medium">
                                Generate First Report
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Report Summary -->
            @if($reports->count() > 0)
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                @php
                    $latestReport = $reports->first();
                @endphp
                
                <!-- Latest Report Details -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Latest Report Summary</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Period:</span>
                            <span class="font-medium text-gray-900">{{ $latestReport->getPeriodLabel() }}</span>
                        </div>
                        <div class="flex justify-between border-t pt-3">
                            <span class="text-gray-600">Total GMV:</span>
                            <span class="font-bold text-purple-600">{{ $latestReport->getFormattedGMV() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Platform Fees ({{ $latestReport->getFeePercentage() }}%):</span>
                            <span class="font-bold text-green-600">{{ $latestReport->getFormattedFees() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Seller Earnings:</span>
                            <span class="font-bold text-blue-600">{{ $latestReport->getFormattedEarnings() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Refunds:</span>
                            <span class="font-bold text-red-600">{{ $latestReport->getFormattedRefunds() }}</span>
                        </div>
                        <div class="flex justify-between border-t pt-3">
                            <span class="text-gray-600 font-semibold">Net Revenue:</span>
                            <span class="font-bold text-lg text-gray-900">{{ $latestReport->getFormattedNetRevenue() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Fee Breakdown -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Fee Breakdown</h3>
                    <div class="space-y-3">
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-600">Platform Fees</span>
                                <span class="text-sm font-medium text-gray-900">{{ $latestReport->getFeePercentage() }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $latestReport->getFeePercentage() }}%"></div>
                            </div>
                            <p class="text-xs text-green-600 mt-1">{{ $latestReport->getFormattedFees() }}</p>
                        </div>
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm text-gray-600">Seller Earnings</span>
                                <span class="text-sm font-medium text-gray-900">{{ 100 - $latestReport->getFeePercentage() }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ 100 - $latestReport->getFeePercentage() }}%"></div>
                            </div>
                            <p class="text-xs text-blue-600 mt-1">{{ $latestReport->getFormattedEarnings() }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
