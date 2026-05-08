<?php $pageTitle='Students'; ?>

<!-- Page Header & Actions -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 animate-fadeInUp">
    <div>
        <h4 class="mb-0 fw-700 text-primary">Students Directory</h4>
        <div class="text-tertiary small mt-1">Manage and view all registered students</div>
    </div>
    <div class="d-flex gap-2 flex-wrap justify-content-md-end">
        <button type="button" class="btn btn-outline-secondary shadow-sm" id="importModalBtn">
            <i class="bi bi-file-earmark-arrow-up me-2"></i>Import Bulk
        </button>
        <div class="dropdown">
            <button class="btn btn-outline-secondary shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-download me-2"></i>Export
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li><a class="dropdown-item" href="<?= url('admin/students/export/pdf?'.http_build_query($_GET)) ?>"><i class="bi bi-file-earmark-pdf text-danger me-2"></i>Export as PDF</a></li>
                <li><a class="dropdown-item" href="<?= url('admin/students/export/excel?'.http_build_query($_GET)) ?>"><i class="bi bi-file-earmark-excel text-success me-2"></i>Export as CSV/Excel</a></li>
            </ul>
        </div>
        <a href="<?= url('admin/students/create') ?>" class="btn btn-primary shadow-sm">
            <i class="bi bi-person-plus-fill me-2"></i>Add Student
        </a>
    </div>
</div>

<!-- Filters Card -->
<div class="card mb-4 animate-fadeInUp stagger-1">
    <div class="card-body p-3">
        <form class="row g-2 align-items-center" method="GET" id="filterForm">
            <div class="col-12 col-md-5">
                <!-- FIX: position:relative on a wrapper div, NOT on input-group, so dropdown doesn't get clipped -->
                <div class="search-wrapper position-relative">
                    <div class="input-group">
                        <span class="input-group-text bg-surface-variant border-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="search" name="q" id="studentSearch"
                               class="form-control bg-surface-variant border-0"
                               placeholder="Search by name, phone or reg no..."
                               value="<?= e($search) ?>" autocomplete="off">
                    </div>
                    <!-- FIX: dropdown is sibling of input-group, inside the relative wrapper -->
                    <div id="searchSuggestions" class="suggestions-dropdown shadow-lg" style="display:none;"></div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="status" class="form-select bg-surface-variant border-0" onchange="this.form.submit()">
                    <option value="active"   <?= $status==='active'   ? 'selected' : '' ?>>Active Only</option>
                    <option value="inactive" <?= $status==='inactive' ? 'selected' : '' ?>>Inactive Only</option>
                    <option value="left"     <?= $status==='left'     ? 'selected' : '' ?>>Left Only</option>
                    <option value=""         <?= $status===''         ? 'selected' : '' ?>>All Statuses</option>
                </select>
            </div>
            <div class="col-6 col-md-4 d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-secondary w-100"><i class="bi bi-funnel me-2"></i>Filter</button>
                <?php if($search || $status): ?>
                <a href="<?= url('admin/students') ?>" class="btn btn-outline-secondary px-3" title="Clear Filters"><i class="bi bi-x-lg"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Data Table Card -->
