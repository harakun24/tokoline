<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\Karyawan;

class AdminController extends Controller
{
    //
    public function form_login(Request $req)
    {
        if (session()->get('su'))
            return redirect()->route('kelola.panel.super');

        if (Auth::guard('karyawan')->check()) {

            $user = Auth::guard('karyawan')->user();
            if ($user->role == 3)
                return redirect()->route('kelola.panel.admin');
            else if ($user->role == 1)
                return redirect()->route('kelola.panel.cs1');
            else if ($user->role == 2)
                return redirect()->route('kelola.panel.cs2');
        }
        return view('pages.gudang.login');
    }
    public function auth_login(Request $req)
    {
        $req->validate([
            'uname' => 'required|string',
            'password' => 'required|string',
        ]);

        $req['username'] = $req->uname;

        if ($req->uname == env('SU_USER') && $req->password == env('SU_PASSWORD')) {
            session(['su' => $req->only(['username', 'password'])]);

            return redirect()->route('kelola.panel.super')->with('req_ok', true);
        } else  if (Auth::guard('karyawan')->attempt($req->only('username', 'password'))) {
            $req->session()->regenerate();

            return redirect()->route('kelola.login.show');
        }
        return back()->withErrors(['Username/password salah']);
    }

    public function add_user(Request $req)
    {

        $req['username'] = $req->uname;

        $req->validate([
            'username' => 'required|string|unique:karyawan,username|max:12',
            'fullname' => 'required|string|max:255',
            'password' => 'required|string|min:3',
            'role' => 'required|integer',
        ]);

        Karyawan::create([
            'username' => $req->username,
            'nama' => $req->fullname,
            'password' => Hash::make($req->password),
            'role' => $req->role,
        ]);

        return redirect()->route('kelola.panel.super')->with('add', true);
    }

    public function show_super()
    {
        if (!session()->get('su'))
            return redirect()->route('kelola.login.show');

        return view('pages.gudang.super', ['data' => Karyawan::paginate(7)]);
    }
    public function show_admin()
    {
        $user = Auth::guard('karyawan')->user();
        if ($user->role != 3)
            return redirect()->route('kelola.login.show');
        return view('pages.gudang.admin', ['data' => Karyawan::paginate(7), 'user' => Auth::guard('karyawan')->user()]);
    }
    public function show_cs1()
    {
        return view('pages.gudang.cs1', ['data' => Karyawan::paginate(7)]);
    }
    public function show_cs2()
    {
        return view('pages.gudang.cs2', ['data' => Karyawan::paginate(7)]);
    }

    public function delete_user($id)
    {
        $user = Karyawan::findOrFail($id);
        $user->delete();

        return redirect()->route('kelola.panel.super')->with('del', true);
    }
    public function update_user(Request $req, $id)
    {
        $user = Karyawan::findOrFail($id);
        $uname = $user->username == $req->uname ? 'string|max:12' : 'string|unique:karyawan,username|max:12';

        $req->validate([
            'uname' => $uname,
            'fullname' => 'required|string|max:255',
            'role' => 'required|integer',
        ]);

        $user->username = $req->uname;
        $user->nama = $req->fullname;
        $user->role = $req->role;
        if ($req->password != '')
            $user->password = Hash::make($req->password);

        $user->save();

        return redirect()->back()->with('up', true);
    }
    public function logout(Request $req)
    {
        Auth::guard('karyawan')->logout();
        $req->session()->invalidate();
        $req->session()->regenerateToken();
        if (session()->get('su'))
            session()->remove('su');
        return redirect()->route('kelola.login.show');
    }
}
