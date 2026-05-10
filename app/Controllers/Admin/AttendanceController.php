<?php
namespace App\Controllers\Admin;

use DB;

use App\Core\Controller;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->requireModule('attendance');
    }

    public function index(): void
    {
        $tid     = auth_user()['tenant_id'];
        $date    = $this->input('date', date('Y-m-d'));
        $slotId  = (int)$this->input('slot_id', 0);

        $slots   = DB::query("SELECT * FROM meal_slots WHERE tenant_id=? AND is_active=1 ORDER BY sort_order",[$tid]);
        if (!$slotId && !empty($slots)) $slotId = $slots[0]['slot_id'];

        // Fetch students with today's attendance for selected slot
        $students = DB::query("SELECT s.student_id, s.full_name, s.room_number, s.photo_path,
            COALESCE(sa.status,'absent') AS att_status, sa.attendance_id
            FROM students s
            JOIN student_meal_slots sms ON sms.student_id=s.student_id AND sms.slot_id=?
            LEFT JOIN student_attendance sa ON sa.student_id=s.student_id
                AND sa.slot_id=? AND sa.date=? AND sa.tenant_id=?
            WHERE s.tenant_id=? AND s.status='active'
            ORDER BY s.full_name ASC",
            [$slotId, $slotId, $date, $tid, $tid]);

        $summary = [
            'present' => count(array_filter($students, fn($s) => $s['att_status'] === 'present')),
            'absent'  => count(array_filter($students, fn($s) => $s['att_status'] === 'absent')),
            'leave'   => count(array_filter($students, fn($s) => $s['att_status'] === 'leave')),
        ];

        // Generate QR Token
        $qrToken = md5($tid . $slotId . $date . env('APP_SECRET', 'MessIndiaSecretKey2026'));

        $pageTitle = 'Attendance';
        $this->view('admin/attendance/index', compact('students','slots','slotId','date','summary','qrToken','pageTitle'), 'app');
    }

    public function mark(): void
    {
        $this->verifyCsrf();
        $this->requirePermission('attendance.mark');
        $tid    = auth_user()['tenant_id'];
        $slotId = (int)$this->input('slot_id');
        $date   = $this->input('date', date('Y-m-d'));

        if ($this->isAjax()) {
            // Single toggle from AJAX
            $studentId = (int)$this->input('student_id');
            $status    = $this->input('status', 'present');

            DB::execute(
                "INSERT INTO student_attendance (tenant_id,student_id,slot_id,date,status,marked_by,created_at,updated_at)
                 VALUES (?,?,?,?,?,?,NOW(),NOW())
                 ON DUPLICATE KEY UPDATE status=VALUES(status),marked_by=VALUES(marked_by),updated_at=NOW()",
                [$tid,$studentId,$slotId,$date,$status,auth_user()['user_id']]
            );
            log_activity('attendance.marked','student_attendance',$studentId);
            $this->json(['success'=>true]);
        }

        $this->back();
    }

    public function bulkMark(): void
    {
        $this->verifyCsrf();
        $this->requirePermission('attendance.mark');
        $tid       = auth_user()['tenant_id'];
        $slotId    = (int)$this->input('slot_id');
        $date      = $this->input('date', date('Y-m-d'));
        $records   = json_decode($this->input('records','[]'), true);

        DB::beginTransaction();
        try {
            foreach ($records as $rec) {
                DB::execute(
                    "INSERT INTO student_attendance (tenant_id,student_id,slot_id,date,status,marked_by,created_at,updated_at)
                     VALUES (?,?,?,?,?,?,NOW(),NOW())
                     ON DUPLICATE KEY UPDATE status=VALUES(status),marked_by=VALUES(marked_by),updated_at=NOW()",
                    [$tid,(int)$rec['student_id'],$slotId,$date,$rec['status'],auth_user()['user_id']]
                );
            }
            DB::commit();
            $this->json(['success'=>true,'count'=>count($records)]);
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->json(['success'=>false,'error'=>$e->getMessage()],500);
        }
    }

    public function report(): void
    {
        $tid       = auth_user()['tenant_id'];
        $month     = $this->input('month', date('Y-m'));
        $studentId = (int)$this->input('student_id', 0);

        [$year,$mon] = explode('-', $month);
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, (int)$mon, (int)$year);

        $sql = "SELECT sa.date, ms.name AS slot_name, s.full_name, sa.status
                FROM student_attendance sa
                JOIN meal_slots ms ON ms.slot_id=sa.slot_id
                JOIN students s ON s.student_id=sa.student_id
                WHERE sa.tenant_id=? AND YEAR(sa.date)=? AND MONTH(sa.date)=?";
        $params = [$tid, $year, $mon];
        if ($studentId) { $sql .= " AND sa.student_id=?"; $params[] = $studentId; }
        $sql .= " ORDER BY sa.date, s.full_name";

        $records  = DB::query($sql, $params);
        $students = DB::query("SELECT student_id, full_name FROM students WHERE tenant_id=? AND status='active' ORDER BY full_name",[$tid]);
        $slots    = DB::query("SELECT * FROM meal_slots WHERE tenant_id=? AND is_active=1 ORDER BY sort_order",[$tid]);

        $pageTitle = 'Attendance Report';
        $this->view('admin/attendance/report', compact('records','students','slots','month','daysInMonth','studentId','pageTitle'), 'app');
    }
}
