<?php $pageTitle='Collect Payment'; ?>
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="panel">
    <div class="panel-header"><h6><i class="bi bi-cash-coin me-2"></i>Collect Payment</h6></div>
    <div class="panel-body">
        <form method="POST" action="<?= url('admin/payments/store') ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">SELECT STUDENT *</label>
                    <select name="student_id" class="form-select select2" required>
                        <option value="">Search student...</option>
                        <?php foreach($students as $s): ?>
                        <option value="<?= $s['student_id'] ?>"><?= e($s['full_name']) ?> (<?= e($s['phone']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">ASSOCIATED PLAN (Optional)</label>
                    <select name="plan_id" class="form-select" id="planSelect" onchange="updateAmount()">
                        <option value="">General Payment / Dues</option>
                        <?php foreach($plans as $p): ?>
                        <option value="<?= $p['plan_id'] ?>" data-price="<?= $p['price'] ?>"><?= e($p['name']) ?> - <?= format_currency($p['price']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">PAYMENT DATE</label>
                    <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">AMOUNT (<?= get_setting('currency_symbol','₹') ?>) *</label>
                    <input type="number" step="0.01" name="amount" id="amountInput" class="form-control" required oninput="calcNet()">
                </div>
                <div class="col-md-6">
                    <label class="form-label">DISCOUNT (<?= get_setting('currency_symbol','₹') ?>)</label>
                    <input type="number" step="0.01" name="discount" id="discountInput" class="form-control" value="0" oninput="calcNet()">
                </div>
                <div class="col-md-6">
                    <label class="form-label">NET PAYABLE</label>
                    <input type="text" id="netInput" class="form-control fw-700 text-success" readonly style="background:var(--card2)">
                </div>
                <div class="col-md-6">
                    <label class="form-label">PAYMENT MODE</label>
                    <select name="payment_mode" class="form-select">
                        <option value="cash">Cash</option>
                        <option value="upi">UPI</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="card">Card</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">TRANSACTION REF (Optional)</label>
                    <input type="text" name="transaction_ref" class="form-control" placeholder="UPI ID / UTR">
                </div>
                <div class="col-12">
                    <label class="form-label">NOTES</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary-g"><i class="bi bi-check-circle me-2"></i>Save Payment</button>
                    <a href="<?= url('admin/payments') ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
</div>
<script>
$(document).ready(function(){ $('.select2').select2({theme:'bootstrap-5'}); });
function updateAmount() {
    const sel = document.getElementById('planSelect');
    const opt = sel.options[sel.selectedIndex];
    if(opt.value) {
        document.getElementById('amountInput').value = opt.dataset.price;
        calcNet();
    }
}
function calcNet() {
    const a = parseFloat(document.getElementById('amountInput').value)||0;
    const d = parseFloat(document.getElementById('discountInput').value)||0;
    document.getElementById('netInput').value = (a-d).toFixed(2);
}
</script>
