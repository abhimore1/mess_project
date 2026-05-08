<?php $pageTitle = 'Reports & Analytics'; ?>

<div class="page-header d-flex align-items-center justify-content-between mb-4 animate-fadeInUp">
    <div>
        <h4 class="fw-700 mb-1">Reports & Analytics</h4>
        <p class="text-muted small mb-0">Generate and export detailed data for accounting, attendance, and management.</p>
    </div>
</div>

<div class="row g-4 animate-fadeInUp stagger-1">
    <!-- Payment Report -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100 report-card">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
                <div class="report-icon bg-success-subtle text-success">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <h6 class="fw-700 mb-0">Payment Report</h6>
            </div>
            <div class="card-body p-4">
                <form action="<?= url('admin/reports/export') ?>" method="GET">
                    <input type="hidden" name="type" value="payments">
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label small fw-700 text-muted text-uppercase tracking-wider">From</label>
                            <input type="date" name="from" class="form-control border-0 bg-surface-variant" value="<?= date('Y-m-01') ?>" style="border-radius: 10px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-700 text-muted text-uppercase tracking-wider">To</label>
                            <input type="date" name="to" class="form-control border-0 bg-surface-variant" value="<?= date('Y-m-d') ?>" style="border-radius: 10px;">
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success py-2 fw-600 shadow-sm">
                            <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export as Excel/CSV
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Attendance Report -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100 report-card">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
                <div class="report-icon bg-primary-subtle text-primary">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <h6 class="fw-700 mb-0">Attendance Report</h6>
            </div>
            <div class="card-body p-4">
                <form action="<?= url('admin/reports/export') ?>" method="GET">
                    <input type="hidden" name="type" value="attendance">
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label small fw-700 text-muted text-uppercase tracking-wider">From</label>
                            <input type="date" name="from" class="form-control border-0 bg-surface-variant" value="<?= date('Y-m-01') ?>" style="border-radius: 10px;">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-700 text-muted text-uppercase tracking-wider">To</label>
                            <input type="date" name="to" class="form-control border-0 bg-surface-variant" value="<?= date('Y-m-d') ?>" style="border-radius: 10px;">
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary py-2 fw-600 shadow-sm">
                            <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export as Excel/CSV
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Student Roster -->
    <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100 report-card">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
                <div class="report-icon bg-info-subtle text-info">
                    <i class="bi bi-people"></i>
                </div>
                <h6 class="fw-700 mb-0">Student Roster</h6>
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-between">
                <div class="mb-4">
                    <p class="text-muted small">Generate a comprehensive list of all students including their contact details, registration numbers, and current status.</p>
                    <div class="alert alert-info border-0 py-2 small" style="background: var(--info-container); color: var(--on-info-container);">
                        <i class="bi bi-info-circle me-2"></i>Great for offline records.
                    </div>
                </div>
                <form action="<?= url('admin/reports/export') ?>" method="GET">
                    <input type="hidden" name="type" value="students">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-info text-white py-2 fw-600 shadow-sm">
                            <i class="bi bi-file-earmark-person me-2"></i>Export Student List
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Placeholder for Analytics Chart -->
<div class="row mt-4 animate-fadeInUp stagger-2">
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-dark text-white overflow-hidden" style="border-radius: 20px;">
            <div class="card-body p-5 text-center position-relative">
                <div class="position-relative z-1">
                    <h3 class="fw-800 mb-3">Custom Data Requests?</h3>
                    <p class="opacity-75 mx-auto" style="max-width: 600px;">Need a specific report or specialized data export? Our system handles custom analytics for multi-tenant environments. Contact support for tailored data solutions.</p>
                    <div class="mt-4">
                        <button class="btn btn-primary-g px-5 py-3 fw-700 shadow-sm border-0">Request Custom Report</button>
                    </div>
                </div>
                <i class="bi bi-graph-up-arrow position-absolute top-50 start-50 translate-middle opacity-05" style="font-size: 20rem;"></i>
            </div>
        </div>
    </div>
</div>

<style>
.report-card {
    transition: all 0.3s ease;
    border-radius: 20px;
}
.report-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
}

.report-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.opacity-05 {
    opacity: 0.05;
}

.tracking-wider {
    letter-spacing: 0.05em;
}
</style>
