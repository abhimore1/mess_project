<?php
namespace App\Controllers\Admin;

use DB;
use App\Core\Controller;

class YearController extends Controller
{
    public function index(): void
    {
        $tid = auth_user()['tenant_id'];
        $years = DB::query("SELECT * FROM academic_years WHERE tenant_id=? ORDER BY created_at DESC", [$tid]);
        
        $pageTitle = 'Academic Years';
        $this->view('admin/years/index', compact('years', 'pageTitle'), 'app');
    }

    public function store(): void
    {
        $this->verifyCsrf();
        $this->requirePermission('settings.manage'); // Reuse settings permission or create a new one
        $tid = auth_user()['tenant_id'];

        $errors = $this->validate(['year_name' => 'required']);
        if ($errors) {
            flash('error', array_values($errors)[0]);
            $this->back();
        }

        $yearName = trim($this->input('year_name'));

        // Check if exists
        $exists = DB::queryOne("SELECT year_id FROM academic_years WHERE tenant_id=? AND year_name=?", [$tid, $yearName]);
        if ($exists) {
            flash('error', 'Academic year already exists.');
            $this->back();
        }

        DB::insert('academic_years', [
            'tenant_id'  => $tid,
            'year_name'  => $yearName,
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        log_activity('academic_year.created', 'academic_years', 0);
        flash('success', 'Academic year added successfully.');
        $this->redirect('admin/years');
    }

    public function toggleStatus(string $id): void
    {
        $this->verifyCsrf();
        $this->requirePermission('settings.manage');
        $tid = auth_user()['tenant_id'];

        $year = DB::queryOne("SELECT * FROM academic_years WHERE year_id=? AND tenant_id=?", [(int)$id, $tid]);
        if (!$year) $this->abort(404);

        $newStatus = $year['is_active'] ? 0 : 1;
        DB::update('academic_years', ['is_active' => $newStatus], ['year_id' => (int)$id]);

        flash('success', 'Year status updated.');
        $this->redirect('admin/years');
    }

    public function delete(string $id): void
    {
        $this->verifyCsrf();
        $this->requirePermission('settings.manage');
        $tid = auth_user()['tenant_id'];

        // Check if there are students linked to this year
        $studentsCount = DB::queryOne("SELECT COUNT(*) AS c FROM students WHERE year_id=? AND tenant_id=?", [(int)$id, $tid])['c'];
        if ($studentsCount > 0) {
            flash('error', "Cannot delete this year. There are $studentsCount students associated with it.");
            $this->redirect('admin/years');
        }

        DB::execute("DELETE FROM academic_years WHERE year_id=? AND tenant_id=?", [(int)$id, $tid]);

        log_activity('academic_year.deleted', 'academic_years', (int)$id);
        flash('success', 'Academic year deleted.');
        $this->redirect('admin/years');
    }
}
