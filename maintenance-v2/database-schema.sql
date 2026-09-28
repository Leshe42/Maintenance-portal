-- ============================================================================
-- MaintenancePro v2.0 - Complete Database Schema
-- ============================================================================
-- Database for TPPL/TAZAMA Maintenance Management System
-- Supports: Planner, Engineer, Workman roles
-- ============================================================================

CREATE DATABASE IF NOT EXISTS maintenance_pro;
USE maintenance_pro;

-- ============================================================================
-- Users & Authentication
-- ============================================================================

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('ADMIN', 'PLANNER', 'ENGINEER', 'WORKMAN') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_role (role),
    INDEX idx_active (is_active)
);

-- ============================================================================
-- Assets & Equipment
-- ============================================================================

CREATE TABLE assets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    asset_code VARCHAR(50) UNIQUE NOT NULL,
    asset_name VARCHAR(100) NOT NULL,
    asset_type VARCHAR(50) NOT NULL,
    location VARCHAR(100) NOT NULL,
    manufacturer VARCHAR(100),
    model VARCHAR(100),
    serial_number VARCHAR(100),
    purchase_date DATE,
    warranty_expiry DATE,
    status ENUM('ACTIVE', 'INACTIVE', 'MAINTENANCE', 'RETIRED') DEFAULT 'ACTIVE',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_location (location)
);

-- ============================================================================
-- Maintenance Plans (Routine Schedules)
-- ============================================================================

CREATE TABLE maintenance_plans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    plan_code VARCHAR(50) UNIQUE NOT NULL,
    asset_id INT NOT NULL,
    maintenance_type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    frequency_days INT NOT NULL,
    last_performed DATE,
    next_due_date DATE,
    estimated_duration_hours DECIMAL(5,2),
    estimated_cost DECIMAL(10,2),
    created_by INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_asset (asset_id),
    INDEX idx_next_due (next_due_date),
    INDEX idx_active (is_active)
);

-- ============================================================================
-- Work Orders (Main Work Management)
-- ============================================================================

CREATE TABLE work_orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    work_order_number VARCHAR(50) UNIQUE NOT NULL,
    asset_id INT NOT NULL,
    maintenance_type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('NEW', 'PLANNED', 'SCHEDULED', 'ASSIGNED', 'IN_PROGRESS', 'COMPLETED', 'VERIFIED', 'CLOSED', 'ON_HOLD', 'CANCELLED') DEFAULT 'NEW',
    is_planned BOOLEAN DEFAULT FALSE,
    is_unplanned BOOLEAN DEFAULT FALSE,
    created_date DATE NOT NULL,
    created_by INT NOT NULL,
    planned_date DATE,
    scheduled_date DATE,
    start_time TIME,
    end_time TIME,
    assigned_to INT,
    completed_date DATE,
    verified_date DATE,
    verified_by INT,
    closed_date DATE,
    estimated_cost DECIMAL(10,2),
    actual_cost DECIMAL(10,2),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id),
    FOREIGN KEY (verified_by) REFERENCES users(id),
    INDEX idx_status (status),
    INDEX idx_asset (asset_id),
    INDEX idx_scheduled_date (scheduled_date),
    INDEX idx_assigned_to (assigned_to),
    INDEX idx_created_date (created_date)
);

-- ============================================================================
-- Job Cards (Work Execution)
-- ============================================================================

