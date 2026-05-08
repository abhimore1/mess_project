<?php $pageTitle='Reports & Exports'; ?>
<div class="row g-4">
    <div class="col-md-4">
        <div class="panel">
            <div class="panel-header"><h6><i class="bi bi-cash-coin me-2 text-success"></i>Payment Report</h6></div>
            <div class="panel-body">
                <form action="<?= url('admin/reports/export') ?>" method="GET">
                    <input type="hidden" name="type" value="payments">
                    <div class="mb-3"><label class="form-label">FROM</label><input type="date" name="from" class="form-control" value="<?= date('Y-m-01') ?>"></div>
                    <div class="mb-3"><label class="form-label">TO</label><input type="date" name="to" class="form-control" value="<?= date('Y-m-d') ?>"></div>
                    <button type="submit" class="btn btn-success w-100"><i class="bi bi-file-earmark-excel me-2"></i>Export CSV</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel">
            <div class="panel-header"><h6><i class="bi bi-calendar-check me-2 text-primary"></i>Attendance Report</h6></div>
            <div class="panel-body">
                <form action="<?= url('admin/reports/export') ?>" method="GET">
                    <input type="hidden" name="type" value="attendance">
                    <div class="mb-3"><label class="form-label">FROM</label><input type="date" name="from" class="form-control" value="<?= date('Y-m-01') ?>"></div>
                    <div class="mb-3"><label class="form-label">TO</label><input type="date" name="to" class="form-control" value="<?= date('Y-m-d') ?>"></div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-file-earmark-excel me-2"></i>Export CSV</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel">
            <div class="panel-header"><h6><i class="bi bi-people me-2 text-info"></i>Student Roster</h6></div>
            <div class="panel-body">
                <p class="text-muted small mb-4">Export a complete list of all active and inactive students currently in the database.</p>
                <form action="<?= url('admin/reports/export') ?>" method="GET">
                    <input type="hidden" name="type" value="students">
                    <button type="submit" class="btn btn-info text-white w-100"><i class="bi bi-file-earmark-excel me-2"></i>Export Roster CSV</button>
                </form>
            </div>
        </div>
    </div>
</div>
