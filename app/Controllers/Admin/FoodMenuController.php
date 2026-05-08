<?php
namespace App\Controllers\Admin;

use DB;
use App\Core\Controller;

class FoodMenuController extends Controller
{
    public function __construct() { $this->requireModule('food_menu'); }

    public function index(): void
    {
        $tid   = auth_user()['tenant_id'];
        $menus = DB::query("SELECT fm.*, ms.name AS slot_name, ms.slot_time FROM food_menu fm JOIN meal_slots ms ON ms.slot_id=fm.slot_id WHERE fm.tenant_id=? ORDER BY fm.day_of_week, ms.sort_order",[$tid]);
        $slots = DB::query("SELECT * FROM meal_slots WHERE tenant_id=? AND is_active=1 ORDER BY sort_order",[$tid]);
        $pageTitle = 'Food Menu';
        $this->view('admin/food_menu/index', compact('menus','slots','pageTitle'), 'app');
    }

    public function store(): void
    {
        $this->verifyCsrf();
        $tid = auth_user()['tenant_id'];
        DB::execute("INSERT INTO food_menu (tenant_id,slot_id,day_of_week,menu_date,items,is_active,created_by,created_at,updated_at)
            VALUES (?,?,?,?,?,1,?,NOW(),NOW())
            ON DUPLICATE KEY UPDATE items=VALUES(items),updated_at=NOW()",
            [$tid,(int)$this->input('slot_id'),$this->input('day_of_week')??null,$this->input('menu_date')??null,
             $this->input('items'),auth_user()['user_id']]);
        if ($this->isAjax()) { $this->json(['success'=>true]); }
        flash('success','Menu saved.'); $this->redirect('admin/food-menu');
    }
}
