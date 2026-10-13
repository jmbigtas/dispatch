<?php
namespace App\Http\Controllers;
use App\Models\{Division, Role, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin() { return view('login'); }

    public function login(Request $r)
    {
        $c = $r->validate(['email' => 'required|email', 'password' => 'required']);
        if (Auth::attempt(['Email' => $c['email'], 'password' => $c['password']])) {
            $r->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }
        return back()->withErrors(['email' => 'Email or password is incorrect.'])->onlyInput('email');
    }

    public function showRegister() { return view('register', ['divisions' => Division::all()]); }

    public function register(Request $r)
    {
        $d = $r->validate([
            'FirstName' => 'required|string|max:100',
            'LastName' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:Users,Email',
            'Division' => 'required|exists:Division,DivisionID',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Self-registration always creates a plain "user". Only an admin can change roles later.
        $user = User::create([
            'FirstName' => $d['FirstName'], 'LastName' => $d['LastName'], 'Email' => $d['email'],
            'Division' => $d['Division'], 'passwordhash' => $d['password'],
            'Role' => Role::where('RoleName', 'user')->value('RoleID'),
        ]);

        Auth::login($user);
        $r->session()->regenerate();
        return redirect()->route('dashboard')->with('ok', 'Account created. Welcome aboard.');
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('login');
    }
}
