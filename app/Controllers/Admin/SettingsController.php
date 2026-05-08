<?php
namespace App\Controllers\Admin;

use DB;

use App\Core\Controller;

class SettingsController extends Controller
{
    public function index(): void
    {
        $tid  = auth_user()['tenant_id'];
        $rows = DB::query("SELECT setting_key, setting_value, setting_group FROM mess_settings WHERE tenant_id=? ORDER BY setting_group, setting_key",[$tid]);
        $settings = [];
        foreach ($rows as $r) $settings[$r['setting_key']] = $r['setting_value'];

        $tenant = DB::queryOne("SELECT owner_name, primary_color, logo FROM tenants WHERE tenant_id=?", [$tid]);
        
        $slots = DB::query("SELECT * FROM meal_slots WHERE tenant_id=? ORDER BY sort_order",[$tid]);
        $pageTitle = 'Mess Settings';
        $this->view('admin/settings/index', compact('settings','slots','pageTitle','tenant'), 'app');
    }

    public function save(): void
    {
        $this->verifyCsrf();
        $this->requirePermission('settings.manage');
        $tid  = auth_user()['tenant_id'];

        $allowed = ['mess_name','mess_address','mess_phone','mess_email','currency_symbol','timezone','student_login','date_format'];

        foreach ($allowed as $key) {
            $val = $this->input($key);
            if ($val === null) continue;
            DB::execute(
                "INSERT INTO mess_settings (tenant_id,setting_key,setting_value,setting_group,created_at,updated_at)
                 VALUES (?,?,?,'general',NOW(),NOW())
                 ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value),updated_at=NOW()",
                [$tid, $key, $val]
            );
        }

        // Update tenant branding & registration info
        $tenantUpdates = [];
        if ($this->input('primary_color')) $tenantUpdates['primary_color'] = $this->input('primary_color');
        if ($this->input('owner_name'))    $tenantUpdates['owner_name']    = $this->input('owner_name');
        if ($this->input('mess_name'))     $tenantUpdates['name']          = $this->input('mess_name');

        // Handle Logo Upload
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $f = $_FILES['logo'];
            $ext  = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
            
            if (in_array($ext, $allowed)) {
                $newName = 'logo_' . $tid . '_' . time() . '.' . $ext;
                $uploadDir = ROOT_PATH . '/public/uploads/logos/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                
                if (move_uploaded_file($f['tmp_name'], $uploadDir . $newName)) {
                    $tenantUpdates['logo'] = 'uploads/logos/' . $newName;
                    $_SESSION['tenant_logo'] = $tenantUpdates['logo'];
                }
            }
        }

        if (!empty($tenantUpdates)) {
            $tenantUpdates['updated_at'] = date('Y-m-d H:i:s');
            DB::update('tenants', $tenantUpdates, ['tenant_id' => $tid]);
            
            // Sync session if color changed
            if (isset($tenantUpdates['primary_color'])) {
                $_SESSION['primary_color'] = $tenantUpdates['primary_color'];
            }
        }

        log_activity('settings.saved','mess_settings',0);
        flash('success','Settings saved successfully.');
        $this->redirect('admin/settings');
    }
}
