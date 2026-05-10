CREATE TABLE IF NOT EXISTS student_meal_slots (
    tenant_id INT UNSIGNED NOT NULL,
    student_id INT UNSIGNED NOT NULL,
    slot_id INT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (student_id, slot_id),
    INDEX idx_tenant (tenant_id),
    FOREIGN KEY (tenant_id) REFERENCES tenants(tenant_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (slot_id) REFERENCES meal_slots(slot_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Backfill existing active students with all active meal slots to preserve backward compatibility
INSERT IGNORE INTO student_meal_slots (tenant_id, student_id, slot_id)
SELECT s.tenant_id, s.student_id, m.slot_id
FROM students s
JOIN meal_slots m ON s.tenant_id = m.tenant_id
WHERE s.status = 'active' AND m.is_active = 1;
