-- ============================================================
-- MessSaaS Database Schema
-- Engine: InnoDB | Charset: utf8mb4
-- ============================================================

CREATE DATABASE IF NOT EXISTS mess_saas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mess_saas;

-- ------------------------------------------------------------
-- 1. subscription_plans (reference table, no tenant_id)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS subscription_plans (
    plan_id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name           VARCHAR(100) NOT NULL,
    price_monthly  DECIMAL(10,2) DEFAULT 0,
    price_yearly   DECIMAL(10,2) DEFAULT 0,
    max_students   INT DEFAULT 0 COMMENT '0=unlimited',
    max_coordinators INT DEFAULT 2,
    storage_mb     INT DEFAULT 500,
    features       JSON,
    is_active      TINYINT(1) DEFAULT 1,
    created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 2. tenants
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tenants (
    tenant_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name           VARCHAR(150) NOT NULL,
    owner_name     VARCHAR(150),
    slug           VARCHAR(100) NOT NULL UNIQUE,
    logo           VARCHAR(255),
    primary_color  VARCHAR(10) DEFAULT '#6366f1',
    secondary_color VARCHAR(10) DEFAULT '#06b6d4',
    contact_email  VARCHAR(150),
    contact_phone  VARCHAR(20),
    address        TEXT,
    city           VARCHAR(100),
    state          VARCHAR(100),
    pincode        VARCHAR(10),
    plan_id        INT UNSIGNED,
    storage_used_mb INT DEFAULT 0,
    status         ENUM('active','inactive','suspended') DEFAULT 'active',
    created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(plan_id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 3. tenant_subscriptions
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tenant_subscriptions (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id      INT UNSIGNED NOT NULL,
    plan_id        INT UNSIGNED NOT NULL,
    billing_cycle  ENUM('monthly','yearly') DEFAULT 'monthly',
    starts_at      DATE NOT NULL,
    expires_at     DATE NOT NULL,
    status         ENUM('active','expired','cancelled') DEFAULT 'active',
    payment_ref    VARCHAR(100),
    amount_paid    DECIMAL(10,2) DEFAULT 0,
    created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id)   REFERENCES subscription_plans(plan_id),
    INDEX idx_tenant (tenant_id),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 4. feature_modules
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS feature_modules (
    module_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    slug        VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon        VARCHAR(100) DEFAULT 'bi-puzzle',
    is_core     TINYINT(1) DEFAULT 0 COMMENT '1=always enabled',
    version     VARCHAR(20) DEFAULT '1.0.0',
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 5. tenant_modules
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS tenant_modules (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id   INT UNSIGNED NOT NULL,
    module_id   INT UNSIGNED NOT NULL,
    is_enabled  TINYINT(1) DEFAULT 1,
    enabled_at  DATETIME,
    updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_tenant_module (tenant_id, module_id),
    FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id) ON DELETE CASCADE,
    FOREIGN KEY (module_id) REFERENCES feature_modules(module_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 6. roles
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS roles (
    role_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id  INT UNSIGNED NULL COMMENT 'NULL=system role',
    name       VARCHAR(100) NOT NULL,
    slug       VARCHAR(100) NOT NULL,
    is_system  TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_role_slug_tenant (slug, tenant_id),
    INDEX idx_tenant (tenant_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 7. permissions
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS permissions (
    permission_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    module_id     INT UNSIGNED,
    name          VARCHAR(150) NOT NULL,
    slug          VARCHAR(150) NOT NULL UNIQUE,
    description   VARCHAR(255),
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (module_id) REFERENCES feature_modules(module_id) ON DELETE SET NULL,
    INDEX idx_slug (slug)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 8. role_permissions
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS role_permissions (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id       INT UNSIGNED NOT NULL,
    permission_id INT UNSIGNED NOT NULL,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_rp (role_id, permission_id),
    FOREIGN KEY (role_id)       REFERENCES roles(role_id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(permission_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 9. users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    user_id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id     INT UNSIGNED NULL COMMENT 'NULL=super admin',
    role_id       INT UNSIGNED NOT NULL,
    email         VARCHAR(180) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name     VARCHAR(150) NOT NULL,
    phone         VARCHAR(20),
    avatar        VARCHAR(255),
    status        ENUM('active','inactive','banned') DEFAULT 'active',
    last_login_at DATETIME,
    login_attempts TINYINT DEFAULT 0,
    locked_until  DATETIME,
    created_by    INT UNSIGNED,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_email_tenant (email, tenant_id),
    FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id) ON DELETE CASCADE,
    FOREIGN KEY (role_id)   REFERENCES roles(role_id),
    INDEX idx_tenant         (tenant_id),
    INDEX idx_email_status   (email, status),
    INDEX idx_tenant_role    (tenant_id, role_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 10. mess_settings (key-value per tenant)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS mess_settings (
    setting_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id     INT UNSIGNED NOT NULL,
    setting_key   VARCHAR(100) NOT NULL,
    setting_value TEXT,
    setting_group VARCHAR(50) DEFAULT 'general',
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_tenant_key (tenant_id, setting_key),
    FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id) ON DELETE CASCADE,
    INDEX idx_group (tenant_id, setting_group)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 11. meal_slots (dynamic per tenant)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS meal_slots (
    slot_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id    INT UNSIGNED NOT NULL,
    name         VARCHAR(100) NOT NULL COMMENT 'e.g. Breakfast, Lunch, Dinner',
    slot_time    VARCHAR(50) COMMENT 'e.g. 07:30 - 09:00',
    meal_type    ENUM('breakfast','lunch','snacks','dinner','other') DEFAULT 'other',
    sort_order   TINYINT DEFAULT 0,
    is_active    TINYINT(1) DEFAULT 1,
    created_by   INT UNSIGNED,
    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id) ON DELETE CASCADE,
    INDEX idx_tenant (tenant_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 12. students
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS students (
    student_id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id         INT UNSIGNED NOT NULL,
    user_id           INT UNSIGNED NULL COMMENT 'If student login enabled',
    reg_number        VARCHAR(50),
    full_name         VARCHAR(150) NOT NULL,
    phone             VARCHAR(20),
    email             VARCHAR(180),
    guardian_name     VARCHAR(150),
    guardian_phone    VARCHAR(20),
    blood_group       VARCHAR(10),
    address           TEXT,
    dob               DATE,
    gender            ENUM('male','female','other'),
    emergency_contact VARCHAR(20),
    id_proof_type     VARCHAR(50),
    id_proof_path     VARCHAR(255),
    photo_path        VARCHAR(255),
    room_number       VARCHAR(20),
    status            ENUM('active','inactive','left') DEFAULT 'active',
    joined_at         DATE,
    left_at           DATE,
    notes             TEXT,
    created_by        INT UNSIGNED,
    created_at        DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_reg_tenant (reg_number, tenant_id),
    FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)   REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_tenant         (tenant_id),
    INDEX idx_tenant_status  (tenant_id, status),
    INDEX idx_tenant_name    (tenant_id, full_name),
    INDEX idx_tenant_phone   (tenant_id, phone),
    INDEX idx_user           (user_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 13. membership_plans
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS membership_plans (
    plan_id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id     INT UNSIGNED NOT NULL,
    name          VARCHAR(150) NOT NULL,
    duration_days INT NOT NULL DEFAULT 30,
    price         DECIMAL(10,2) NOT NULL DEFAULT 0,
    meal_slots    JSON COMMENT 'Array of slot_ids included',
    description   TEXT,
    is_active     TINYINT(1) DEFAULT 1,
    created_by    INT UNSIGNED,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id) ON DELETE CASCADE,
    INDEX idx_tenant (tenant_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 14. memberships
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS memberships (
    membership_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id     INT UNSIGNED NOT NULL,
    student_id    INT UNSIGNED NOT NULL,
    plan_id       INT UNSIGNED NOT NULL,
    start_date    DATE NOT NULL,
    end_date      DATE NOT NULL,
    status        ENUM('active','expired','cancelled','pending') DEFAULT 'active',
    renewal_count SMALLINT DEFAULT 0,
    created_by    INT UNSIGNED,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id)  REFERENCES tenants(tenant_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id)    REFERENCES membership_plans(plan_id),
    INDEX idx_student        (student_id),
    INDEX idx_tenant_status  (tenant_id, status),
    INDEX idx_tenant_end     (tenant_id, end_date, status),
    INDEX idx_student_active (student_id, status)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 15. payments
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS payments (
    payment_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id      INT UNSIGNED NOT NULL,
    student_id     INT UNSIGNED NOT NULL,
    membership_id  INT UNSIGNED NULL,
    amount         DECIMAL(10,2) NOT NULL,
    discount       DECIMAL(10,2) DEFAULT 0,
    net_amount     DECIMAL(10,2) NOT NULL,
    payment_mode   ENUM('cash','upi','card','netbanking','cheque','online') DEFAULT 'cash',
    transaction_ref VARCHAR(150),
    payment_date   DATE NOT NULL,
    due_date       DATE,
    status         ENUM('paid','pending','partial','refunded','failed') DEFAULT 'paid',
    receipt_number VARCHAR(50),
    notes          TEXT,
    created_by     INT UNSIGNED,
    created_at     DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id)     REFERENCES tenants(tenant_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id)    REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (membership_id) REFERENCES memberships(membership_id) ON DELETE SET NULL,
    INDEX idx_student        (student_id),
    INDEX idx_receipt        (receipt_number),
    INDEX idx_tenant_date    (tenant_id, payment_date),
    INDEX idx_tenant_status  (tenant_id, status, payment_date),
    INDEX idx_student_date   (student_id, payment_date)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 16. payment_history (audit trail for status changes)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS payment_history (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_id  INT UNSIGNED NOT NULL,
    tenant_id   INT UNSIGNED NOT NULL,
    changed_by  INT UNSIGNED,
    old_status  VARCHAR(30),
    new_status  VARCHAR(30),
    note        TEXT,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payment_id) REFERENCES payments(payment_id) ON DELETE CASCADE,
    INDEX idx_payment (payment_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 17. student_attendance
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS student_attendance (
    attendance_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id     INT UNSIGNED NOT NULL,
    student_id    INT UNSIGNED NOT NULL,
    slot_id       INT UNSIGNED NOT NULL COMMENT 'Which meal slot',
    date          DATE NOT NULL,
    status        ENUM('present','absent','leave') DEFAULT 'present',
    marked_by     INT UNSIGNED COMMENT 'NULL=self by student',
    self_marked   TINYINT(1) DEFAULT 0,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_attend          (tenant_id, student_id, slot_id, date),
    FOREIGN KEY (tenant_id)  REFERENCES tenants(tenant_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (slot_id)    REFERENCES meal_slots(slot_id) ON DELETE CASCADE,
    INDEX idx_student            (student_id),
    INDEX idx_tenant_date_slot   (tenant_id, date, slot_id),
    INDEX idx_student_date       (student_id, date),
    INDEX idx_tenant_student     (tenant_id, student_id, date)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 18. student_documents
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS student_documents (
    doc_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id   INT UNSIGNED NOT NULL,
    student_id  INT UNSIGNED NOT NULL,
    doc_type    VARCHAR(100),
    file_path   VARCHAR(255) NOT NULL,
    file_name   VARCHAR(255),
    file_size   INT DEFAULT 0,
    uploaded_by INT UNSIGNED,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id)  REFERENCES tenants(tenant_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    INDEX idx_student (student_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 19. coordinators
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS coordinators (
    coordinator_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id         INT UNSIGNED NOT NULL,
    user_id           INT UNSIGNED NOT NULL,
    assigned_tenants  JSON COMMENT 'Array of tenant_ids this coord manages',
    custom_permissions JSON,
    status            ENUM('active','inactive') DEFAULT 'active',
    created_by        INT UNSIGNED,
    created_at        DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)   REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 20. notifications
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS notifications (
    notification_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id       INT UNSIGNED NOT NULL,
    target_role     VARCHAR(50) COMMENT 'all / student / admin etc',
    target_user_id  INT UNSIGNED NULL,
    title           VARCHAR(255) NOT NULL,
    message         TEXT NOT NULL,
    type            ENUM('info','warning','success','danger') DEFAULT 'info',
    channel         ENUM('in_app','email','whatsapp','sms') DEFAULT 'in_app',
    is_read         TINYINT(1) DEFAULT 0,
    created_by      INT UNSIGNED,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id)      REFERENCES tenants(tenant_id) ON DELETE CASCADE,
    FOREIGN KEY (target_user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_user               (target_user_id),
    INDEX idx_tenant             (tenant_id),
    INDEX idx_tenant_user_read   (tenant_id, target_user_id, is_read),
    INDEX idx_created            (created_at)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 21. activity_logs
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS activity_logs (
    log_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id   INT UNSIGNED,
    user_id     INT UNSIGNED,
    action      VARCHAR(150) NOT NULL,
    entity_type VARCHAR(100),
    entity_id   INT UNSIGNED,
    old_values  JSON,
    new_values  JSON,
    ip_address  VARCHAR(45),
    user_agent  VARCHAR(255),
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tenant (tenant_id),
    INDEX idx_user (user_id),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 22. food_menu
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS food_menu (
    menu_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id  INT UNSIGNED NOT NULL,
    slot_id    INT UNSIGNED NOT NULL,
    day_of_week TINYINT COMMENT '0=Sunday,1=Monday..6=Saturday, NULL=specific date',
    menu_date  DATE NULL,
    items      TEXT NOT NULL,
    is_active  TINYINT(1) DEFAULT 1,
    created_by INT UNSIGNED,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id) ON DELETE CASCADE,
    FOREIGN KEY (slot_id)   REFERENCES meal_slots(slot_id) ON DELETE CASCADE,
    UNIQUE KEY uq_tenant_slot_day (tenant_id, slot_id, day_of_week),
    INDEX idx_tenant_date         (tenant_id, menu_date)
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- 23. complaints
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS complaints (
    complaint_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id    INT UNSIGNED NOT NULL,
    student_id   INT UNSIGNED NOT NULL,
    subject      VARCHAR(255) NOT NULL,
    description  TEXT,
    status       ENUM('open','in_progress','resolved','closed') DEFAULT 'open',
    priority     ENUM('low','medium','high') DEFAULT 'medium',
    resolved_by  INT UNSIGNED,
    resolved_at  DATETIME,
    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id)  REFERENCES tenants(tenant_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    INDEX idx_tenant         (tenant_id),
    INDEX idx_tenant_status  (tenant_id, status, created_at)
) ENGINE=InnoDB;

-- ============================================================
-- SEED DATA
-- ============================================================

-- Subscription plans
INSERT IGNORE INTO subscription_plans (name, price_monthly, price_yearly, max_students, max_coordinators, storage_mb, features) VALUES
('Starter',  499,  4999,  100, 1, 500,  '{"attendance":true,"reports":true,"food_menu":false,"complaints":false}'),
('Growth',   999,  9999,  500, 3, 2000, '{"attendance":true,"reports":true,"food_menu":true,"complaints":true}'),
('Enterprise',1999,19999, 0,   10,10000,'{"attendance":true,"reports":true,"food_menu":true,"complaints":true,"whatsapp":true,"online_payment":true}');

-- Feature modules
INSERT IGNORE INTO feature_modules (name, slug, description, icon, is_core, version) VALUES
('Dashboard',         'dashboard',        'Core dashboard',                'bi-speedometer2', 1, '1.0.0'),
('Students',          'students',         'Student management',            'bi-people',       1, '1.0.0'),
('Payments',          'payments',         'Payment & receipts',            'bi-credit-card',  1, '1.0.0'),
('Settings',          'settings',         'Mess settings',                 'bi-gear',         1, '1.0.0'),
('Attendance',        'attendance',       'Meal-slot attendance',          'bi-calendar-check',1,'1.0.0'),
('Membership',        'membership',       'Membership plans',              'bi-card-checklist',0,'1.0.0'),
('Food Menu',         'food_menu',        'Weekly food menu',              'bi-journal-text', 0, '1.0.0'),
('Complaints',        'complaints',       'Student complaint system',      'bi-chat-square-text',0,'1.0.0'),
('WhatsApp Notify',   'whatsapp',         'WhatsApp notifications',        'bi-whatsapp',     0, '1.0.0'),
('Online Payment',    'online_payment',   'Razorpay/Stripe gateway',       'bi-bank',         0, '1.0.0'),
('QR Attendance',     'qr_attendance',    'QR code based attendance',      'bi-qr-code',      0, '1.0.0'),
('Reports',           'reports',          'Export reports PDF/Excel',      'bi-file-earmark-bar-graph',1,'1.0.0'),
('Notifications',     'notifications',    'Push & in-app notifications',   'bi-bell',         0, '1.0.0');

-- System roles
INSERT IGNORE INTO roles (tenant_id, name, slug, is_system) VALUES
(NULL, 'Super Admin', 'super_admin', 1),
(NULL, 'Mess Admin',  'mess_admin',  1),
(NULL, 'Student',     'student',     1),
(NULL, 'Coordinator', 'coordinator', 1);

-- Core permissions
INSERT IGNORE INTO permissions (module_id, name, slug) VALUES
(1,  'View Dashboard',         'dashboard.view'),
(2,  'View Students',          'students.view'),
(2,  'Create Student',         'students.create'),
(2,  'Edit Student',           'students.edit'),
(2,  'Delete Student',         'students.delete'),
(3,  'View Payments',          'payments.view'),
(3,  'Create Payment',         'payments.create'),
(3,  'Refund Payment',         'payments.refund'),
(5,  'View Attendance',        'attendance.view'),
(5,  'Mark Attendance',        'attendance.mark'),
(6,  'View Memberships',       'membership.view'),
(6,  'Create Membership',      'membership.create'),
(7,  'View Food Menu',         'food_menu.view'),
(7,  'Manage Food Menu',       'food_menu.manage'),
(8,  'View Complaints',        'complaints.view'),
(8,  'Manage Complaints',      'complaints.manage'),
(12, 'View Reports',           'reports.view'),
(12, 'Export Reports',         'reports.export'),
(4,  'Manage Settings',        'settings.manage'),
(NULL,'Manage Tenants',        'superadmin.tenants'),
(NULL,'Manage Modules',        'superadmin.modules'),
(NULL,'Manage Subscriptions',  'superadmin.subscriptions'),
(NULL,'View Audit Logs',       'superadmin.audit');

-- Super admin gets all permissions (*)
-- Role permissions for mess_admin (role_id=2)
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT 2, permission_id FROM permissions
WHERE slug NOT LIKE 'superadmin.%';

-- Student role permissions
INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT 3, permission_id FROM permissions
WHERE slug IN ('dashboard.view','payments.view','attendance.view','food_menu.view','complaints.view','membership.view');

-- Coordinator permissions
INSERT INTO role_permissions (role_id, permission_id)
SELECT 4, permission_id FROM permissions
WHERE slug IN ('dashboard.view','students.view','payments.view','attendance.view','reports.view','complaints.view','complaints.manage');

-- Default super admin user (password: SuperAdmin@123)
INSERT INTO users (tenant_id, role_id, email, password_hash, full_name, status)
VALUES (NULL, 1, 'superadmin@messsaas.com',
        '$2y$12$XnTRWa9PwNFMQg0whyTZY.VIPRl/ZUy7Xzl5bBMQ2krbVjrB9MMdS', -- 'SuperAdmin@123' bcrypt
        'Super Admin', 'active');
