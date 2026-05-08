-- ============================================================
-- MessSaaS SCALABILITY UPGRADE
-- Covering indexes for 1000+ tenants, 10000+ students
-- Run: mysql -u root mess_saas < storage/optimize.sql
-- ============================================================
USE mess_saas;

-- ============================================================
-- STUDENT_ATTENDANCE: Highest volume table — 3-col covering index
-- At 10k students * 3 slots * 365 days = ~11M rows/year
-- ============================================================
ALTER TABLE student_attendance
    DROP INDEX IF EXISTS idx_date,
    DROP INDEX IF EXISTS idx_student;

-- Primary query pattern: tenant+date+slot (attendance marking page)
ALTER TABLE student_attendance
    ADD INDEX idx_tenant_date_slot (tenant_id, date, slot_id),
-- Student portal: my attendance by month
    ADD INDEX idx_student_date     (student_id, date),
-- Report: tenant+student range
    ADD INDEX idx_tenant_student   (tenant_id, student_id, date);

-- ============================================================
-- PAYMENTS: Second highest volume
-- ============================================================
ALTER TABLE payments
    DROP INDEX IF EXISTS idx_date,
    DROP INDEX IF EXISTS idx_status,
    DROP INDEX IF EXISTS idx_student;

-- Date range filter + tenant (main payments list)
ALTER TABLE payments
    ADD INDEX idx_tenant_date      (tenant_id, payment_date),
    ADD INDEX idx_tenant_status    (tenant_id, status, payment_date),
    ADD INDEX idx_student_date     (student_id, payment_date),
    ADD INDEX idx_receipt          (receipt_number);

-- ============================================================
-- STUDENTS: Composite indexes for search + status filters
-- ============================================================
ALTER TABLE students
    DROP INDEX IF EXISTS idx_status,
    DROP INDEX IF EXISTS idx_name;

ALTER TABLE students
    ADD INDEX idx_tenant_status    (tenant_id, status),
    ADD INDEX idx_tenant_name      (tenant_id, full_name),
    ADD INDEX idx_tenant_phone     (tenant_id, phone),
    ADD INDEX idx_user             (user_id);

-- ============================================================
-- MEMBERSHIPS: Expiry checks run frequently (cron/dashboard)
-- ============================================================
ALTER TABLE memberships
    DROP INDEX IF EXISTS idx_status,
    DROP INDEX IF EXISTS idx_end_date;

ALTER TABLE memberships
    ADD INDEX idx_tenant_status    (tenant_id, status),
    ADD INDEX idx_tenant_end       (tenant_id, end_date, status),
    ADD INDEX idx_student_active   (student_id, status);

-- ============================================================
-- USERS: Login lookup
-- ============================================================
ALTER TABLE users
    DROP INDEX IF EXISTS idx_email;
ALTER TABLE users
    ADD INDEX idx_email_status     (email, status),
    ADD INDEX idx_tenant_role      (tenant_id, role_id);

-- ============================================================
-- NOTIFICATIONS: Inbox queries
-- ============================================================
ALTER TABLE notifications
    DROP INDEX IF EXISTS idx_user,
    DROP INDEX IF EXISTS idx_tenant;
ALTER TABLE notifications
    ADD INDEX idx_tenant_user_read (tenant_id, target_user_id, is_read),
    ADD INDEX idx_created          (created_at);

-- ============================================================
-- ACTIVITY_LOGS: Partition by date range if > 5M rows
-- For now: composite covering index
-- ============================================================
ALTER TABLE activity_logs
    DROP INDEX IF EXISTS idx_created;
ALTER TABLE activity_logs
    ADD INDEX idx_tenant_created   (tenant_id, created_at),
    ADD INDEX idx_user_created     (user_id, created_at);

-- ============================================================
-- COMPLAINTS, FOOD_MENU, MESS_SETTINGS: Minor tuning
-- ============================================================
ALTER TABLE complaints
    DROP INDEX IF EXISTS idx_status;
ALTER TABLE complaints
    ADD INDEX idx_tenant_status    (tenant_id, status, created_at);

ALTER TABLE food_menu
    DROP INDEX IF EXISTS idx_tenant_day;
ALTER TABLE food_menu
    ADD UNIQUE KEY uq_tenant_slot_day    (tenant_id, slot_id, day_of_week),
    ADD INDEX      idx_tenant_date       (tenant_id, menu_date);

ALTER TABLE mess_settings
    DROP INDEX IF EXISTS idx_group;
ALTER TABLE mess_settings
    ADD INDEX idx_tenant_group     (tenant_id, setting_group);

-- ============================================================
-- InnoDB PERFORMANCE TUNING — Run via my.ini for production:
--   innodb_buffer_pool_size = 2G        (set to 70% of RAM)
--   innodb_log_file_size    = 512M
--   innodb_flush_log_at_trx_commit = 2  (safe async flush)
--   query_cache_type        = 0         (disabled in MySQL 8)
--   max_connections         = 500
--   innodb_read_io_threads  = 8
--   innodb_write_io_threads = 8
-- ============================================================

-- ============================================================
-- VERIFY: Show all indexes on critical tables
-- ============================================================
SELECT TABLE_NAME, INDEX_NAME, COLUMN_NAME, SEQ_IN_INDEX, CARDINALITY
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = 'mess_saas'
  AND TABLE_NAME IN ('students','student_attendance','payments','memberships','users')
ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX;
