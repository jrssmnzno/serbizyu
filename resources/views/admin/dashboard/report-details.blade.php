<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Report #{{ $report->id }}
            </h2>
            <a href="{{ route('admin.disputes') }}" class="text-blue-600 hover:text-blue-800 font-medium">← Back to Reports</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Report Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Report Overview -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Report Overview</h3>
                                <p class="text-sm text-gray-500">Created: {{ $report->created_at->format('F d, Y H:i') }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-lg text-sm font-semibold {{ 
                                $report->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                ($report->status === 'resolved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')
                            }}">
                                {{ ucfirst($report->status) }}
                            </span>
                        </div>

                        <hr class="my-4" />

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold">Reason</p>
                                <p class="text-sm font-medium text-gray-900 mt-1">
                                    {{ ucfirst(str_replace('_', ' ', $report->reason ?? 'N/A')) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-semibold">Report Age</p>
                                <p class="text-sm font-medium text-gray-900 mt-1">
                                    {{ $report->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold mb-2">Description</p>
                            <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded">
                                {{ $report->description ?? 'No description provided' }}
                            </p>
                        </div>
                    </div>

                    <!-- Reported User Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Reported User</h3>
                        <div class="flex gap-4 p-4 bg-red-50 rounded-lg border border-red-200">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-red-500 to-orange-600 text-white flex items-center justify-center text-2xl">
                                {{ strtoupper(substr($report->reportedUser->firstname ?? 'U', 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">USER ACCOUNT</p>
                                <h4 class="text-lg font-bold text-gray-900">
                                    {{ $report->reportedUser->firstname ?? 'Deleted' }} {{ $report->reportedUser->lastname ?? '' }}
                                </h4>
                                <p class="text-sm text-gray-600">{{ $report->reportedUser->email ?? 'N/A' }}</p>
                                <div class="mt-2">
                                    <a href="{{ route('admin.users.show', $report->reportedUser) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                        View Profile →
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reporter Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Reporter</h3>
                        <div class="flex gap-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-cyan-600 text-white flex items-center justify-center text-2xl">
                                {{ strtoupper(substr($report->reporter->firstname ?? 'U', 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-500">REPORTER ACCOUNT</p>
                                <h4 class="text-lg font-bold text-gray-900">
                                    {{ $report->reporter->firstname ?? 'Deleted' }} {{ $report->reporter->lastname ?? '' }}
                                </h4>
                                <p class="text-sm text-gray-600">{{ $report->reporter->email ?? 'N/A' }}</p>
                                <div class="mt-2">
                                    <a href="{{ route('admin.users.show', $report->reporter) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                        View Profile →
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Notes -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Admin Notes</h3>
                        @if($report->admin_notes)
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4">
                                <p class="text-sm text-gray-700">{{ $report->admin_notes }}</p>
                                @if($report->resolved_at)
                                    <p class="text-xs text-gray-500 mt-2">
                                        {{ ucfirst($report->status) }} on {{ $report->resolved_at->format('F d, Y H:i') }}
                                    </p>
                                @endif
                            </div>
                        @else
                            <p class="text-sm text-gray-500 italic">No admin notes yet</p>
                        @endif
                    </div>
                </div>

                <!-- Sidebar Actions -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    @if($report->status === 'pending')
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                        <div class="space-y-2">
                            <button onclick="openResolveModal()" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                                ✓ Resolve Report
                            </button>
                            <button onclick="openDismissModal()" class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-medium">
                                ✗ Dismiss Report
                            </button>
                        </div>
                    </div>
                    @else
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Status</h3>
                        <div class="text-center py-4">
                            <span class="inline-block px-4 py-2 rounded-lg text-lg font-semibold {{ 
                                $report->status === 'resolved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                            }}">
                                {{ ucfirst($report->status) }}
                            </span>
                        </div>
                    </div>
                    @endif

                    <!-- Reported User History -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">User Report History</h3>
                        @php
                            $userReports = $reported_user_stats ?? [];
                        @endphp
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Total Reports</span>
                                <span class="font-bold text-gray-900">{{ $userReports['total'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Pending</span>
                                <span class="font-bold text-yellow-600">{{ $userReports['pending'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Resolved</span>
                                <span class="font-bold text-green-600">{{ $userReports['resolved'] ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Dismissed</span>
                                <span class="font-bold text-gray-600">{{ $userReports['dismissed'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Recommended Actions -->
                    <div class="bg-blue-50 border border-blue-200 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">⚠️ Recommended Actions</h3>
                        <div class="space-y-2">
                            @if(($userReports['total'] ?? 0) > 3)
                                <div class="text-sm text-blue-800">
                                    <p class="font-semibold">🚩 Frequent Reports</p>
                                    <p class="text-xs">Consider suspending user account</p>
                                </div>
                            @endif
                            <div class="text-sm text-blue-800 pt-2 border-t border-blue-200">
                                <p class="text-xs">Review account activity and contact user if necessary</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resolve Modal -->
    <div id="resolveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-96">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Resolve Report</h3>
            <form action="{{ route('admin.disputes.resolve', $report) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Admin Notes</label>
                    <textarea name="admin_notes" rows="4" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Document your resolution..."></textarea>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Resolve
                    </button>
                    <button type="button" onclick="closeResolveModal()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Dismiss Modal -->
    <div id="dismissModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-96">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Dismiss Report</h3>
            <form action="{{ route('admin.disputes.dismiss', $report) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Dismissal</label>
                    <textarea name="admin_notes" rows="4" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500"
                        placeholder="Explain why this report is being dismissed..."></textarea>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        Dismiss
                    </button>
                    <button type="button" onclick="closeDismissModal()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openResolveModal() {
            document.getElementById('resolveModal').classList.remove('hidden');
        }
        function closeResolveModal() {
            document.getElementById('resolveModal').classList.add('hidden');
        }
        function openDismissModal() {
            document.getElementById('dismissModal').classList.remove('hidden');
        }
        function closeDismissModal() {
            document.getElementById('dismissModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
