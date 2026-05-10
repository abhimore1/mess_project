<?php
namespace App\Controllers\Admin;

use DB;
use App\Core\Controller;

class StudentSlotController extends Controller
{
    public function __construct()
    {
        $this->requireModule('students');
    }

    public function index(): void
    {
        $tid = auth_user()['tenant_id'];

        // Get all active meal slots
        $slots = DB::query("SELECT * FROM meal_slots WHERE tenant_id=? AND is_active=1 ORDER BY sort_order", [$tid]);
        
        // Get all active students
        $students = DB::query("SELECT student_id, full_name, room_number FROM students WHERE tenant_id=? AND status='active' ORDER BY full_name", [$tid]);
        
        // Get all assignments
        $assignments = DB::query("SELECT student_id, slot_id FROM student_meal_slots WHERE tenant_id=?", [$tid]);
        
        // Map assignments: student_id => [slot_id1, slot_id2, ...]
        $assignedSlots = [];
        foreach ($assignments as $a) {
            $assignedSlots[$a['student_id']][] = $a['slot_id'];
        }

        $pageTitle = 'Assign Meal Slots';
        $this->view('admin/students/assign_slots', compact('students', 'slots', 'assignedSlots', 'pageTitle'), 'app');
    }

    public function saveBulk(): void
    {
        $this->verifyCsrf();
        $tid = auth_user()['tenant_id'];
        
        // The data comes as a JSON array of student assignments
        $records = json_decode($this->input('records', '[]'), true);
        
        DB::beginTransaction();
        try {
            foreach ($records as $rec) {
                $studentId = (int)$rec['student_id'];
                $assignedSlotIds = $rec['slot_ids'] ?? []; // Array of slot_ids checked for this student
                
                // Remove all existing slot assignments for this student
                DB::execute("DELETE FROM student_meal_slots WHERE tenant_id=? AND student_id=?", [$tid, $studentId]);
                
                // Insert new ones
                foreach ($assignedSlotIds as $slotId) {
                    DB::execute(
                        "INSERT INTO student_meal_slots (tenant_id, student_id, slot_id) VALUES (?, ?, ?)",
                        [$tid, $studentId, (int)$slotId]
                    );
                }
            }
            DB::commit();
            $this->json(['success' => true, 'message' => 'Assignments updated successfully']);
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
