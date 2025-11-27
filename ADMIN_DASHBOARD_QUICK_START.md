# Admin Dashboard - Quick Start Guide

## Accessing the Admin Dashboard

### Requirements
1. User account with **admin** role
2. Authentication required
3. Spatie Permission roles enabled

### URLs
- **Dashboard Overview**: `http://localhost:8000/admin/` (handled by DashboardController)
- **Analytics**: `http://localhost:8000/admin/analytics`
- **Financial Reports**: `http://localhost:8000/admin/financial-reports`
- **User Management**: `http://localhost:8000/admin/users`
- **Dispute Resolution**: `http://localhost:8000/admin/disputes`
- **Listing Management**: `http://localhost:8000/admin/listings`

## Setting Up Admin User for Testing

### Option 1: Using Tinker
```bash
php artisan tinker
```

Then in Tinker:
```php
// Create or get a user
$user = User::firstOrCreate(['email' => 'admin@test.com'], [
    'firstname' => 'Admin',
    'lastname' => 'User',
    'email' => 'admin@test.com',
    'password' => Hash::make('password'),
    'email_verified_at' => now(),
]);

// Assign admin role
$user->assignRole('admin');

exit;
```

### Option 2: Using SQL
```sql
-- Add user
INSERT INTO users (firstname, lastname, email, password, email_verified_at, created_at, updated_at)
VALUES ('Admin', 'User', 'admin@test.com', 'hashed_password_here', now(), now(), now());

-- Assign role (if using Spatie roles)
-- First get user ID, then assign role
```

## Testing Workflow

### 1. Dashboard Overview
```
✅ Navigate to /admin/analytics
Expected:
- Quick stats cards showing totals
- Weekly and monthly performance
- Order, user, listing metrics
- Revenue analytics
```

### 2. Financial Reports
```
✅ Navigate to /admin/financial-reports
Expected:
- List of generated reports (initially empty)
- Button to generate new report
- Form to select month/year
- Export functionality for reports
```

Generate a Test Report:
```
1. Click "Generate Report"
2. Select current month and year
3. Click "Generate Report"
4. Verify report appears in list
5. Click "Export CSV" to download
```

### 3. User Management
```
✅ Navigate to /admin/users
Expected:
- List of all users with pagination
- Search functionality
- User role badges
- Status indicators (Active/Suspended/Pending)
- Action buttons (View, Suspend/Unsuspend)
```

Test User Suspension:
```
1. Click "Suspend" on any user
2. Enter reason and duration (1-365 days)
3. Click "Suspend User"
4. Verify status changes to "Suspended"
5. Click "Unsuspend" to remove suspension
```

### 4. User Details
```
✅ Navigate to /admin/users/{user_id}
Expected:
- User profile information
- Activity statistics
- Orders as buyer/seller
- Reports filed against user
- Reports filed by user
- Suspension history
```

### 5. Dispute Resolution
```
✅ Navigate to /admin/disputes
Expected:
- List of user reports with pagination
- Status badges (Pending/Resolved/Dismissed)
- Filter by status dropdown
- Report reason distribution
- Top report reasons listed
```

### 6. Report Details
```
✅ Navigate to /admin/disputes/{report_id}
Expected:
- Full report details and description
- Reported user profile card
- Reporter profile card
- Admin notes section
- Resolve/Dismiss action buttons (if pending)
```

Test Report Resolution:
```
1. Click "Resolve Report"
2. Enter admin notes
3. Click "Resolve"
4. Verify status changes to "Resolved"
5. Notes are preserved
```

### 7. Listing Management
```
✅ Navigate to /admin/listings
Expected:
- All service listings in table
- Search by title/seller
- Filter by status
- Category breakdown
- Price statistics
- Activate/Deactivate buttons
```

## Common Test Cases

### Authorization Tests
```bash
# Test without authentication
curl http://localhost:8000/admin/analytics
# Expected: Redirect to login

# Test with non-admin user
# Login as regular user, navigate to /admin/analytics
# Expected: 403 Forbidden error

# Test with admin user
# Login as admin, navigate to /admin/analytics
# Expected: Dashboard loads successfully
```

### Data Population Tests
```
1. Create test users via Tinker
2. Generate test orders
3. Create test listings
4. Create test user reports
5. Verify metrics calculate correctly
```

### Pagination Tests
```
1. Navigate to /admin/users?page=1
2. Verify 20 users per page
3. Click next page
4. Verify pagination links work
5. Test search with pagination
```

### Search & Filter Tests
```
Users:
- Search by name: /admin/users?search=John
- Search by email: /admin/users?search=john@example.com

Disputes:
- Filter by status: /admin/disputes?status=pending
- Filter resolved: /admin/disputes?status=resolved

Listings:
- Filter active: /admin/listings?status=active
- Search title: /admin/listings?search=design
```

