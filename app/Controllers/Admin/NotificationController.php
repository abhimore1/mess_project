<?php
namespace App\Controllers\Admin;

use DB;
use App\Core\Controller;

class NotificationController extends Controller
{
    public function __construct() { $this->requireModule('notifications'); }

    public function index(): void
    {
        $tid   = auth_user()['tenant_id'];
        $notifs = DB::query("SELECT n.*, u.full_name AS sender FROM notifications n LEFT JOIN users u ON u.user_id=n.created_by WHERE n.tenant_id=? ORDER BY n.created_at DESC LIMIT 50",[$tid]);
        $pageTitle = 'Notifications';
        $this->view('admin/notifications/index', compact('notifs','pageTitle'), 'app');
    }

    public function send(): void
    {
        $this->verifyCsrf();
        $tid = auth_user()['tenant_id'];
        DB::insert('notifications',[
            'tenant_id'    => $tid,
            'target_role'  => $this->input('target_role','all'),
            'target_user_id'=> $this->input('target_user_id') ?: null,
            'title'        => $this->input('title'),
            'message'      => $this->input('message'),
            'type'         => $this->input('type','info'),
            'channel'      => 'in_app',
            'is_read'      => 0,
            'created_by'   => auth_user()['user_id'],
            'created_at'   => date('Y-m-d H:i:s'),
        ]);
        if ($this->isAjax()) { $this->json(['success'=>true]); }
        flash('success','Notification sent.'); $this->back();
    }

    public function unreadCount(): void
    {
        $uid = auth_user()['user_id'] ?? 0;
        $tid = auth_user()['tenant_id'] ?? 0;
        $count = DB::queryOne("SELECT COUNT(*) AS c FROM notifications WHERE tenant_id=? AND is_read=0 AND (target_user_id=? OR target_user_id IS NULL)",[$tid,$uid])['c']??0;
        $this->json(['count'=>$count]);
    }
}