CREATE TABLE job_cards (
    id INT PRIMARY KEY AUTO_INCREMENT,
    job_card_number VARCHAR(50) UNIQUE NOT NULL,
    work_order_id INT NOT NULL,
    assigned_to INT NOT NULL,
    status ENUM('NEW', 'ASSIGNED', 'IN_PROGRESS', 'PAUSED', 'COMPLETED', 'VERIFIED', 'CLOSED') DEFAULT 'NEW',
    scheduled_start_time DATETIME,
    scheduled_end_time DATETIME,
    actual_start_time DATETIME,
    actual_end_time DATETIME,
    pause_count INT DEFAULT 0,
    total_pause_minutes INT DEFAULT 0,
    work_performed TEXT,
    findings TEXT,
    parts_used TEXT,
    remarks TEXT,
    created_by INT NOT NULL,
    verified_by INT,
    verified_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (work_order_id) REFERENCES work_orders(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (verified_by) REFERENCES users(id),
    INDEX idx_status (status),
    INDEX idx_assigned_to (assigned_to),
    INDEX idx_work_order (work_order_id),
    INDEX idx_scheduled_start (scheduled_start_time)
);

-- ============================================================================
-- Workman Assignments (Assignment History & Conflicts)
-- ============================================================================

CREATE TABLE workman_assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    work_order_id INT NOT NULL,
    job_card_id INT,
    assigned_to INT NOT NULL,
    assigned_by INT NOT NULL,
    scheduled_start DATETIME NOT NULL,
    scheduled_end DATETIME NOT NULL,
    assignment_status ENUM('ASSIGNED', 'CONFIRMED', 'COMPLETED', 'CANCELLED') DEFAULT 'ASSIGNED',
    conflict_checked BOOLEAN DEFAULT FALSE,
    has_conflict BOOLEAN DEFAULT FALSE,
    conflict_details TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (work_order_id) REFERENCES work_orders(id),
    FOREIGN KEY (job_card_id) REFERENCES job_cards(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id),
    FOREIGN KEY (assigned_by) REFERENCES users(id),
    INDEX idx_assigned_to (assigned_to),
    INDEX idx_scheduled_start (scheduled_start),
    INDEX idx_assignment_status (assignment_status)
);

-- ============================================================================
-- Maintenance Verifications (Quality Control)
-- ============================================================================

CREATE TABLE maintenance_verifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    job_card_id INT NOT NULL,
    verified_by INT NOT NULL,
    verification_date DATETIME NOT NULL,
    is_approved BOOLEAN,
    approval_notes TEXT,
    rejection_reason TEXT,
    re_work_required BOOLEAN DEFAULT FALSE,
    re_work_notes TEXT,
    verified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_card_id) REFERENCES job_cards(id),
    FOREIGN KEY (verified_by) REFERENCES users(id),
    INDEX idx_verified_by (verified_by),
    INDEX idx_verification_date (verification_date)
);

-- ============================================================================
-- Maintenance History (Audit Trail & Archive)
-- ============================================================================

CREATE TABLE maintenance_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    work_order_id INT NOT NULL,
    job_card_id INT,
    asset_id INT NOT NULL,
    activity_type VARCHAR(50) NOT NULL,
    description TEXT,
    performed_by INT,
    start_time DATETIME,
    end_time DATETIME,
    duration_minutes INT,
    work_performed TEXT,
    findings TEXT,
    parts_used TEXT,
    cost DECIMAL(10,2),
    next_maintenance_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (work_order_id) REFERENCES work_orders(id),
    FOREIGN KEY (job_card_id) REFERENCES job_cards(id),
    FOREIGN KEY (asset_id) REFERENCES assets(id),
    FOREIGN KEY (performed_by) REFERENCES users(id),
    INDEX idx_asset (asset_id),
    INDEX idx_created_at (created_at),
    INDEX idx_performed_by (performed_by)
);

-- ============================================================================
-- Spare Parts & Inventory
-- ============================================================================

CREATE TABLE spare_parts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    part_code VARCHAR(50) UNIQUE NOT NULL,
    part_name VARCHAR(100) NOT NULL,
    description TEXT,
    asset_id INT,
    quantity_on_hand INT DEFAULT 0,
    quantity_reserved INT DEFAULT 0,
    reorder_level INT,
    unit_cost DECIMAL(10,2),
    supplier VARCHAR(100),
    supplier_code VARCHAR(50),
    lead_time_days INT,
    location VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (asset_id) REFERENCES assets(id),
    INDEX idx_part_code (part_code),
    INDEX idx_quantity_on_hand (quantity_on_hand)
);

-- ============================================================================
-- Parts Used in Job Cards
-- ============================================================================

CREATE TABLE job_card_parts_used (
    id INT PRIMARY KEY AUTO_INCREMENT,
    job_card_id INT NOT NULL,
    spare_part_id INT NOT NULL,
    quantity_used INT NOT NULL,
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_card_id) REFERENCES job_cards(id) ON DELETE CASCADE,
    FOREIGN KEY (spare_part_id) REFERENCES spare_parts(id),
    INDEX idx_job_card (job_card_id)
);

-- ============================================================================
-- Activity Logs (System Audit Trail)
-- ============================================================================

CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_user (user_id),
    INDEX idx_created_at (created_at),
    INDEX idx_action (action)
);

-- ============================================================================
-- System Settings
-- ============================================================================

CREATE TABLE system_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value VARCHAR(255),
    data_type VARCHAR(20),
    description TEXT,
    is_editable BOOLEAN DEFAULT TRUE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================================================
-- SAMPLE DATA
-- ============================================================================

-- Users (4 demo users)
INSERT INTO users (username, email, password_hash, full_name, role) VALUES
('planner01', 'sarah.mitchell@tppl.com', '$2y$10$YourHashedPasswordHere1', 'Sarah Mitchell', 'PLANNER'),
('engineer01', 'rashid.omar@tppl.com', '$2y$10$YourHashedPasswordHere2', 'Rashid Omar', 'ENGINEER'),
('workman01', 'john.petersen@tppl.com', '$2y$10$YourHashedPasswordHere3', 'John Petersen', 'WORKMAN'),
('workman02', 'paul.mulenga@tppl.com', '$2y$10$YourHashedPasswordHere4', 'Paul Mulenga', 'WORKMAN'),
('admin01', 'admin@tppl.com', '$2y$10$YourHashedPasswordHere5', 'System Administrator', 'ADMIN');

-- Assets (5 sample equipment)
INSERT INTO assets (asset_code, asset_name, asset_type, location, manufacturer, model, serial_number) VALUES
('PUMP-001', 'Pump A-01', 'Pump', 'Building 1', 'Grundfos', 'NB 50-250', 'SN-10001'),
('MOTOR-001', 'Motor B-03', 'Motor', 'Building 2', 'Siemens', '1LE1 250M', 'SN-10002'),
('CONV-001', 'Conveyor C', 'Conveyor', 'Building 1', 'Fenner', 'PLC', 'SN-10003'),
('COMP-001', 'Compressor E-02', 'Compressor', 'Building 3', 'Atlas Copco', 'GA18VSD', 'SN-10004'),
('GEN-001', 'Generator G-01', 'Generator', 'Building 4', 'Cummins', 'C250D5', 'SN-10005');

-- Maintenance Plans (routine schedules)
INSERT INTO maintenance_plans (plan_code, asset_id, maintenance_type, description, frequency_days, next_due_date, created_by) VALUES
('PLAN-001', 1, 'Oil Change', 'Change oil and filter on Pump A-01', 90, '2026-10-15', 1),
('PLAN-002', 2, 'Bearing Inspection', 'Inspect and lubricate motor bearings', 180, '2026-11-20', 1),
('PLAN-003', 3, 'Belt Inspection', 'Inspect conveyor belt for wear and tension', 60, '2026-10-30', 1),
('PLAN-004', 4, 'Valve Inspection', 'Inspect compressor intake valve', 120, '2026-12-10', 1),
('PLAN-005', 5, 'Fuel Filter', 'Replace fuel filter on generator', 365, '2027-09-20', 1);

-- Work Orders (5 sample orders in various statuses)
INSERT INTO work_orders (work_order_number, asset_id, maintenance_type, description, status, created_date, created_by, scheduled_date, start_time, end_time) VALUES
('WO-2026-001', 1, 'Oil Change', 'Scheduled oil change and filter replacement', 'SCHEDULED', '2026-09-20', 1, '2026-09-25', '08:00:00', '10:00:00'),
('WO-2026-002', 2, 'Bearing Inspection', 'Inspect and lubricate motor bearings', 'SCHEDULED', '2026-09-21', 1, '2026-09-26', '14:00:00', '16:00:00'),
('WO-2026-003', 5, 'Battery Replacement', 'Replace UPS battery pack', 'IN_PROGRESS', '2026-09-22', 1, '2026-09-24', '08:15:00', '10:45:00'),
('WO-2026-004', 3, 'Belt Replacement', 'Complete conveyor belt replacement', 'PLANNED', '2026-09-23', 1, NULL, NULL, NULL),
('WO-2026-005', 4, 'Annual Inspection', 'Compressor annual safety inspection', 'VERIFIED', '2026-09-20', 1, '2026-09-23', '09:00:00', '12:00:00');

