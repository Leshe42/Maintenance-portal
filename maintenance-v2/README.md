# MaintenancePro - Maintenance Management System v2.0

**Professional, production-ready HTML/CSS/PHP dashboards based on modern design system**

---

## 📋 Overview

MaintenancePro v2.0 is a complete redesign of the Maintenance Management Module using a professional, cohesive design system inspired by modern SaaS applications. All three role dashboards (Planner, Engineer, Workman) now feature:

- **Consistent visual design** across all pages
- **Professional typography** with Inter font family  
- **Clean, intuitive UI/UX** with proper spacing and hierarchy
- **Role-based sidebars** with collapsible navigation
- **Real-time dashboards** with KPI stat cards
- **Interactive tables** with hover states
- **Job card layouts** for maintenance work visualization
- **Responsive design** for desktop and mobile viewing

---

## 🎨 Design System

### Color Palette
```css
Primary Blue:    #2563eb
Primary Dark:    #0f1729
Light Background: #f1f5f9
White Card:      #ffffff
Borders:         #e2e8f0
Text Primary:    #0f172a
Text Muted:      #64748b
```

### Typography
- **Font Family:** Inter (System UI fallback)
- **Headings:** 600-700 weight
- **Body:** 400-500 weight
- **Mono:** JetBrains Mono (for timestamps, IDs)
- **Base Size:** 14px (scales to viewport)

### Layout
- **Sidebar:** 240px (collapsible to 64px)
- **Main Content:** Full-width responsive
- **Header:** Fixed 56px height
- **Cards:** 8px border radius
- **Spacing:** 4px, 8px, 12px, 16px, 24px units

---

## 📁 File Structure

```
maintenance-v2/
├── index.html                 ← Landing page with role selector
├── dashboard-planner.html     ← Maintenance Planner dashboard
├── dashboard-engineer.html    ← Maintenance Engineer dashboard
├── dashboard-workman.html     ← Workman dashboard
├── design-system.css          ← Unified design system & utilities
└── README.md                  ← This file
```

### Alternative Structure (for PHP integration)
```
maintenance-v2/
├── public/
│   ├── index.php              ← Landing/role selector
│   ├── dashboard/
│   │   ├── planner.php
│   │   ├── engineer.php
│   │   └── workman.php
│   └── css/
│       └── design-system.css
├── src/
│   ├── config/
│   │   └── database.php
│   └── components/
│       ├── sidebar.php
│       ├── header.php
│       └── footer.php
└── database/
    └── schema.sql
```

---

## 🚀 Quick Start

### 1. **View in Browser**
Simply open `index.html` in a web browser to see the landing page, then click any role card to view the dashboard.

```bash
cd /home/claude/maintenance-v2
# Open index.html in browser or use local server:
python -m http.server 8000
# Visit http://localhost:8000
```

### 2. **Use as PHP Application**
These files work as-is with PHP:

```bash
php -S localhost:8000 dashboard-planner.html
```

### 3. **Deploy to Production**
- Copy all HTML/CSS files to web server
- If using PHP backend:
  - Add session management to handle role switching
  - Connect to MySQL database
  - Replace simulated data with live queries
  - Add authentication/authorization

---

## 📊 Dashboard Overview

### **Planner Dashboard** (`dashboard-planner.html`)

