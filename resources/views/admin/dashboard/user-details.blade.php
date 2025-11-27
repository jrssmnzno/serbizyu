<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $user->firstname }} {{ $user->lastname }}
            </h2>
            <a href="{{ route('admin.users') }}" class="text-blue-600 hover:text-blue-800 font-medium">← Back to Users</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- User Profile -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Profile Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-center mb-6">
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 text-white flex items-center justify-center text-4xl mx-auto mb-4">
                            {{ strtoupper(substr($user->firstname, 0, 1)) }}{{ strtoupper(substr($user->lastname, 0, 1)) }}
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $user->firstname }} {{ $user->lastname }}</h3>
                        <p class="text-sm text-gray-600">{{ $user->email }}</p>
                    </div>
                    <hr class="my-4" />
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold">Status</p>
                            @if($user->suspended_until && $user->suspended_until > now())
                                <span class="inline-block px-2 py-1 bg-red-100 text-red-800 text-xs rounded font-semibold mt-1">
                                    Suspended until {{ $user->suspended_until->format('M d, Y') }}
                                </span>
                            @elseif($user->email_verified_at)
                                <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs rounded font-semibold mt-1">
                                    Active
                                </span>
                            @else
                                <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded font-semibold mt-1">
                                    Pending Verification
                                </span>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold">Joined</p>
                            <p class="text-sm text-gray-900 font-medium mt-1">{{ $user->created_at->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold">Phone</p>
                            <p class="text-sm text-gray-900 font-medium mt-1">{{ $user->phonenumber ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold">Role</p>
                            <div class="mt-1 space-x-1">
                                @if($user->hasRole('seller'))
                                    <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded font-semibold">Seller</span>
                                @endif
                                @if($user->hasRole('buyer'))
                                    <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs rounded font-semibold">Buyer</span>
                                @endif
                                @if($user->hasRole('admin'))
                                    <span class="inline-block px-2 py-1 bg-red-100 text-red-800 text-xs rounded font-semibold">Admin</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Statistics</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm text-gray-600">Orders as Buyer</span>
                                <span class="text-lg font-bold text-gray-900">{{ $user->orders_as_buyer_count ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm text-gray-600">Orders as Seller</span>
                                <span class="text-lg font-bold text-gray-900">{{ $user->orders_as_seller_count ?? 0 }}</span>
                            </div>
                            <hr class="my-3" />
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm text-gray-600">Money Spent</span>
                                <span class="text-lg font-bold text-blue-600">${{ number_format($user->total_spent ?? 0, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Money Earned</span>
                                <span class="text-lg font-bold text-green-600">${{ number_format($user->total_earned ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reviews & Ratings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Seller Profile</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center mb-2">
                                <span class="text-sm text-gray-600">Rating:</span>
                                <span class="ml-2 text-lg font-bold text-yellow-500">
                                    {{ $user->average_rating ?? 'N/A' }} ⭐
                                </span>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600">Total Reviews</span>
                                <span class="text-lg font-bold text-gray-900">{{ $user->reviews_received_count ?? 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Completion Rate</span>
                                <span class="text-lg font-bold text-green-600">{{ $user->completion_rate ?? '0%' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="border-b border-gray-200">
                    <div class="flex gap-0">
                        <button onclick="switchTab('orders')" class="tab-btn active px-6 py-3 border-b-2 border-blue-500 text-blue-600 font-medium">
                            Orders
                        </button>
                        <button onclick="switchTab('reports')" class="tab-btn px-6 py-3 border-b-2 border-transparent text-gray-600 font-medium hover:text-gray-900">
                            Reports ({{ count($reports ?? []) }})
                        </button>
                    </div>
                </div>

                <!-- Orders Tab -->
                <div id="orders-tab" class="tab-content p-6">
                    @if(count($orders ?? []) > 0)
                        <div class="space-y-4">
                            @foreach($orders ?? [] as $order)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">Order #{{ $order->id }}</h4>
                                            <p class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y H:i') }}</p>
                                        </div>
                                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">
                                        @if(auth()->id() === $order->buyer_id)
                                            Service by: <strong>{{ $order->seller->firstname }} {{ $order->seller->lastname }}</strong>
                                        @else
                                            Buyer: <strong>{{ $order->buyer->firstname }} {{ $order->buyer->lastname }}</strong>
                                        @endif
                                    </p>
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="font-semibold text-gray-900">${{ number_format($order->total_amount, 2) }}</p>
                                            <p class="text-xs text-gray-500">
                                                Payment: 
                                                <span class="font-medium {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600' }}">
                                                    {{ ucfirst($order->payment_status) }}
                                                </span>
                                            </p>
                                        </div>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            No orders found
                        </div>
                    @endif
                </div>

                <!-- Reports Tab -->
                <div id="reports-tab" class="tab-content hidden p-6">
                    @if(count($reports ?? []) > 0)
                        <div class="space-y-4">
                            @foreach($reports ?? [] as $report)
                                <div class="border border-gray-200 rounded-lg p-4 {{ $report->status === 'pending' ? 'bg-yellow-50' : '' }}">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">Report #{{ $report->id }}</h4>
                                            <p class="text-sm text-gray-500">{{ $report->created_at->format('M d, Y H:i') }}</p>
                                        </div>
                                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($report->status === 'resolved' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">
                                        <strong>Reason:</strong> {{ $report->reason ?? 'N/A' }}
                                    </p>
                                    <p class="text-sm text-gray-600 mb-2">
                                        {{ $report->description }}
                                    </p>
                                    <div class="flex justify-between items-center">
                                        <p class="text-xs text-gray-500">
                                            Reported by: <strong>{{ $report->reporter->firstname ?? 'Unknown' }}</strong>
                                        </p>
                                        <a href="{{ route('admin.disputes.show', $report) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                            View Report
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            No reports filed against this user
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-600');
            });

            // Show selected tab
            document.getElementById(tabName + '-tab').classList.remove('hidden');
            event.target.classList.add('active', 'border-blue-500', 'text-blue-600');
            event.target.classList.remove('border-transparent', 'text-gray-600');
        }
    </script>
</x-app-layout>
