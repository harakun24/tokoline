<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\Karyawan;
use App\Models\Kategori;

class AdminController extends Controller
{
    //
    public function form_login()
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

    //kelola barang untuk admin
    public function show_admin()
    {
        return view('pages.gudang.admin', ['user' => Auth::guard('karyawan')->user()]);
    }
    public function show_kategori()
    {
        return view('pages.gudang.kategori', ['data' => Kategori::paginate(7), 'user' => Auth::guard('karyawan')->user()]);
    }
    public function show_barang()
    {
        return view('pages.gudang.barang', ['data' => Barang::paginate(7), 'user' => Auth::guard('karyawan')->user(), 'kategori' => Kategori::all()]);
    }
    public function add_kategori(Request $req)
    {
        $req->validate(['nama' => 'required|string|max:12|unique:kategori,nama']);

        Kategori::create(['nama' => $req->nama]);

        return redirect()->route('kelola.panel.kategori')->with('add', true);
    }
    public function update_kategori(Request $req, $id)
    {
        $user = Kategori::findOrFail($id);

        $req->validate(['nama' => 'required|string|max:12|unique:kategori,nama']);

        $user->nama = $req->nama;
        $user->save();

        return redirect()->back()->with('up', true);
    }
    public function delete_kategori($id)
    {
        $user = Kategori::findOrFail($id);
        $user->delete();

        return redirect()->route('kelola.panel.kategori')->with('del', true);
    }
}
