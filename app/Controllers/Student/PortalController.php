<?php
namespace App\Controllers\Student;

use DB;

use App\Core\Controller;

class PortalController extends Controller
{
    private function getStudent(): array
    {
        $tid    = auth_user()['tenant_id'];
        $userId = auth_user()['user_id'];
        $student = DB::queryOne("SELECT * FROM students WHERE user_id=? AND tenant_id=? LIMIT 1",[$userId,$tid]);
        if (!$student) $this->abort(403, 'Student profile not found.');
        return $student;
    }

    public function dashboard(): void
    {
        $student = $this->getStudent();
        $tid     = auth_user()['tenant_id'];

        $activeMembership = DB::queryOne("SELECT m.*, mp.name AS plan_name, mp.price,
            DATEDIFF(m.end_date, CURDATE()) AS days_left
            FROM memberships m JOIN membership_plans mp ON mp.plan_id=m.plan_id
            WHERE m.student_id=? AND m.status='active' ORDER BY m.end_date DESC LIMIT 1",
            [$student['student_id']]);

        $recentPayments = DB::query("SELECT * FROM payments WHERE student_id=? AND tenant_id=? ORDER BY payment_date DESC LIMIT 5",
            [$student['student_id'],$tid]);

        // Attendance last 7 days
        $attendanceSummary = DB::query("SELECT sa.date, ms.name AS slot_name, sa.status
            FROM student_attendance sa JOIN meal_slots ms ON ms.slot_id=sa.slot_id
            WHERE sa.student_id=? AND sa.tenant_id=? AND sa.date >= DATE_SUB(CURDATE(),INTERVAL 7 DAY)
            ORDER BY sa.date DESC, ms.sort_order", [$student['student_id'],$tid]);

        // Today's food menu (only for slots the student is assigned to)
        $todayMenu = DB::query("SELECT ms.name AS slot_name, ms.slot_time, fm.items
            FROM food_menu fm JOIN meal_slots ms ON ms.slot_id=fm.slot_id
            JOIN student_meal_slots sms ON sms.slot_id=ms.slot_id AND sms.student_id=?
            WHERE fm.tenant_id=? AND (fm.menu_date=CURDATE() OR fm.day_of_week=DAYOFWEEK(CURDATE())-1)
            AND fm.is_active=1 ORDER BY ms.sort_order",[$student['student_id'], $tid]);

        $pageTitle = 'My Dashboard';
        $this->view('student/dashboard', compact('student','activeMembership','recentPayments','attendanceSummary','todayMenu','pageTitle'), 'app');
    }

    public function profile(): void
    {
        $student   = $this->getStudent();
        $pageTitle = 'My Profile';
        $this->view('student/profile', compact('student','pageTitle'), 'app');
    }

    public function payments(): void
    {
        $student  = $this->getStudent();
        $payments = DB::query("SELECT * FROM payments WHERE student_id=? AND tenant_id=? ORDER BY payment_date DESC",
            [$student['student_id'], auth_user()['tenant_id']]);
        $pageTitle = 'Payment History';
        $this->view('student/payments', compact('student','payments','pageTitle'), 'app');
    }

    public function receipt(string $id): void
    {
        $student = $this->getStudent();
        $payment = DB::queryOne("SELECT p.*, s.full_name AS student_name FROM payments p
            JOIN students s ON s.student_id=p.student_id
            WHERE p.payment_id=? AND p.student_id=? AND p.tenant_id=? LIMIT 1",
            [(int)$id,$student['student_id'],auth_user()['tenant_id']]);
        if (!$payment) $this->abort(403);
        $tenant    = DB::queryOne("SELECT * FROM tenants WHERE tenant_id=? LIMIT 1",[auth_user()['tenant_id']]);
        $pageTitle = 'Receipt — '.$payment['receipt_number'];
        $this->view('admin/payments/receipt', compact('payment','tenant','pageTitle'), 'app');
    }

    public function attendance(): void
    {
        $student = $this->getStudent();
        $tid     = auth_user()['tenant_id'];
        $month   = $this->input('month', date('Y-m'));
        [$year,$mon] = explode('-', $month);

        $records = DB::query("SELECT sa.date, ms.name AS slot_name, ms.slot_time, sa.status, sa.self_marked
            FROM student_attendance sa JOIN meal_slots ms ON ms.slot_id=sa.slot_id
            WHERE sa.student_id=? AND sa.tenant_id=? AND YEAR(sa.date)=? AND MONTH(sa.date)=?
            ORDER BY sa.date ASC, ms.sort_order",
            [$student['student_id'],$tid,$year,$mon]);

        $slots   = DB::query("SELECT m.* FROM meal_slots m 
            JOIN student_meal_slots sms ON sms.slot_id=m.slot_id 
            WHERE m.tenant_id=? AND sms.student_id=? AND m.is_active=1 ORDER BY m.sort_order",
            [$tid, $student['student_id']]);
        $pageTitle = 'My Attendance';
        $this->view('student/attendance', compact('student','records','slots','month','pageTitle'), 'app');
    }

    public function selfMark(): void
    {
        $this->verifyCsrf();
        $student = $this->getStudent();
        $tid     = auth_user()['tenant_id'];
        $slotId  = (int)$this->input('slot_id');
        $status  = $this->input('status','present');

        // Verify slot is for today & tenant & assigned to student
        $slot = DB::queryOne("SELECT m.slot_id FROM meal_slots m
            JOIN student_meal_slots sms ON sms.slot_id=m.slot_id
            WHERE m.slot_id=? AND m.tenant_id=? AND sms.student_id=? AND m.is_active=1",
            [$slotId, $tid, $student['student_id']]);
        if (!$slot) { $this->json(['success'=>false,'error'=>'Invalid or unassigned slot.']); }

        DB::execute(
            "INSERT INTO student_attendance (tenant_id,student_id,slot_id,date,status,self_marked,created_at,updated_at)
             VALUES (?,?,?,CURDATE(),?,1,NOW(),NOW())
             ON DUPLICATE KEY UPDATE status=VALUES(status),self_marked=1,updated_at=NOW()",
            [$tid,$student['student_id'],$slotId,$status]
        );
        $this->json(['success'=>true]);
    }

    public function scanQr(): void
    {
        $student = $this->getStudent();
        $tid     = auth_user()['tenant_id'];

        $slotId = (int)$this->input('slot_id');
        $date   = $this->input('date');
        $token  = $this->input('token');

        // Verify token
        $expectedToken = md5($tid . $slotId . $date . env('APP_SECRET', 'MessIndiaSecretKey2026'));
        if ($token !== $expectedToken) {
            $this->abort(403, 'Invalid or expired QR code.');
        }

        // Verify slot
        $slot = DB::queryOne("SELECT m.slot_id, m.name FROM meal_slots m
            JOIN student_meal_slots sms ON sms.slot_id=m.slot_id
            WHERE m.slot_id=? AND m.tenant_id=? AND sms.student_id=? AND m.is_active=1",
            [$slotId, $tid, $student['student_id']]);
        if (!$slot) {
            $this->abort(404, 'Meal slot not found or not assigned to you.');
        }

        // Mark attendance
        DB::execute(
            "INSERT INTO student_attendance (tenant_id,student_id,slot_id,date,status,self_marked,created_at,updated_at)
             VALUES (?,?,?,?,'present',1,NOW(),NOW())
             ON DUPLICATE KEY UPDATE status='present',self_marked=1,updated_at=NOW()",
            [$tid, $student['student_id'], $slotId, $date]
        );

        flash('success', "Attendance marked successfully for {$slot['name']} on {$date}.");
        $this->redirect('student/attendance');
    }

    public function foodMenu(): void
    {
        $tid     = auth_user()['tenant_id'];
        $student = $this->getStudent();

        // All active slots
        $allSlots = DB::query("SELECT * FROM meal_slots WHERE tenant_id=? AND is_active=1 ORDER BY sort_order", [$tid]);

        // Student's assigned slot IDs
        $assignedRows = DB::query("SELECT slot_id FROM student_meal_slots WHERE student_id=? AND tenant_id=?",
            [$student['student_id'], $tid]);
        $assignedSlotIds = array_column($assignedRows, 'slot_id');

        // All food menu entries (including all slots, not just assigned)
        $menus = DB::query("SELECT ms.slot_id, ms.name AS slot_name, ms.slot_time, ms.meal_type, fm.items, fm.day_of_week
            FROM food_menu fm
            JOIN meal_slots ms ON ms.slot_id=fm.slot_id
            WHERE fm.tenant_id=? AND fm.is_active=1
            ORDER BY fm.day_of_week, ms.sort_order", [$tid]);

        $pageTitle = 'Food Menu';
        $this->view('student/food_menu', compact('menus', 'allSlots', 'assignedSlotIds', 'pageTitle'), 'app');
    }

    public function complaints(): void
    {
        $student    = $this->getStudent();
        $complaints = DB::query("SELECT * FROM complaints WHERE student_id=? AND tenant_id=? ORDER BY created_at DESC",
            [$student['student_id'],auth_user()['tenant_id']]);
        $pageTitle  = 'My Complaints';
        $this->view('student/complaints', compact('student','complaints','pageTitle'), 'app');
    }

    public function submitComplaint(): void
    {
        $this->verifyCsrf();
        $student = $this->getStudent();
        $errors  = $this->validate(['subject'=>'required|min:5','description'=>'required']);
        if ($errors) { flash('error',array_values($errors)[0]); $this->back(); }

        DB::insert('complaints',[
            'tenant_id'   => auth_user()['tenant_id'],
            'student_id'  => $student['student_id'],
            'subject'     => $this->input('subject'),
            'description' => $this->input('description'),
            'status'      => 'open',
            'priority'    => $this->input('priority','medium'),
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);
        flash('success','Complaint submitted successfully.');
        $this->redirect('student/complaints');
    }

    public function notifications(): void
    {
        $tid  = auth_user()['tenant_id'];
        $uid  = auth_user()['user_id'];
        $notifs = DB::query("SELECT * FROM notifications WHERE tenant_id=? AND (target_user_id=? OR target_user_id IS NULL) ORDER BY created_at DESC LIMIT 50",[$tid,$uid]);
        // Mark as read
        DB::execute("UPDATE notifications SET is_read=1 WHERE tenant_id=? AND (target_user_id=? OR target_user_id IS NULL)",[$tid,$uid]);
        $pageTitle = 'Notifications';
        $this->view('student/notifications', compact('notifs','pageTitle'), 'app');
    }

    public function membership(): void
    {
        $student     = $this->getStudent();
        $tid         = auth_user()['tenant_id'];
        $memberships = DB::query("SELECT m.*, mp.name AS plan_name, mp.price, mp.duration_days
            FROM memberships m JOIN membership_plans mp ON mp.plan_id=m.plan_id
            WHERE m.student_id=? AND m.tenant_id=? ORDER BY m.created_at DESC",
            [$student['student_id'],$tid]);
        $pageTitle   = 'My Membership';
        $this->view('student/membership', compact('student','memberships','pageTitle'), 'app');
    }

    public function updatePassword(): void
    {
        $this->verifyCsrf();
        $userId = auth_user()['user_id'];
        
        $errors = $this->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:6',
            'confirm_password' => 'required|same:new_password'
        ]);

        if ($errors) {
            flash('error', array_values($errors)[0]);
            $this->back();
        }

        $user = DB::queryOne("SELECT password_hash FROM users WHERE user_id = ?", [$userId]);
        if (!password_verify($this->input('current_password'), $user['password_hash'])) {
            flash('error', 'Current password is incorrect.');
            $this->back();
        }

        DB::update('users', [
            'password_hash' => password_hash($this->input('new_password'), PASSWORD_BCRYPT, ['cost' => 12]),
            'updated_at'    => date('Y-m-d H:i:s')
        ], ['user_id' => $userId]);

        flash('success', 'Password updated successfully.');
        $this->redirect('student/profile');
    }

    public function updatePhoto(): void
    {
        $this->verifyCsrf();
        $student = $this->getStudent();
        $userId  = auth_user()['user_id'];
        $tid     = auth_user()['tenant_id'];

        if (empty($_FILES['photo']['name'])) {
            flash('error', 'Please select a photo.');
            $this->back();
        }

        $file = $_FILES['photo'];
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            flash('error', 'Only JPG and PNG files are allowed.');
            $this->back();
        }

        $newName = 'student_' . $student['student_id'] . '_' . time() . '.' . $ext;
        $targetDir = 'uploads/students/';
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        
        $targetFile = $targetDir . $newName;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            // Delete old photo if exists
            if ($student['photo_path'] && file_exists($student['photo_path'])) {
                @unlink($student['photo_path']);
            }

            // Update student table
            DB::update('students', ['photo_path' => $targetFile], ['student_id' => $student['student_id']]);
            
            // Update user table avatar
            DB::update('users', ['avatar' => $targetFile], ['user_id' => $userId]);
            
            // Update session
            $_SESSION['avatar'] = $targetFile;

            flash('success', 'Profile photo updated successfully.');
        } else {
            flash('error', 'Failed to upload photo.');
        }

        $this->redirect('student/profile');
    }
}
