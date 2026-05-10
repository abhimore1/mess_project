<?php
namespace App\Controllers\Admin;

use DB;

use App\Core\Controller;

class MealSlotController extends Controller
{
    public function __construct()
    {
        if (!module_enabled('attendance') && !module_enabled('food_menu')) {
            $this->abort(403, 'Module disabled or not subscribed.');
        }
    }

    public function index(): void
    {
        $tid   = auth_user()['tenant_id'];
        $slots = DB::query("SELECT * FROM meal_slots WHERE tenant_id=? ORDER BY sort_order, slot_id",[$tid]);
        $pageTitle = 'Meal Slots';
        $this->view('admin/meal_slots/index', compact('slots','pageTitle'), 'app');
    }

    public function store(): void
    {
        $this->verifyCsrf();
        $this->requirePermission('settings.manage');
        $tid = auth_user()['tenant_id'];

        $errors = $this->validate(['name'=>'required|min:2','slot_time'=>'required']);
        if ($errors) { $this->json(['success'=>false,'errors'=>$errors]); }

        // Determine sort order
        $maxOrder = DB::queryOne("SELECT MAX(sort_order) AS m FROM meal_slots WHERE tenant_id=?",[$tid])['m'] ?? 0;

        $id = DB::insert('meal_slots',[
            'tenant_id'  => $tid,
            'name'       => $this->input('name'),
            'slot_time'  => $this->input('slot_time'),
            'meal_type'  => $this->input('meal_type','other'),
            'sort_order' => $maxOrder + 1,
            'is_active'  => 1,
            'created_by' => auth_user()['user_id'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        log_activity('meal_slot.created','meal_slots',$id);
        if ($this->isAjax()) { $this->json(['success'=>true,'slot_id'=>$id]); }
        flash('success','Meal slot added.');
        $this->redirect('admin/meal-slots');
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();
        $this->requirePermission('settings.manage');
        $tid = auth_user()['tenant_id'];

        $slot = DB::queryOne("SELECT * FROM meal_slots WHERE slot_id=? AND tenant_id=?", [(int)$id, $tid]);
        if (!$slot) $this->abort(404);

        $name = $this->input('name');
        $slotTime = $this->input('slot_time');
        $mealType = $this->input('meal_type');
        $isActive = $this->input('is_active');

        $updateData = ['updated_at' => date('Y-m-d H:i:s')];
        if ($name !== null) $updateData['name'] = $name;
        if ($slotTime !== null) $updateData['slot_time'] = $slotTime;
        if ($mealType !== null) $updateData['meal_type'] = $mealType;
        if ($isActive !== null) $updateData['is_active'] = (int)(bool)$isActive;

        DB::update('meal_slots', $updateData, ['slot_id'=>(int)$id,'tenant_id'=>$tid]);

        if ($this->isAjax()) { $this->json(['success'=>true]); }
        flash('success','Meal slot updated.');
        $this->redirect('admin/meal-slots');
    }

    public function delete(string $id): void
    {
        $this->verifyCsrf();
        $this->requirePermission('settings.manage');
        DB::delete('meal_slots',['slot_id'=>(int)$id,'tenant_id'=>auth_user()['tenant_id']]);
        if ($this->isAjax()) { $this->json(['success'=>true]); }
        flash('success','Meal slot deleted.');
        $this->redirect('admin/meal-slots');
    }
}
