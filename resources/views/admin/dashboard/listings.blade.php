<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Listing Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Listing Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-600 text-sm">Total Listings</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $listing_stats['total_listings'] ?? 0 }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-600 text-sm">Active Listings</p>
                    <p class="text-3xl font-bold text-green-600">{{ $listing_stats['active_listings'] ?? 0 }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-600 text-sm">Inactive Listings</p>
                    <p class="text-3xl font-bold text-gray-600">{{ $listing_stats['inactive_listings'] ?? 0 }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-gray-600 text-sm">Average Price</p>
                    <p class="text-3xl font-bold text-purple-600">${{ number_format($listing_stats['avg_price'] ?? 0, 2) }}</p>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
                <form method="GET" class="flex gap-2">
                    <input type="text" name="search" placeholder="Search listings by title or seller..." 
                        value="{{ request('search') }}" 
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Search
                    </button>
                </form>
            </div>

            <!-- Listings Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Listing</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Seller</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Category</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Price</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Created</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Orders</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($listings as $listing)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex items-center gap-3">
                                        @if($listing->cover_image)
                                            <img src="{{ asset('storage/' . $listing->cover_image) }}" alt="{{ $listing->title }}" class="w-10 h-10 rounded object-cover">
                                        @else
                                            <div class="w-10 h-10 rounded bg-gray-300 flex items-center justify-center">📷</div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $listing->title }}</p>
                                            <p class="text-xs text-gray-500">ID: {{ $listing->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="{{ route('admin.users.show', $listing->seller) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        {{ $listing->seller->firstname }} {{ $listing->seller->lastname }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $listing->category->name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-sm font-bold text-gray-900">
                                    ${{ number_format($listing->price, 2) }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="inline-block px-2 py-1 rounded text-xs font-semibold {{ 
                                        $listing->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                                    }}">
                                        {{ ucfirst($listing->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $listing->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    {{ $listing->orders_count ?? 0 }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($listing->status === 'active')
                                        <form action="{{ route('admin.listings.deactivate', $listing) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Deactivate</button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.listings.reactivate', $listing) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800 font-medium">Reactivate</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                    No listings found
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-6 border-t border-gray-200">
                    {{ $listings->links() }}
                </div>
            </div>

            <!-- Category Breakdown -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Category Breakdown</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($category_stats ?? [] as $category => $count)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <p class="text-gray-600 text-sm">{{ $category }}</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $count }}</p>
                            <p class="text-xs text-gray-500 mt-2">
                                {{ round(($count / ($listing_stats['total_listings'] ?? 1)) * 100) }}% of total
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