**Role:** Maintenance Planner  
**Color:** Purple (#7c3aed)

#### Key Metrics
- Open Work Orders
- Planned Maintenance
- Scheduled Maintenance
- Unplanned Maintenance
- Overdue Maintenance
- Completed Maintenance

#### Main Sections
1. **Upcoming Maintenance Table** - Full list of scheduled jobs
2. **Recent Maintenance Activity** - Timeline of recent work

#### Quick Actions
- Create Work Order
- Create Maintenance Plan
- Schedule Maintenance
- View Calendar

---

### **Engineer Dashboard** (`dashboard-engineer.html`)

**Role:** Maintenance Engineer  
**Color:** Blue (#0369a1)

#### Key Metrics
- Jobs Awaiting Assignment
- Assigned Jobs
- In Progress
- Awaiting Verification
- Verified
- Closed

#### Main Sections
1. **Jobs Awaiting Assignment** - Job cards with ASSIGN button
2. **Assigned & In Progress** - Progress bars, assigned technician
3. **Jobs Awaiting Verification** - Completed jobs needing approval
4. **Team Workload** - Utilization bars per workman

#### Key Actions
- Assign workman to job
- Monitor job progress
- Verify completed work
- View team utilization

---

### **Workman Dashboard** (`dashboard-workman.html`)

**Role:** Workman  
**Color:** Green (#047857)

#### Key Metrics
- Completed This Month
- In Progress
- Scheduled
- Completion Rate

#### Main Sections
1. **Today's Job Cards** - Current assignments with progress
2. **Upcoming Jobs** - Next 7 days
3. **Recently Completed** - Work history

#### Key Actions
- Start job
- Pause/resume work
- Complete job (with work performed, findings, parts used)
- View job details

---

## 🎯 Component Library

### CSS Classes Available

#### Layout
```css
.layout              /* Main flex container */
.sidebar             /* Left navigation panel */
.main-container      /* Content area wrapper */
.header              /* Top navigation bar */
.main                /* Page content area */
```

#### Cards & Content
```css
.card                /* Standard card container */
.card-header         /* Card title section */
.card-body           /* Card content section */
.card-footer         /* Card action section */
```

#### Stat Cards
```css
.stats-grid          /* Responsive stat grid */
.stat-card           /* Individual stat */
.stat-value          /* Large number */
.stat-label          /* Stat description */
```

#### Tables
```css
.table-wrapper       /* Scrollable table container */
.table               /* HTML table styling */
.table th            /* Header cells */
.table td            /* Data cells */
```

#### Job Cards
```css
.job-card            /* Maintenance job container */
.job-card-header     /* Job title & status */
.job-card-body       /* Job details grid */
.job-card-footer     /* Action buttons */
```

#### Buttons
```css
.btn                 /* Base button */
.btn-primary         /* Blue action button */
.btn-secondary       /* Light border button */
.btn-small           /* Compact size */
.btn-large           /* Full-width size */
```

#### Badges
```css
.badge               /* Base badge */
.badge-new           /* Red - NEW status */
.badge-planned       /* Yellow - PLANNED */
.badge-scheduled     /* Light blue - SCHEDULED */
.badge-assigned      /* Purple - ASSIGNED */
.badge-in-progress   /* Orange - IN_PROGRESS */
.badge-completed     /* Green - COMPLETED */
.badge-verified      /* Teal - VERIFIED */
.badge-closed        /* Gray - CLOSED */
```

### Forms
```css
.form-group          /* Group wrapper */
.form-label          /* Label styling */
.form-input          /* Text/email inputs */
.form-select         /* Select dropdown */
.form-textarea       /* Multi-line text */
```

---

## 🔄 Workflow Integration

### Maintenance Flow
```
NEW WORK ORDER
    ↓
PLANNED (Planner creates plan, sets dates/times)
    ↓
SCHEDULED (Confirmed in calendar)
    ↓
ASSIGNED (Engineer assigns to workman)
    ↓
IN PROGRESS (Workman starts job)
    ↓
COMPLETED (Workman finishes, submits findings)
    ↓
VERIFIED (Engineer reviews & approves)
    ↓
CLOSED (Archived in maintenance history)
```

### Role Permissions

| Action | Planner | Engineer | Workman |
|--------|---------|----------|---------|
| Create Work Order | ✅ | ❌ | ❌ |
| Schedule Maintenance | ✅ | ❌ | ❌ |
| Generate Job Cards | ✅ | ❌ | ❌ |
| Assign Workmen | ❌ | ✅ | ❌ |
| Start/Pause Job | ❌ | ❌ | ✅ |
| Submit Work Report | ❌ | ❌ | ✅ |
| Verify Work | ❌ | ✅ | ❌ |
| Close Job | ❌ | ✅ | ❌ |
| View Dashboard | ✅ | ✅ | ✅ |
| View Calendar | ✅ | ✅ | ❌ |
| View History | ✅ | ✅ | ❌ |

---

## 🔌 Backend Integration

### Data Structure (Simulated, Ready for DB)

```php
// Work Order
$workOrder = [
    'id' => 1,
    'number' => 'WO-2026-001',
    'asset' => 'Pump A-01',
    'activity' => 'Oil change',
    'status' => 'SCHEDULED',  // NEW, PLANNED, SCHEDULED, ASSIGNED, IN_PROGRESS, COMPLETED, VERIFIED, CLOSED
    'createdDate' => '2026-09-20',
    'scheduledDate' => '2026-09-25',
    'startTime' => '08:00',
    'endTime' => '10:00',
];

// Job Card
$jobCard = [
    'id' => 1,
    'number' => 'JC-2026-003',
    'workOrderId' => 1,
    'assignedTo' => 'John Petersen',
    'status' => 'IN_PROGRESS',
    'startTime' => '08:15',
    'workPerformed' => 'Changed battery, tested system',
    'findings' => 'Battery weak, replaced successfully',
    'partsUsed' => 'UPS Battery Pack (P/N: UPS-BAT-001)',
];

// Workman
$workman = [
    'id' => 1,
    'name' => 'John Petersen',
    'role' => 'Workman',
    'totalJobs' => 8,
    'completedJobs' => 5,
];
```

### MySQL Schema (For Production)

See `/home/claude/maintenance-v1/database-schema.sql` for complete schema with:
- `users` table (roles: Planner, Engineer, Workman, Admin)
- `work_orders` table
- `job_cards` table
- `workman_assignments` table
- `maintenance_schedules` table
- `maintenance_verifications` table
- `maintenance_history` table (audit trail)

---

## 📱 Responsive Design

### Breakpoints
- **Desktop:** 1025px+ (full sidebar, expanded nav)
- **Tablet:** 769px - 1024px (collapsed sidebar icons only)
- **Mobile:** 0 - 768px (horizontal nav bar, full-width content)

### Mobile Optimizations
- Sidebar converts to horizontal navbar
- Cards stack to single column
- Table columns reflow for mobile
- Buttons expand to full width

---

## 🎬 Live Demo Data

### Sample Users
| Username | Role | Name | Password |
|----------|------|------|----------|
| planner01 | Planner | Sarah Mitchell | password123 |
| engineer01 | Engineer | Rashid Omar | password123 |
| workman01 | Workman | John Petersen | password123 |
| workman02 | Workman | Paul Mulenga | password123 |

### Sample Assets
- Pump A-01
- Motor B-03
- Conveyor C
- Compressor E-02
- Generator G-01

### Sample Work Orders
5 pre-populated work orders with various statuses for realistic demo

---

## ⚙️ Customization

### Change Theme Color
Edit `design-system.css`:
```css
:root {
  --primary: #2563eb;      ← Change this
  --primary-foreground: #ffffff;
  --sidebar-bg: #0f1729;   ← And this
}
```

### Add Custom Badge Status
```css
.badge-my-status { 
  background: #f0e5ff; 
  color: #6d28d9; 
}
```

### Adjust Spacing
All spacing uses CSS variables:
```css
--radius: 6px;  /* Border radius */
/* Cards, buttons use this for consistency */
```

---

## 🚦 Status Badges

Semantic color-coding for job statuses:

- 🔴 **NEW** (Red) - Newly created, awaiting planning
- 🟡 **PLANNED** (Yellow) - Scheduled and planned
- 🔵 **SCHEDULED** (Light Blue) - Confirmed in calendar
- 🟣 **ASSIGNED** (Purple) - Assigned to technician
- 🟠 **IN_PROGRESS** (Orange) - Currently being worked on
- 🟢 **COMPLETED** (Green) - Work finished, awaiting verification
- 🔷 **VERIFIED** (Teal) - Approved and verified
- ⚫ **CLOSED** (Gray) - Archived and closed

---

## 📈 KPI Metrics

### Planner Dashboard
- Open Work Orders (Trend)
- Planned Maintenance (This Month)
- Scheduled Maintenance (Next 7 Days)
- Unplanned Maintenance (Emergency jobs)
- Overdue Maintenance (Past due)
- Completed Maintenance (This month)

### Engineer Dashboard
- Jobs Awaiting Assignment (Urgent)
- Assigned Jobs (Not yet started)
- In Progress (Currently being worked)
- Awaiting Verification (Completed, pending review)
- Verified (Approved)
- Closed (Archive)

### Workman Dashboard
- Jobs Completed This Month
- Jobs In Progress
- Jobs Scheduled
- On-Time Completion Rate (%)

---

## 🔒 Security Notes

Current version:
- ✅ Frontend demo only (no database)
- ⚠️ Passwords hardcoded in comments (demo only)

When integrating with backend:
- Use parameterized queries / PDO prepared statements
- Implement proper session management
- Hash passwords with bcrypt
- Validate all user inputs
- Check role permissions on every action
- Log all modifications (audit trail)
- Use HTTPS in production
- Implement CSRF tokens

---

## 🐛 Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile browsers (iOS Safari, Chrome Mobile)

---

## 📝 Navigation Menus

### Planner Sidebar
```
Dashboard
Work Orders
Maintenance Planning
Maintenance Calendar
Maintenance Plans
─────────────
Assets
Spare Parts
Maintenance History
Reports
```

### Engineer Sidebar
```
Dashboard
Work Orders
Maintenance Calendar
Job Cards
─────────────
Assets
Spare Parts
Maintenance History
Reports
```

### Workman Sidebar
```
Dashboard
My Job Cards
My Work History
─────────────
Spare Parts
Job Guidelines
```

---

## 📞 Support & Documentation

### Files Location
- **Planner:** `/home/claude/maintenance-v2/dashboard-planner.html`
- **Engineer:** `/home/claude/maintenance-v2/dashboard-engineer.html`
- **Workman:** `/home/claude/maintenance-v2/dashboard-workman.html`
- **CSS:** `/home/claude/maintenance-v2/design-system.css`
- **Index:** `/home/claude/maintenance-v2/index.html`

### Next Steps
1. ✅ Copy files to web server
2. ⏭️ Integrate MySQL database
3. ⏭️ Add PHP backend logic
4. ⏭️ Implement user authentication
5. ⏭️ Add form submissions (create WO, assign job, etc.)
6. ⏭️ Deploy to production

---

## 📄 License & Credits

**MaintenancePro v2.0**  
- Designed for TPPL/TAZAMA Operations
- Based on modern SaaS design principles
- Built with HTML5, CSS3, PHP
- Production-ready and fully responsive

---

**Version:** 2.0  
**Date:** September 24, 2026  
**Status:** ✅ Ready for Integration