<div class="card animate-fadeInUp stagger-2">
    <div class="table-responsive">
        <table class="table responsive-table mb-0">
            <thead>
                <tr>
                    <th>Student Info</th>
                    <th>Contact</th>
                    <th>Room</th>
                    <th>Membership</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($students as $s): ?>
            <tr>
                <td data-label="Student Info" class="student-cell">
                    <div class="d-flex align-items-center gap-3">
                        <?php if(!empty($s['photo_path'])): ?>
                            <img src="<?= url($s['photo_path']) ?>" width="40" height="40"
                                 class="rounded-circle shadow-sm" style="object-fit:cover">
                        <?php else: ?>
                            <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                 style="width:40px;height:40px;background:var(--primary-container);color:var(--on-primary-container);font-weight:700;font-size:1rem;flex-shrink:0;">
                                <?= strtoupper(mb_substr($s['full_name'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <div class="fw-700 text-primary mb-1" style="line-height:1.2;"><?= e($s['full_name']) ?></div>
                            <div class="text-tertiary font-medium" style="font-size:0.75rem;">
                                <i class="bi bi-upc-scan me-1"></i><?= e($s['reg_number'] ?? '—') ?>
                            </div>
                        </div>
                    </div>
                </td>
                <td data-label="Contact" class="font-medium align-middle">
                    <a href="tel:<?= e($s['phone']) ?>" class="text-decoration-none text-body">
                        <i class="bi bi-telephone text-tertiary me-2 d-none d-md-inline"></i><?= e($s['phone']) ?>
                    </a>
                </td>
                <td data-label="Room" class="font-medium align-middle">
                    <span class="badge badge-secondary">
                        <i class="bi bi-door-closed me-1"></i><?= e($s['room_number'] ?? 'Unassigned') ?>
                    </span>
                </td>
                <td data-label="Membership" class="align-middle">
                    <?php if(!empty($s['membership_end'])): ?>
                        <div class="d-flex flex-column align-items-md-start align-items-end">
                            <span class="badge <?= days_until($s['membership_end']) < 7 ? 'badge-warning' : 'badge-success' ?> mb-1">
                                <i class="bi bi-calendar-check me-1"></i>Ends <?= format_date($s['membership_end']) ?>
                            </span>
                            <span class="text-tertiary font-medium"
                                  style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.05em;">
                                <?= e($s['plan_name']) ?>
                            </span>
                        </div>
                    <?php else: ?>
                        <span class="badge" style="background:var(--surface-container-highest);color:var(--text-tertiary);">
                            No Active Plan
                        </span>
                    <?php endif; ?>
                </td>
                <td data-label="Status" class="align-middle">
                    <?php
                        $statusClass = 'badge-secondary';
                        if ($s['status'] === 'active') $statusClass = 'badge-success';
                        elseif ($s['status'] === 'left') $statusClass = 'badge-error';
                    ?>
                    <span class="badge <?= $statusClass ?>"><?= ucfirst($s['status']) ?></span>
                </td>
                <td class="action-cell align-middle text-end">
                    <a href="<?= url('admin/students/'.$s['student_id']) ?>"
                       class="btn btn-sm btn-icon btn-outline-secondary" title="View Profile">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="<?= url('admin/students/'.$s['student_id'].'/edit') ?>"
                       class="btn btn-sm btn-icon btn-outline-primary ms-1" title="Edit Student">
                        <i class="bi bi-pencil"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>

            <?php if(empty($students)): ?>
            <tr>
                <td colspan="6" class="text-center py-5">
                    <div class="text-tertiary mb-3" style="font-size:3rem;"><i class="bi bi-people"></i></div>
                    <h5 class="fw-600 text-secondary">No Students Found</h5>
                    <p class="text-muted mb-0">Try adjusting your search or filters.</p>
                </td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<div class="mt-4 d-flex justify-content-center">
    <?= paginate_links($pagination, url('admin/students')) ?>
</div>

<!-- ============================================================
     IMPORT MODAL
     FIX: Using a plain div.modal — NOT a <form> as the modal-content.
          The <form> is placed INSIDE modal-body/footer so Bootstrap
          can properly control the modal backdrop and dismiss buttons.
     ============================================================ -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-600" id="importModalLabel">
                    <i class="bi bi-file-earmark-spreadsheet text-success me-2"></i>Import Students
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- FIX: form wraps body + footer so submit button works correctly -->
            <form method="POST" action="<?= url('admin/students/import') ?>" enctype="multipart/form-data" id="importForm">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="alert alert-info d-flex align-items-start gap-3 mb-4 border-0"
                         style="background:var(--info-container);color:var(--on-info-container)">
                        <i class="bi bi-info-circle-fill fs-5 mt-1 flex-shrink-0"></i>
                        <div>
                            <strong>Instructions:</strong>
                            <ul class="mb-0 ps-3 mt-1 small">
                                <li><b>Step 1:</b> Click <b>Download Template</b> to get the formatted Excel file.</li>
                                <li><b>Step 2:</b> Fill in students (you can delete the sample row).</li>
                                <li><b>Step 3:</b> Upload the filled Excel file (<b>.xlsx</b>) directly below.</li>
                                <li>For <b>Academic Year</b> use the label you created &mdash; e.g. <code>2024-25</code>.</li>
                            </ul>
                            <div class="mt-3">
                                <a href="<?= url('admin/students/import/template') ?>"
                                   class="btn btn-sm btn-success shadow-sm">
                                    <i class="bi bi-file-earmark-excel me-1"></i>Download Excel Template (.xlsx)
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-medium text-secondary">
                            Select Excel (.xlsx) or CSV File
                        </label>
                        <input type="file" name="import_file" id="importFileInput" class="form-control"
                               accept=".csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,.xlsx"
                               required>
                        <div class="form-text text-muted small mt-1">
                            Max file size: 5MB. Duplicate records (same phone/email) will be skipped automatically.
                        </div>
                    </div>

                    <!-- Progress bar (hidden until submit) -->
                    <div id="importProgress" style="display:none;">
                        <div class="d-flex align-items-center gap-2 text-muted small mb-1">
                            <div class="spinner-border spinner-border-sm"></div>
                            Uploading and processing, please wait...
                        </div>
                        <div class="progress" style="height:6px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated w-100"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="importSubmitBtn">
                        <i class="bi bi-upload me-2"></i>Upload &amp; Import
                    </button>
                </div>
            </form><!-- /importForm -->
        </div>
    </div>
</div>

<style>
/* ── Search Suggestions Dropdown ─────────────────────────────── */
.search-wrapper {
    /* This is the positioning parent for the dropdown */
}

.suggestions-dropdown {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    right: 0;
    background: #ffffff;
    border: 1px solid var(--outline-variant);
    border-radius: 12px;
    z-index: 1100; /* High enough to clear everything */
    max-height: 420px;
    overflow-y: auto;
    padding: 8px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    scrollbar-width: thin;
    scrollbar-color: var(--outline-variant) transparent;
}

/* Custom Scrollbar for Chrome/Safari */
.suggestions-dropdown::-webkit-scrollbar {
    width: 6px;
}
.suggestions-dropdown::-webkit-scrollbar-thumb {
    background-color: var(--outline-variant);
    border-radius: 10px;
}

.suggestion-item {
    padding: 12px 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 12px;
    border-radius: 10px;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    color: var(--text-primary);
    margin-bottom: 2px;
}
.suggestion-item:last-child { margin-bottom: 0; }
.suggestion-item:hover, .suggestion-item:focus {
    background: var(--primary-container);
    color: var(--on-primary-container);
    outline: none;
}
.suggestion-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: var(--surface-container-highest);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-weight: 700;
    flex-shrink: 0;
    font-size: 1.1rem;
}
.suggestion-details { flex-grow: 1; min-width: 0; }
.suggestion-details .fw-700 {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.suggestion-meta {
    text-align: right;
    font-size: 0.8rem;
    flex-shrink: 0;
    opacity: 0.8;
}
.suggestions-loading,
.suggestions-empty {
    padding: 24px;
    text-align: center;
    color: var(--text-tertiary);
    font-size: 0.9rem;
}

/* ── Responsive Table ─────────────────────────────────────────── */
@media (max-width: 768px) {
    .responsive-table thead { display: none; }

    .responsive-table tbody tr {
        display: block;
        padding: var(--space-3);
        border-bottom: 1px solid var(--outline-variant) !important;
        transition: background var(--transition-fast);
    }
    .responsive-table tbody tr:hover {
        background: var(--surface-container-low);
    }
    .responsive-table tbody td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: none !important;
        padding: var(--space-2) 0;
        text-align: right;
    }
    .responsive-table tbody td::before {
        content: attr(data-label);
        font-weight: 600;
        font-size: var(--font-size-xs);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-tertiary);
        text-align: left;
        margin-right: var(--space-4);
        flex-shrink: 0;
    }

    /* Student info cell — full width, no label */
    .responsive-table .student-cell {
        display: flex;
        justify-content: flex-start;
        border-bottom: 1px solid var(--surface-container-highest) !important;
        padding-bottom: var(--space-3);
        margin-bottom: var(--space-2);
    }
    .responsive-table .student-cell::before { display: none; }

    /* Actions cell — right-aligned, no label */
    .responsive-table .action-cell {
        justify-content: flex-end;
        border-top: 1px dashed var(--surface-container-highest) !important;
        padding-top: var(--space-3);
        margin-top: var(--space-2);
    }
    .responsive-table .action-cell::before { display: none; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── 1. Import Modal — open via JS to avoid btn-inside-form issues ── */
    const importModalEl = document.getElementById('importModal');
    const importModal   = importModalEl ? new bootstrap.Modal(importModalEl) : null;

    document.getElementById('importModalBtn')?.addEventListener('click', function () {
        importModal?.show();
    });

    /* Reset form & progress bar when modal closes */
    importModalEl?.addEventListener('hidden.bs.modal', function () {
        document.getElementById('importForm')?.reset();
        document.getElementById('importProgress').style.display = 'none';
        document.getElementById('importSubmitBtn').disabled = false;
        document.getElementById('importSubmitBtn').innerHTML = '<i class="bi bi-upload me-2"></i>Upload &amp; Import';
    });

    /* Show progress on submit */
    document.getElementById('importForm')?.addEventListener('submit', function () {
        document.getElementById('importProgress').style.display = 'block';
        const btn = document.getElementById('importSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Importing...';
    });

    /* ── 2. Search Suggestions ───────────────────────────────────── */
    const searchInput = document.getElementById('studentSearch');
    const dropdown    = document.getElementById('searchSuggestions');

    if (!searchInput || !dropdown) return;

    let debounceTimer = null;
    const API_URL = '<?= url('admin/students/suggestions') ?>';
    const VIEW_URL = '<?= url('admin/students') ?>/';

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const q = this.value.trim();

        if (q.length < 2) {
            hideDropdown();
            return;
        }

        debounceTimer = setTimeout(() => fetchSuggestions(q), 250);
    });

    function fetchSuggestions(q) {
        dropdown.innerHTML = '<div class="suggestions-loading"><div class="spinner-border spinner-border-sm me-2"></div>Searching...</div>';
        showDropdown();

        fetch(API_URL + '?q=' + encodeURIComponent(q), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (res) {
            if (!res.ok) throw new Error('Network error');
            return res.json();
        })
        .then(function (data) {
            if (!Array.isArray(data) || data.length === 0) {
                dropdown.innerHTML = '<div class="suggestions-empty"><i class="bi bi-search me-2"></i>No matching students found</div>';
                return;
            }

            dropdown.innerHTML = data.map(function (s) {
                /* FIX: safe URL building — no template literal injection risk */
                const url     = VIEW_URL + parseInt(s.student_id, 10);
                const initial = (s.full_name || '?').charAt(0).toUpperCase();
                const name    = escHtml(s.full_name  || '');
                const reg     = escHtml(s.reg_number || '');
                const phone   = escHtml(s.phone      || '');

                return `<div class="suggestion-item" role="button" tabindex="0"
                             onclick="window.location='${url}'"
                             onkeydown="if(event.key==='Enter')window.location='${url}'">
                    <div class="suggestion-icon">${initial}</div>
                    <div class="suggestion-details">
                        <div class="fw-700" style="font-size:0.9rem;line-height:1.2;">${name}</div>
                        <div class="small text-muted" style="font-size:0.75rem;">${reg}</div>
                    </div>
                    <div class="suggestion-meta">
                        <div class="fw-600 text-primary">${phone}</div>
                        <div class="text-tertiary" style="font-size:0.65rem;text-transform:uppercase;letter-spacing:0.05em;">STUDENT</div>
                    </div>
                </div>`;
            }).join('');
        })
        .catch(function () {
            hideDropdown();
        });
    }

    /* Keyboard navigation: Escape closes dropdown */
    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') hideDropdown();
    });

    /* Close when clicking outside the search wrapper */
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.search-wrapper')) {
            hideDropdown();
        }
    });

    function showDropdown() { dropdown.style.display = 'block'; }
    function hideDropdown() { dropdown.style.display = 'none'; dropdown.innerHTML = ''; }

    /* Simple HTML escape to prevent XSS in suggestion rendering */
    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }
});
</script>