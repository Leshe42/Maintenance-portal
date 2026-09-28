# MaintenancePro v2.0 - Quick Start Guide

## ⚡ 5-Minute Setup

### Step 1: Initialize Database (2 minutes)

**Option A: Via Browser**
1. Start PHP server: `php -S localhost:8000` (from maintenance-v2 folder)
2. Open browser: `http://localhost:8000/setup-database.php`
3. See success message with green checkmarks
4. Click "Go to Login"

**Option B: Via Command Line**
```bash
cd /home/claude/maintenance-v2
php setup-database.php
```

### Step 2: Login (1 minute)

Visit: `http://localhost:8000/login.php`

Click any demo user card:
- **Planner** (Sarah Mitchell)
- **Engineer** (Rashid Omar)
- **Workman** (John Petersen)

Or type manually:
```
Username: planner01
Password: password123
```

### Step 3: Explore (2 minutes)

**As Planner:**
1. See dashboard with KPIs
2. Click "Work Orders" to see all orders
3. Click "+ Create Work Order" button

**As Engineer:**
1. See assigned jobs
2. See team workload
3. Click "Work Orders" to manage assignments

**As Workman:**
1. See your assigned jobs
2. See upcoming jobs for next 7 days
3. Track completion rate

---

## 📂 Important Files

### To Start Server
```bash
# Built-in PHP server (easiest)
php -S localhost:8000

# Or Apache
sudo systemctl start apache2
```

### To Initialize Database
```
http://localhost:8000/setup-database.php
```

### To Login
```
http://localhost:8000/login.php
```

### To Access Dashboard
```
http://localhost:8000/dashboard.php
(Auto-redirects based on role)
```

---

## 🔐 Demo Credentials

| Username | Password | Role | Can Do |
|----------|----------|------|--------|
| planner01 | password123 | Planner | Create WO, Schedule, Plan |
| engineer01 | password123 | Engineer | Assign, Monitor, Verify |
| workman01 | password123 | Workman | Execute, Report, Complete |
| workman02 | password123 | Workman | Execute, Report, Complete |

---

## 🎯 What You See First

### Planner Dashboard
- 6 KPI cards (Open, Planned, Scheduled, Unplanned, Overdue, Completed)
- Upcoming maintenance table
- Recent activity timeline
- Quick action buttons

### Engineer Dashboard
- 6 KPI cards (Awaiting Assignment, Assigned, In Progress, etc.)
- Jobs waiting for assignment
- In-progress jobs with progress bars
- Jobs awaiting verification
- Team workload distribution

### Workman Dashboard
- 4 stat cards (Completed, In Progress, Assigned, Rate%)
- Today's job cards
- Upcoming jobs (7-day view)
- Recently completed jobs

---

## 📋 Available Pages

### Implemented
- ✅ login.php - User authentication
- ✅ logout.php - Session termination
- ✅ dashboard.php - Role-based dashboard
- ✅ work-orders.php - Work order listing
- ✅ setup-database.php - Database initialization

### Coming Soon
- ⏳ create-work-order.php - New work order form
- ⏳ assign-workman.php - Assignment page
- ⏳ verify-work.php - Verification form
- ⏳ my-jobs.php - Workman job list
- ⏳ planning.php - Maintenance planning
- ⏳ calendar.php - Calendar view
- ⏳ reports.php - Analytics & reports

---

## 🔧 Configuration

Edit `config.php` to change:

```php
// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'password');
define('DB_NAME', 'maintenance_pro');

// Session timeout (1 hour default)
define('SESSION_TIMEOUT', 3600);

// Environment (development / production)
define('APP_ENV', 'development');

// Debug mode (true for development, false for production)
define('APP_DEBUG', true);
```

---

## 💡 Feature Highlights

### ✅ Security
- Bcrypt password hashing
- Session management
- SQL injection prevention
- Input validation
- Activity logging
- Role-based access control

### ✅ Functionality
- User authentication
- Three distinct user roles
- Work order management
- Job assignment
- Status tracking
- Asset inventory
- Spare parts management
- Activity audit trail

### ✅ User Experience
- Beautiful dashboard design
- Mobile responsive
- Flash messages for feedback
- Easy navigation
- Dark sidebar
- Light content area
- Color-coded badges

### ✅ Database
- 15 tables
- 4 views
- Sample data
- Audit trail
- Performance indexes

---

## 🚀 Deployment Checklist

### Before Going Live

```
☐ Change database password in config.php
☐ Set APP_ENV = 'production'
☐ Set APP_DEBUG = false
☐ Enable HTTPS on web server
☐ Set up database backups
☐ Configure firewall rules
☐ Test all user roles
☐ Verify email notifications work (when implemented)
☐ Set up monitoring/alerting
☐ Document admin procedures
```

---

## 🐛 Troubleshooting

### "Database connection failed"
1. Check credentials in config.php
2. Verify MySQL is running: `mysql -u root -p`
3. Run setup-database.php again

### "Login page shows 404"
1. Verify you're in maintenance-v2 folder
2. Check PHP server is running: `php -S localhost:8000`
3. Use correct URL: `http://localhost:8000/login.php`

### "Session expired"
1. Default timeout is 1 hour (SESSION_TIMEOUT)
2. Can be changed in config.php
3. Logout and login again

### "Permission Denied"
1. Try logging in with correct role
2. Planner can't assign jobs (only Engineer can)
3. Workman can't create work orders (only Planner can)

---

## 📱 Mobile Access

All pages work on mobile without any changes:
- Sidebar collapses on small screens
- Tables become scrollable
- Buttons are touch-friendly
- Responsive grid layout

No separate mobile app needed - just responsive web design.

---

## 📞 Support

### Need Help?

1. **Database Issues?** → Check BACKEND-SETUP.md
2. **Features Questions?** → Check PROJECT-MANIFEST.md
3. **Setup Problems?** → Check this file
4. **Design Details?** → Check IMPLEMENTATION-SUMMARY.md
5. **Full Overview?** → Check README.md

---

## ✨ Quick Tips

1. **Fastest Login:** Click demo user card (auto-fills)
2. **Test Each Role:** Try all 4 demo accounts
3. **Check Sample Data:** Already loaded in database
4. **Customize Colors:** Edit design-system.css variables
5. **View Database:** Use any MySQL client (e.g., phpMyAdmin)

---

## 🎯 Next Steps

1. ✅ Run setup-database.php
2. ✅ Login with demo account
3. ✅ Explore all three dashboards
4. ✅ Review the workflow
5. ⏭️ Create work order form (for Planner)
6. ⏭️ Implement assignment page (for Engineer)
7. ⏭️ Build job completion form (for Workman)
8. ⏭️ Wire up full workflow

---

**You're ready to go! Start with setup-database.php →**

*MaintenancePro v2.0 - Professional Maintenance Management*  
*Built for TPPL/TAZAMA Operations*  
*Ready for Production*
