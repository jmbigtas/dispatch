<?php
namespace App\Http\Controllers;
use App\Models\{Role, User};
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index() { return view('admin.users', ['users' => User::with(['roleRel', 'divisionRel'])->get(), 'roles' => Role::all()]); }

    public function update(Request $r, User $user)
    {
        $user->update($r->validate(['Role' => 'required|exists:Role,RoleID']));
        return back()->with('ok', "{$user->name} is now {$user->fresh()->role_label}.");
    }
}
