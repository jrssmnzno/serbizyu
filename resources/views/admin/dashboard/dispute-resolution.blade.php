<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dispute Resolution') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-600 text-sm">Pending Reports</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $pending_count ?? 0 }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-600 text-sm">Resolved</p>
                    <p class="text-3xl font-bold text-green-600">{{ $resolved_count ?? 0 }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-600 text-sm">Dismissed</p>
                    <p class="text-3xl font-bold text-gray-600">{{ $dismissed_count ?? 0 }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-600 text-sm">Total Reports</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $total_count ?? 0 }}</p>
                </div>
            </div>

            <!-- Report Reason Distribution -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Report Reasons</h3>
                    <div class="space-y-2">
                        @foreach($top_reasons ?? [] as $reason => $count)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $reason)) }}</span>
                                <span class="font-bold text-gray-900">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 lg:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Options</h3>
                    <form method="GET" class="space-y-3">
                        <div class="flex gap-2">
                            <select name="status" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="dismissed" {{ request('status') === 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                            </select>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Reports Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Reported User</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Reporter</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Reason</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Created</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $report)
                            <tr class="border-b border-gray-100 hover:bg-gray-50 {{ $report->status === 'pending' ? 'bg-yellow-50' : '' }}">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">#{{ $report->id }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="{{ route('admin.users.show', $report->reportedUser) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        {{ $report->reportedUser->firstname ?? 'Deleted' }} {{ $report->reportedUser->lastname ?? '' }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="{{ route('admin.users.show', $report->reporter) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        {{ $report->reporter->firstname ?? 'Deleted' }} {{ $report->reporter->lastname ?? '' }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ ucfirst(str_replace('_', ' ', $report->reason ?? 'N/A')) }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-block px-2 py-1 rounded text-xs font-semibold {{ 
                                        $report->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                        ($report->status === 'resolved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')
                                    }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $report->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="{{ route('admin.disputes.show', $report) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        Review
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    No reports found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-6 border-t border-gray-200">
                    {{ $reports->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
