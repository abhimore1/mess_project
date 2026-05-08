<?php
namespace App\Controllers\Admin;

use DB;

use App\Core\Controller;

class StudentController extends Controller
{
    public function index(): void
    {
        $page   = (int)($this->input('page', 1));
        $search = trim($this->input('q', ''));
        $status = $this->input('status', 'active');
        $tid    = auth_user()['tenant_id'];

        $sql    = "SELECT s.*, m.end_date AS membership_end, mp.name AS plan_name, ay.year_name
                   FROM students s
                   LEFT JOIN memberships m ON m.student_id=s.student_id AND m.status='active'
                   LEFT JOIN membership_plans mp ON mp.plan_id=m.plan_id
                   LEFT JOIN academic_years ay ON ay.year_id=s.year_id
                   WHERE s.tenant_id=?";
        $params = [$tid];

        if ($search) { $sql .= " AND (s.full_name LIKE ? OR s.phone LIKE ? OR s.reg_number LIKE ?)"; $params = array_merge($params,["%$search%","%$search%","%$search%"]); }
        if ($status)  { $sql .= " AND s.status=?"; $params[] = $status; }

        $total  = DB::queryOne("SELECT COUNT(*) AS c FROM ($sql) x", $params)['c'] ?? 0;
        $perPage= 20;
        $offset = ($page-1)*$perPage;
        $students = DB::query("$sql ORDER BY s.full_name ASC LIMIT $perPage OFFSET $offset", $params);
        $pagination=['current_page'=>$page,'last_page'=>(int)ceil($total/$perPage),'total'=>$total,'per_page'=>$perPage];

        $pageTitle = 'Students';
        if ($this->isAjax()) { $this->json(['data'=>$students,'pagination'=>$pagination]); }
        $this->view('admin/students/index', compact('students','pagination','search','status','pageTitle'), 'app');
    }

    public function create(): void
    {
        $tid   = auth_user()['tenant_id'];
        $years = \DB::query("SELECT * FROM academic_years WHERE tenant_id=? AND is_active=1 ORDER BY year_name DESC", [$tid]);

        // Suggest next registration number
        $last  = \DB::queryOne("SELECT reg_number FROM students WHERE tenant_id=? ORDER BY student_id DESC LIMIT 1", [$tid]);
        $num   = $last ? (int)preg_replace('/\D/', '', $last['reg_number'] ?? '0') + 1 : 1;
        $suggestReg = 'STU-' . str_pad($num, 5, '0', STR_PAD_LEFT);

        // Pre-select active year
        $activeYear = \DB::queryOne("SELECT year_id FROM academic_years WHERE tenant_id=? AND is_active=1 ORDER BY year_id DESC LIMIT 1", [$tid]);
        $defaultYearId = $activeYear['year_id'] ?? null;

        $pageTitle = 'Add Student';
        $this->view('admin/students/create', compact('pageTitle','years','suggestReg','defaultYearId'), 'app');
    }

