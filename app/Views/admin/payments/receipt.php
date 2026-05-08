<?php $pageTitle='Receipt — '.$payment['receipt_number']; ?>
<div class="row justify-content-center">
<div class="col-lg-8">
    <div class="panel" id="receiptPrintArea">
        <div class="panel-body p-5">
            <div class="text-center border-bottom pb-4 mb-4" style="border-color:var(--border)!important">
                <h3 class="fw-800 mb-1" style="color:var(--primary)"><?= e($tenant['name']) ?></h3>
                <div class="text-muted small"><?= nl2br(e($tenant['address'])) ?></div>
                <div class="text-muted small">Ph: <?= e($tenant['contact_phone']) ?> | Email: <?= e($tenant['contact_email']) ?></div>
            </div>
            
            <div class="row mb-5">
                <div class="col-6">
                    <h6 class="text-muted small mb-1">BILLED TO:</h6>
                    <div class="fw-700"><?= e($payment['student_name']) ?></div>
                    <div class="text-muted small">Phone: <?= e($payment['phone']??'—') ?></div>
                    <div class="text-muted small">Room: <?= e($payment['room_number']??'—') ?></div>
                </div>
                <div class="col-6 text-end">
                    <h6 class="text-muted small mb-1">RECEIPT NO:</h6>
                    <div class="fw-700 mb-2"><?= e($payment['receipt_number']) ?></div>
                    <h6 class="text-muted small mb-1">DATE:</h6>
                    <div class="fw-600"><?= format_date($payment['payment_date']) ?></div>
                </div>
            </div>

            <table class="table mb-4">
                <thead style="background:var(--card2)">
                    <tr><th>Description</th><th class="text-end">Amount</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="fw-600">Mess Fee Payment</div>
                            <?php if($payment['plan_name']): ?>
                            <div class="text-muted small">Plan: <?= e($payment['plan_name']) ?></div>
                            <?php endif; ?>
                            <?php if($payment['notes']): ?>
                            <div class="text-muted small mt-1">Note: <?= e($payment['notes']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-end"><?= format_currency($payment['amount']) ?></td>
                    </tr>
                    <?php if($payment['discount'] > 0): ?>
                    <tr>
                        <td class="text-end text-muted small">Discount</td>
                        <td class="text-end text-danger">- <?= format_currency($payment['discount']) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="text-end fw-800 fs-5">NET TOTAL</td>
                        <td class="text-end fw-800 fs-5 text-success"><?= format_currency($payment['net_amount']) ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="row">
                <div class="col-6">
                    <div class="text-muted small">Payment Mode: <strong class="text-body"><?= ucfirst($payment['payment_mode']) ?></strong></div>
                    <?php if($payment['transaction_ref']): ?>
                    <div class="text-muted small">Transaction Ref: <strong class="text-body"><?= e($payment['transaction_ref']) ?></strong></div>
                    <?php endif; ?>
                </div>
                <div class="col-6 text-end">
                    <div class="mt-4 pt-4 border-top d-inline-block" style="border-color:var(--border)!important;min-width:150px">
                        <div class="text-muted small">Authorized Signatory</div>
                    </div>
                </div>
            </div>
            
            <div class="text-center text-muted small mt-5 pt-3 border-top" style="border-color:var(--border)!important">
                This is a computer-generated receipt.
            </div>
        </div>
    </div>
    
    <div class="text-center mt-4">
        <button onclick="window.print()" class="btn btn-primary-g"><i class="bi bi-printer me-2"></i>Print Receipt</button>
        <a href="<?= url('admin/payments') ?>" class="btn btn-outline-secondary ms-2">Back to Payments</a>
    </div>
</div>
</div>
<style>
@media print {
    body * { visibility: hidden; }
    #receiptPrintArea, #receiptPrintArea * { visibility: visible; }
    #receiptPrintArea { position: absolute; left: 0; top: 0; width: 100%; border: none!important; box-shadow: none!important; }
}
</style>