## Troubleshooting

### Issue: "Unauthorized access to admin dashboard"
**Solution**: Verify user has admin role
```php
$user->roles; // Should show 'admin' role
$user->hasRole('admin'); // Should return true
```

### Issue: Views not found
**Solution**: Verify view files exist
```bash
ls resources/views/admin/dashboard/
# Should show all 8 blade.php files
```

### Issue: Database tables not created
**Solution**: Run migrations
```bash
php artisan migrate
# Should show 3 completed migrations
```

### Issue: Middleware not working
**Solution**: Verify middleware registration
```bash
# Check bootstrap/app.php has:
$middleware->alias([
    'admin' => \App\Http\Middleware\IsAdmin::class,
]);
```

### Issue: Route not found
**Solution**: Clear route cache
```bash
php artisan route:clear
php artisan route:cache
```

## Performance Testing

### Metrics Calculation
- Dashboard overview: Should load < 500ms
- Analytics page: Should load < 1s
- User list (20 per page): Should load < 500ms
- Reports list: Should load < 500ms

### Load Testing
```bash
# Test with more users
php artisan tinker
User::factory(1000)->create();

# Navigate to /admin/users and verify pagination works
# Monitor query performance
```

## Features to Test

### ✅ Dashboard
- [ ] Quick stats cards display correct totals
- [ ] Top performers list shows correct sellers
- [ ] Recent orders display latest transactions
- [ ] Navigation cards link correctly

### ✅ Analytics
- [ ] Weekly stats aggregate correctly
- [ ] Monthly stats show proper totals
- [ ] Order metrics breakdown accurate
- [ ] User demographics displayed
- [ ] Listing metrics calculated
- [ ] Revenue metrics showing correct fee calculation

### ✅ Financial Reports
- [ ] Generate report for selected month/year
- [ ] Reports display in list with correct data
- [ ] Fee percentage calculation correct (5%)
- [ ] GMV, fees, earnings, refunds calculated properly
- [ ] CSV export contains all report data
- [ ] Pagination works with multiple reports

### ✅ Users
- [ ] User list displays all users
- [ ] Search filters users correctly
- [ ] Role badges show correctly
- [ ] Status indicators accurate
- [ ] Suspend modal appears and submits
- [ ] User details page loads all info
- [ ] Orders tab shows buyer/seller orders
- [ ] Reports tab shows filed reports

### ✅ Disputes
- [ ] Reports list displays all reports
- [ ] Status filter works (pending/resolved/dismissed)
- [ ] Reason distribution accurate
- [ ] Report details page complete
- [ ] Resolve modal appears and saves notes
- [ ] Dismiss modal appears and saves reason
- [ ] Admin notes persist after save
- [ ] User report history shows all reports

### ✅ Listings
- [ ] All listings display with images
- [ ] Search finds listings by title/seller
- [ ] Status filter works
- [ ] Category breakdown shows percentages
- [ ] Deactivate button hides listing
- [ ] Reactivate button shows listing again

## Database Verification

### Check Table Structures
```sql
-- Platform stats table
PRAGMA table_info(platform_stats);

-- Financial reports table
PRAGMA table_info(financial_reports);

-- User reports table
PRAGMA table_info(user_reports);
```

### Sample Data Insertion
```sql
-- Insert test platform stat
INSERT INTO platform_stats (date, total_users, active_users, total_orders, total_revenue, platform_fees_collected)
VALUES (date('now'), 100, 45, 250, 5000.00, 250.00);

-- Insert test financial report
INSERT INTO financial_reports (report_period_start, report_period_end, total_gmv, total_platform_fees, total_seller_earnings, status)
VALUES (date('now', 'start of month'), date('now'), 10000.00, 500.00, 9500.00, 'completed');

-- Insert test user report
INSERT INTO user_reports (reported_user_id, reporter_id, reason, description, status)
VALUES (2, 1, 'inappropriate_content', 'User uploaded inappropriate images', 'pending');
```

## Monitoring & Logging

### View Application Logs
```bash
tail -f storage/logs/laravel.log
```

### Monitor Admin Actions
```bash
# Check activity log for admin actions
php artisan tinker
Activity::where('causer_id', 1)->get();
```

## Next: Setup Dashboard Automation (Optional)

### Scheduled Daily Stats Generation
Add to `app/Console/Kernel.php`:
```php
$schedule->call(function () {
    \App\Domains\Admin\Services\DashboardService::generateDailyStats(now());
})->daily();
```

### Email Alerts for Suspensions
Send email when user suspended - already coded, just requires email config.

---

**Last Updated**: 2025-11-26
**Status**: Phase 7 Complete - Ready for Testing