    public function store(): void
    {
        $this->verifyCsrf();
        $this->requirePermission('students.create');

        $errors = $this->validate(['full_name'=>'required|min:2','phone'=>'required']);
        if ($errors) { flash('error', array_values($errors)[0]); $this->back(); }

        $tid = auth_user()['tenant_id'];
        $phone = $this->input('phone');
        $email = $this->input('email');

        // Check for duplicates before manual registration
        $checkSql = "SELECT student_id FROM students WHERE tenant_id = ? AND (phone = ?";
        $checkParams = [$tid, $phone];
        if ($email) {
            $checkSql .= " OR email = ?";
            $checkParams[] = $email;
        }
        $checkSql .= ") LIMIT 1";

        if (DB::queryOne($checkSql, $checkParams)) {
            flash('error', 'A student with this phone number or email already exists.');
            $this->back();
        }

        // Handle photo upload
        $photoPath = null;
        if (!empty($_FILES['photo']['tmp_name'])) {
            $photoPath = $this->uploadFile('photo', "uploads/{$tid}/students/");
        }

        $studentId = DB::insert('students',[
            'tenant_id'         => $tid,
            'year_id'           => $this->input('year_id') ?: null,
            'reg_number'        => $this->input('reg_number') ?: 'STU-'.date('Ymd').'-'.rand(100,999),
            'full_name'         => $this->input('full_name'),
            'phone'             => $this->input('phone'),
            'email'             => $this->input('email'),
            'guardian_name'     => $this->input('guardian_name'),
            'guardian_phone'    => $this->input('guardian_phone'),
            'blood_group'       => $this->input('blood_group'),
            'address'           => $this->input('address'),
            'dob'               => $this->input('dob') ?: null,
            'gender'            => $this->input('gender','male'),
            'emergency_contact' => $this->input('emergency_contact'),
            'room_number'       => $this->input('room_number'),
            'photo_path'        => $photoPath,
            'status'            => 'active',
            'joined_at'         => $this->input('joined_at', date('Y-m-d')),
            'created_by'        => auth_user()['user_id'],
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);

        // Create student login if enabled
        if (get_setting('student_login') === '1' && $this->input('email')) {
            $roleId = DB::queryOne("SELECT role_id FROM roles WHERE slug='student' LIMIT 1")['role_id'];
            DB::insert('users',[
                'tenant_id'     => $tid,
                'role_id'       => $roleId,
                'email'         => $this->input('email'),
                'password_hash' => password_hash($this->input('phone'), PASSWORD_BCRYPT, ['cost'=>12]),
                'full_name'     => $this->input('full_name'),
                'status'        => 'active',
                'created_by'    => auth_user()['user_id'],
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        log_activity('student.created','students',$studentId);
        flash('success','Student added successfully! Default password is their phone number.');
        $this->redirect('admin/students');
    }

    public function show(string $id): void
    {
        $tid     = auth_user()['tenant_id'];
        $student = DB::queryOne("SELECT * FROM students WHERE student_id=? AND tenant_id=?", [(int)$id,$tid]);
        if (!$student) $this->abort(404);

        $memberships = DB::query("SELECT m.*, mp.name AS plan_name FROM memberships m JOIN membership_plans mp ON mp.plan_id=m.plan_id WHERE m.student_id=? AND m.tenant_id=? ORDER BY m.created_at DESC", [(int)$id,$tid]);
        $payments    = DB::query("SELECT * FROM payments WHERE student_id=? AND tenant_id=? ORDER BY payment_date DESC LIMIT 10", [(int)$id,$tid]);
        $attendance  = DB::query("SELECT sa.*, ms.name AS slot_name FROM student_attendance sa JOIN meal_slots ms ON ms.slot_id=sa.slot_id WHERE sa.student_id=? AND sa.tenant_id=? ORDER BY sa.date DESC LIMIT 30", [(int)$id,$tid]);

        $pageTitle = $student['full_name'];
        $this->view('admin/students/show', compact('student','memberships','payments','attendance','pageTitle'), 'app');
    }

    public function edit(string $id): void
    {
        $tid     = auth_user()['tenant_id'];
        $student = DB::queryOne("SELECT * FROM students WHERE student_id=? AND tenant_id=?", [(int)$id,$tid]);
        if (!$student) $this->abort(404);
        
        $years = DB::query("SELECT * FROM academic_years WHERE tenant_id=? AND is_active=1 ORDER BY year_name DESC", [$tid]);
        
        $pageTitle = 'Edit — ' . $student['full_name'];
        $this->view('admin/students/edit', compact('student','years','pageTitle'), 'app');
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();
        $this->requirePermission('students.edit');
        $tid = auth_user()['tenant_id'];
        $phone = $this->input('phone');
        $email = $this->input('email');

        // Check for duplicates (excluding current student)
        $checkSql = "SELECT student_id FROM students WHERE tenant_id = ? AND student_id != ? AND (phone = ?";
        $checkParams = [$tid, (int)$id, $phone];
        if ($email) {
            $checkSql .= " OR email = ?";
            $checkParams[] = $email;
        }
        $checkSql .= ") LIMIT 1";

        if (DB::queryOne($checkSql, $checkParams)) {
            flash('error', 'Another student already uses this phone number or email.');
            $this->back();
        }

        $photoPath = DB::queryOne("SELECT photo_path FROM students WHERE student_id=? AND tenant_id=?",[(int)$id,$tid])['photo_path'];
        if (!empty($_FILES['photo']['tmp_name'])) {
            $photoPath = $this->uploadFile('photo', "uploads/{$tid}/students/");
        }

        DB::update('students',[
            'year_id'           => $this->input('year_id') ?: null,
            'full_name'         => $this->input('full_name'),
            'phone'             => $this->input('phone'),
            'email'             => $this->input('email'),
            'guardian_name'     => $this->input('guardian_name'),
            'guardian_phone'    => $this->input('guardian_phone'),
            'blood_group'       => $this->input('blood_group'),
            'address'           => $this->input('address'),
            'dob'               => $this->input('dob') ?: null,
            'gender'            => $this->input('gender','male'),
            'emergency_contact' => $this->input('emergency_contact'),
            'room_number'       => $this->input('room_number'),
            'photo_path'        => $photoPath,
            'status'            => $this->input('status','active'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ],['student_id'=>(int)$id,'tenant_id'=>$tid]);

        log_activity('student.updated','students',(int)$id);
        flash('success','Student updated successfully.');
        $this->redirect("admin/students/$id");
    }

    public function delete(string $id): void
    {
        $this->verifyCsrf();
        $this->requirePermission('students.delete');
        DB::update('students',['status'=>'left','left_at'=>date('Y-m-d'),'updated_at'=>date('Y-m-d H:i:s')],
            ['student_id'=>(int)$id,'tenant_id'=>auth_user()['tenant_id']]);
        log_activity('student.deactivated','students',(int)$id);
        flash('success','Student marked as left.');
        $this->redirect('admin/students');
    }

    public function ajaxList(): void
    {
        $this->index();
    }

    public function downloadTemplate(): void
    {
        // Build a real .xlsx (OOXML) using ZipArchive — no warnings in any Excel version
        $tmp = tempnam(sys_get_temp_dir(), 'xlsx_');

        $cols = [
            // [label, col-width, style-index]  styles: 1=header 2=cell 3=text 4=required
            ['Full Name *',       18, 4],
            ['Email',             24, 2],
            ['Phone *',           16, 3],
            ['Room Number',       14, 2],
            ['Registration No',   16, 2],
            ['Gender',            10, 2],
            ['DOB',               14, 2],
            ['Blood Group',       12, 2],
            ['Address',           30, 2],
            ['Guardian Name',     18, 2],
            ['Guardian Phone',    16, 3],
            ['Emergency Contact', 16, 3],
            ['Joining Date',      16, 2],
            ['Academic Year',     14, 2],
        ];
        $sample = ['John Doe','john@example.com','9876543210','A-101','STU-00001','Male','2000-01-01','O+','123 Main Street, City','Jane Doe','9876543211','9876543212','2024-05-01','2024-25'];

        $esc     = fn(string $s): string => htmlspecialchars($s, ENT_XML1, 'UTF-8');
        $colName = function(int $n): string {
            $s = '';
            do { $s = chr(65 + ($n % 26)) . $s; $n = intdiv($n, 26) - 1; } while ($n >= 0);
            return $s;
        };

        // Content types
        $ct = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
 <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
 <Default Extension="xml"  ContentType="application/xml"/>
 <Override PartName="/xl/workbook.xml"          ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
 <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
 <Override PartName="/xl/styles.xml"            ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>';

        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
 <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';

        $wb = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
 <sheets><sheet name="Students" sheetId="1" r:id="rId1"/></sheets>
</workbook>';

        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
 <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
 <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles"    Target="styles.xml"/>
</Relationships>';

        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
 <numFmts count="1"><numFmt numFmtId="164" formatCode="@"/></numFmts>
 <fonts count="2">
  <font><sz val="11"/><name val="Calibri"/></font>
  <font><b/><sz val="11"/><color rgb="FF1E3A5F"/><name val="Calibri"/></font>
 </fonts>
 <fills count="4">
  <fill><patternFill patternType="none"/></fill>
  <fill><patternFill patternType="gray125"/></fill>
  <fill><patternFill patternType="solid"><fgColor rgb="FFDBEAFE"/></patternFill></fill>
  <fill><patternFill patternType="solid"><fgColor rgb="FFFEF9C3"/></patternFill></fill>
 </fills>
 <borders count="2">
  <border><left/><right/><top/><bottom/><diagonal/></border>
  <border><left style="thin"><color rgb="FFD1D5DB"/></left><right style="thin"><color rgb="FFD1D5DB"/></right><top style="thin"><color rgb="FFD1D5DB"/></top><bottom style="thin"><color rgb="FFD1D5DB"/></bottom><diagonal/></border>
 </borders>
 <cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>
 <cellXfs count="5">
  <xf numFmtId="0"   fontId="0" fillId="0" borderId="0" xfId="0"/>
  <xf numFmtId="0"   fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center" vertical="center"/></xf>
  <xf numFmtId="0"   fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"><alignment vertical="center"/></xf>
  <xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1" applyNumberFormat="1"><alignment vertical="center"/></xf>
  <xf numFmtId="164" fontId="0" fillId="3" borderId="1" xfId="0" applyFill="1" applyBorder="1" applyNumberFormat="1"><alignment vertical="center"/></xf>
 </cellXfs>
</styleSheet>';

        // Build sheet XML
        $colDefs = '';
        foreach ($cols as $i => $c) {
            $colDefs .= '<col min="'.($i+1).'" max="'.($i+1).'" width="'.$c[1].'" customWidth="1"/>';
        }

        $headerRow = '<row r="1" ht="22" customHeight="1">';
        foreach ($cols as $i => $c) {
            $headerRow .= '<c r="'.$colName($i).'1" s="1" t="inlineStr"><is><t>'.$esc($c[0]).'</t></is></c>';
        }
        $headerRow .= '</row>';

        $sampleRow = '<row r="2" ht="18" customHeight="1">';
        foreach ($cols as $i => $c) {
            $sampleRow .= '<c r="'.$colName($i).'2" s="'.$c[2].'" t="inlineStr"><is><t>'.$esc($sample[$i]).'</t></is></c>';
        }
        $sampleRow .= '</row>';

        $blankRows = '';
        for ($r = 3; $r <= 52; $r++) {
            $blankRows .= '<row r="'.$r.'" ht="18" customHeight="1">';
            foreach ($cols as $i => $c) {
                $blankRows .= '<c r="'.$colName($i).$r.'" s="'.$c[2].'" t="inlineStr"><is><t></t></is></c>';
            }
            $blankRows .= '</row>';
        }

        $sheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
 <sheetFormatPr defaultRowHeight="15"/>
 <cols>'.$colDefs.'</cols>
 <sheetData>'.$headerRow.$sampleRow.$blankRows.'</sheetData>
</worksheet>';

        $zip = new \ZipArchive();
        $zip->open($tmp, \ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml',        $ct);
        $zip->addFromString('_rels/.rels',                $rels);
        $zip->addFromString('xl/workbook.xml',            $wb);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);
        $zip->addFromString('xl/styles.xml',              $styles);
        $zip->addFromString('xl/worksheets/sheet1.xml',   $sheet);
        $zip->close();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename=students_import_template.xlsx');
        header('Content-Length: ' . filesize($tmp));
        header('Cache-Control: max-age=0');
        readfile($tmp);
        unlink($tmp);
        exit;
    }

    public function importExcel(): void

    {
        $this->verifyCsrf();
        $this->requirePermission('students.create');
        $tid = auth_user()['tenant_id'];

        if (empty($_FILES['import_file']['tmp_name'])) {
            flash('error', 'Please upload a CSV file.');
            $this->back();
        }

        $file = $_FILES['import_file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['csv', 'xlsx'])) {
            flash('error', 'Please upload an Excel (.xlsx) or CSV file.');
            $this->back();
        }

        $rows = [];
        if ($ext === 'xlsx') {
            $zip = new \ZipArchive();
            if ($zip->open($file['tmp_name']) === true) {
                $sharedStrings = [];
                if (($ssData = $zip->getFromName('xl/sharedStrings.xml')) !== false) {
                    $xml = simplexml_load_string($ssData);
                    foreach ($xml->si as $si) {
                        $val = '';
                        if (isset($si->t)) {
                            $val = (string)$si->t;
                        } elseif (isset($si->r)) {
                            foreach ($si->r as $r) {
                                if (isset($r->t)) $val .= (string)$r->t;
                            }
                        }
                        $sharedStrings[] = $val;
                    }
                }

                if (($sheetData = $zip->getFromName('xl/worksheets/sheet1.xml')) !== false) {
                    $xml = simplexml_load_string($sheetData);
                    foreach ($xml->sheetData->row as $xmlRow) {
                        $rowData = [];
                        $colIndex = 0;
                        foreach ($xmlRow->c as $c) {
                            $r = (string)$c['r'];
                            $alpha = preg_replace('/[0-9]/', '', $r);
                            $idx = 0;
                            for ($i = 0; $i < strlen($alpha); $i++) {
                                $idx = $idx * 26 + (ord($alpha[$i]) - 64);
                            }
                            $idx -= 1;

                            while ($colIndex < $idx) {
                                $rowData[] = '';
                                $colIndex++;
                            }

                            $val = (string)$c->v;
                            if (isset($c['t'])) {
                                $t = (string)$c['t'];
                                if ($t == 's') {
                                    $val = $sharedStrings[(int)$val] ?? '';
                                } elseif ($t == 'inlineStr' && isset($c->is)) {
                                    $val = '';
                                    if (isset($c->is->t)) {
                                        $val = (string)$c->is->t;
                                    } elseif (isset($c->is->r)) {
                                        foreach ($c->is->r as $r) {
                                            if (isset($r->t)) $val .= (string)$r->t;
                                        }
                                    }
                                }
                            }
                            $rowData[] = $val;
                            $colIndex++;
                        }
                        if (count(array_filter($rowData, fn($v) => trim((string)$v) !== '')) > 0) {
                            $rows[] = $rowData;
                        }
                    }
                }
                $zip->close();
            }
        } else {
            if (($handle = fopen($file['tmp_name'], 'r')) !== false) {
                while (($data = fgetcsv($handle, 2000, ",")) !== false) {
                    $rows[] = $data;
                }
                fclose($handle);
            }
        }

        if (count($rows) < 2) {
            flash('error', 'File is empty or could not be read. Ensure data starts after headers.');
            $this->back();
        }

        $rawHeaders = array_shift($rows);
        $headers = array_map(function($h) {
            return trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', (string)$h));
        }, $rawHeaders);

            // Map both human-readable headers AND snake_case keys to DB columns
            $headerMap = [
                'Full Name'         => 'full_name',
                'Full Name *'       => 'full_name',
                'full_name'         => 'full_name',
                'Email'             => 'email',
                'email'             => 'email',
                'Phone'             => 'phone',
                'Phone *'           => 'phone',
                'phone'             => 'phone',
                'Room Number'       => 'room_number',
                'room_number'       => 'room_number',
                'Registration No'   => 'reg_number',
                'reg_number'        => 'reg_number',
                'Guardian Name'     => 'guardian_name',
                'guardian_name'     => 'guardian_name',
                'Guardian Phone'    => 'guardian_phone',
                'guardian_phone'    => 'guardian_phone',
                'Emergency Contact' => 'emergency_contact',
                'emergency_contact' => 'emergency_contact',
                'Blood Group'       => 'blood_group',
                'blood_group'       => 'blood_group',
                'Address'           => 'address',
                'address'           => 'address',
                'Gender'            => 'gender',
                'gender'            => 'gender',
                'DOB'               => 'dob',
                'dob'               => 'dob',
                'Academic Year'     => 'academic_year',
                'academic_year'     => 'academic_year',
                'Joining Date'      => 'joined_at',
                'joining_date'      => 'joined_at',
            ];

            // Normalise headers to DB keys
            $dbHeaders = array_map(fn($h) => $headerMap[$h] ?? $h, $headers);

            // Lookup academic years for this tenant
            $yearRows = \DB::query("SELECT year_id, year_name, is_active FROM academic_years WHERE tenant_id=?", [$tid]);
            $yearLookup = [];
            $activeYearId = null;
            foreach ($yearRows as $y) {
                $yearLookup[strtolower(trim($y['year_name']))] = $y['year_id'];
                if ($y['is_active']) {
                    $activeYearId = $y['year_id'];
                }
            }
            \DB::beginTransaction();
            try {
                $count = 0;
                $skipped = 0;
                foreach ($rows as $data) {
                    // Pad or truncate to match header count
                    if (count($data) < count($dbHeaders)) {
                        $data = array_pad($data, count($dbHeaders), '');
                    }
                    $data = array_slice($data, 0, count($dbHeaders));
                    $row = array_combine($dbHeaders, $data);

                    $fullName = trim((string)($row['full_name'] ?? ''));
                    $phone = trim(ltrim((string)($row['phone'] ?? ''), "\t "));

                    if (empty($fullName) || empty($phone)) {
                        continue;
                    }

                    // Skip the sample row if the user forgot to delete it
                    if ($fullName === 'John Doe' && $phone === '9876543210' && ($row['email'] ?? '') === 'john@example.com') {
                        continue;
                    }

                    $reg = trim((string)($row['reg_number'] ?? ''));
                    $email = trim((string)($row['email'] ?? ''));

                    // Check for existing student with same phone OR same email
                    $checkSql = "SELECT student_id FROM students WHERE tenant_id = ? AND (phone = ?";
                    $checkParams = [$tid, $phone];
                    if ($email) {
                        $checkSql .= " OR email = ?";
                        $checkParams[] = $email;
                    }
                    $checkSql .= ") LIMIT 1";

                    $existing = \DB::queryOne($checkSql, $checkParams);
                    if ($existing) {
                        $skipped++;
                        continue; // Skip duplicates
                    }

                    if (!$reg) {
                        $lastStudent = \DB::queryOne("SELECT reg_number FROM students WHERE tenant_id=? ORDER BY student_id DESC LIMIT 1", [$tid]);
                        $num = $lastStudent ? (int)preg_replace('/\D/', '', $lastStudent['reg_number'] ?? '0') + 1 : 1;
                        $reg = 'STU-' . str_pad((string)$num, 5, '0', STR_PAD_LEFT);
                    }

                    $yearLabel = strtolower(trim((string)($row['academic_year'] ?? '')));
                    $yearId    = $yearLookup[$yearLabel] ?? $activeYearId;

                    $joinedAt = null;
                    if (!empty($row['joined_at'])) {
                        $time = strtotime($row['joined_at']);
                        if ($time) {
                            $joinedAt = date('Y-m-d', $time);
                        }
                    }

                    $dob = null;
                    if (!empty($row['dob'])) {
                        $time = strtotime($row['dob']);
                        if ($time) {
                            $dob = date('Y-m-d', $time);
                        }
                    }

                    $genderVal = strtolower(trim((string)($row['gender'] ?? '')));
                    $gender = in_array($genderVal, ['male','female','other']) ? $genderVal : null;

                    \DB::insert('students', [
                        'tenant_id'      => $tid,
                        'full_name'      => $fullName,
                        'email'          => trim((string)($row['email'] ?? '')),
                        'phone'          => $phone,
                        'room_number'    => trim((string)($row['room_number'] ?? '')),
                        'reg_number'        => $reg,
                        'guardian_name'     => trim((string)($row['guardian_name'] ?? '')),
                        'guardian_phone'    => trim(ltrim((string)($row['guardian_phone'] ?? ''), "\t ")),
                        'emergency_contact' => trim(ltrim((string)($row['emergency_contact'] ?? ''), "\t ")),
                        'blood_group'       => trim((string)($row['blood_group'] ?? '')),
                        'address'           => trim((string)($row['address'] ?? '')),
                        'gender'            => $gender,
                        'dob'               => $dob,
                        'year_id'           => $yearId,
                        'status'         => 'active',
                        'joined_at'      => $joinedAt,
                        'created_by'     => auth_user()['user_id'],
                        'created_at'     => date('Y-m-d H:i:s'),
                        'updated_at'     => date('Y-m-d H:i:s'),
                    ]);
                    $count++;
                }
                \DB::commit();
                $msg = "{$count} student(s) imported successfully.";
                if ($skipped > 0) {
                    $msg .= " {$skipped} duplicate record(s) were skipped (already exist).";
                }
                flash('success', $msg);
            } catch (\Throwable $e) {
                \DB::rollBack();
                flash('error', 'Import failed: ' . $e->getMessage());
            }
        $this->back();
    }


    public function searchSuggestions(): void
    {
        $tid    = auth_user()['tenant_id'];
        $query  = $this->input('q', '');
        
        if (strlen($query) < 2) {
            echo json_encode([]);
            return;
        }

        $students = \DB::query(
            "SELECT student_id, full_name, reg_number, phone 
             FROM students 
             WHERE tenant_id = ? 
             AND (full_name LIKE ? OR reg_number LIKE ? OR phone LIKE ?) 
             LIMIT 10",
            [$tid, "%$query%", "%$query%", "%$query%"]
        );

        $this->json($students);
    }

    public function exportExcel(): void
    {
        $tid = auth_user()['tenant_id'];
        $status = $this->input('status', '');
        $search = $this->input('q', '');
        
        $sql = "SELECT full_name, email, phone, room_number, reg_number, guardian_name, guardian_phone, blood_group, address, status, created_at FROM students WHERE tenant_id=?";
        $params = [$tid];

        if ($status) {
            $sql .= " AND status=?";
            $params[] = $status;
        }
        if ($search) {
            $sql .= " AND (full_name LIKE ? OR phone LIKE ? OR reg_number LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $sql .= " ORDER BY full_name ASC";
        $students = \DB::query($sql, $params);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=students_export_' . date('Ymd_His') . '.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Full Name', 'Email', 'Phone', 'Room Number', 'Registration No', 'Guardian Name', 'Guardian Phone', 'Blood Group', 'Address', 'Status', 'Added On']);
        
        foreach ($students as $row) {
            fputcsv($output, [
                $row['full_name'], $row['email'], $row['phone'], $row['room_number'], $row['reg_number'],
                $row['guardian_name'], $row['guardian_phone'], $row['blood_group'], $row['address'],
                $row['status'], date('Y-m-d', strtotime($row['created_at']))
            ]);
        }
        fclose($output);
        exit;
    }

    public function exportPdf(): void
    {
        $tid = auth_user()['tenant_id'];
        $status = $this->input('status', '');
        $search = $this->input('q', '');
        
        $sql = "SELECT * FROM students WHERE tenant_id=?";
        $params = [$tid];

        if ($status) {
            $sql .= " AND status=?";
            $params[] = $status;
        }
        if ($search) {
            $sql .= " AND (full_name LIKE ? OR phone LIKE ? OR reg_number LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $sql .= " ORDER BY full_name ASC";
        $students = \DB::query($sql, $params);
        $tenant = \DB::queryOne("SELECT name FROM tenants WHERE tenant_id=?", [$tid]);

        require_once APP_PATH . '/../vendor/tcpdf/tcpdf.php';
        
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Mess SaaS');
        $pdf->SetTitle('Students Report');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();
        
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, ($tenant['name'] ?? 'Mess') . ' - Students Report', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 10, 'Generated on: ' . date('d M Y h:i A'), 0, 1, 'C');
        $pdf->Ln(5);

        $html = '<table border="1" cellpadding="5">
            <thead>
                <tr style="background-color:#f0f0f0;font-weight:bold;">
                    <th width="30%">Name</th>
                    <th width="20%">Reg No</th>
                    <th width="20%">Phone</th>
                    <th width="15%">Room</th>
                    <th width="15%">Status</th>
                </tr>
            </thead>
            <tbody>';
            
        foreach ($students as $s) {
            $html .= '<tr>
                <td width="30%">' . htmlspecialchars($s['full_name']) . '</td>
                <td width="20%">' . htmlspecialchars($s['reg_number'] ?? '-') . '</td>
                <td width="20%">' . htmlspecialchars($s['phone']) . '</td>
                <td width="15%">' . htmlspecialchars($s['room_number'] ?? '-') . '</td>
                <td width="15%">' . ucfirst($s['status']) . '</td>
            </tr>';
        }
        
        $html .= '</tbody></table>';
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('students_report.pdf', 'D');
        exit;
    }

    private function uploadFile(string $field, string $dir): ?string
    {
        $allowed = ['image/jpeg','image/png','image/webp'];
        $file    = $_FILES[$field];
        if (!in_array($file['type'], $allowed)) return null;
        if ($file['size'] > 2097152) return null; // 2MB max

        $fullDir = PUBLIC_PATH . '/' . $dir;
        if (!is_dir($fullDir)) mkdir($fullDir, 0755, true);

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('', true) . '.' . $ext;
        move_uploaded_file($file['tmp_name'], $fullDir . $filename);
        return $dir . $filename;
    }
}
