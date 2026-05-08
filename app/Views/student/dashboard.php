<?php $pageTitle='Student Dashboard'; ?>
<div class="panel p-5 text-center">
    <h3 class="fw-700 text-primary">Welcome, <?= e($student['full_name']) ?></h3>
    <p class="text-muted">This is your student portal.</p>
    <a href="<?= url('student/food-menu') ?>" class="btn btn-primary-g mt-3">View Today's Menu</a>
</div>