-- Spare Parts (stock items)
INSERT INTO spare_parts (part_code, part_name, description, quantity_on_hand, reorder_level, unit_cost, supplier) VALUES
('PUMP-OIL-01', 'Hydraulic Oil', 'Premium grade hydraulic oil, 20L', 5, 2, 450.00, 'Shell'),
('MOTOR-BEARING-01', 'Ball Bearing', 'Deep groove ball bearing 6309', 12, 5, 150.00, 'SKF'),
('CONV-BELT-01', 'Conveyor Belt', 'PVC conveyor belt, 500mm width', 2, 1, 2500.00, 'Fenner'),
('COMP-VALVE-01', 'Intake Valve', 'Compressor intake valve assembly', 3, 1, 800.00, 'Atlas Copco'),
('GEN-FUEL-01', 'Fuel Filter', 'High efficiency fuel filter', 8, 3, 75.00, 'Cummins');

-- System Settings
INSERT INTO system_settings (setting_key, setting_value, data_type, description) VALUES
('app_name', 'MaintenancePro', 'string', 'Application name'),
('app_version', '2.0', 'string', 'Application version'),
('max_workload_hours', '40', 'integer', 'Maximum weekly hours per workman'),
('auto_overdue_days', '7', 'integer', 'Days to mark maintenance as overdue'),
('enable_notifications', '1', 'boolean', 'Enable email notifications');

-- ============================================================================
-- VIEWS FOR COMMON QUERIES
-- ============================================================================

CREATE VIEW v_work_orders_summary AS
SELECT 
    wo.id,
    wo.work_order_number,
    a.asset_name,
    a.asset_code,
    wo.maintenance_type,
    wo.status,
    wo.scheduled_date,
    wo.start_time,
    wo.end_time,
    u.full_name as created_by_name,
    wm.full_name as assigned_to_name,
    a.location
FROM work_orders wo
JOIN assets a ON wo.asset_id = a.id
JOIN users u ON wo.created_by = u.id
LEFT JOIN users wm ON wo.assigned_to = wm.id
ORDER BY wo.scheduled_date DESC;

CREATE VIEW v_job_cards_summary AS
SELECT 
    jc.id,
    jc.job_card_number,
    wo.work_order_number,
    a.asset_name,
    jc.status,
    u.full_name as assigned_to_name,
    jc.scheduled_start_time,
    jc.scheduled_end_time,
    jc.actual_start_time,
    jc.actual_end_time
FROM job_cards jc
JOIN work_orders wo ON jc.work_order_id = wo.id
JOIN assets a ON wo.asset_id = a.id
JOIN users u ON jc.assigned_to = u.id
ORDER BY jc.scheduled_start_time DESC;

CREATE VIEW v_overdue_maintenance AS
SELECT 
    mp.id,
    mp.plan_code,
    a.asset_name,
    mp.description,
    mp.next_due_date,
    DATEDIFF(CURDATE(), mp.next_due_date) as days_overdue
FROM maintenance_plans mp
JOIN assets a ON mp.asset_id = a.id
WHERE mp.next_due_date < CURDATE()
AND mp.is_active = TRUE
ORDER BY mp.next_due_date ASC;

CREATE VIEW v_team_workload AS
SELECT 
    u.id,
    u.full_name,
    COUNT(CASE WHEN jc.status = 'IN_PROGRESS' THEN 1 END) as in_progress_count,
    COUNT(CASE WHEN jc.status = 'ASSIGNED' THEN 1 END) as assigned_count,
    COUNT(CASE WHEN jc.status = 'COMPLETED' THEN 1 END) as completed_count,
    COUNT(DISTINCT jc.id) as total_jobs
FROM users u
LEFT JOIN job_cards jc ON u.id = jc.assigned_to AND jc.status IN ('ASSIGNED', 'IN_PROGRESS', 'COMPLETED')
WHERE u.role = 'WORKMAN'
GROUP BY u.id, u.full_name;

-- ============================================================================
-- INDEXES FOR PERFORMANCE
-- ============================================================================

-- Additional useful indexes
CREATE INDEX idx_work_orders_status_date ON work_orders(status, scheduled_date);
CREATE INDEX idx_job_cards_assigned_status ON job_cards(assigned_to, status);
CREATE INDEX idx_maintenance_history_asset_date ON maintenance_history(asset_id, created_at);

-- ============================================================================
-- END OF SCHEMA
-- ============================================================================
