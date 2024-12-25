<?php

namespace App\Http\Controllers;

use App\Models\Pembeli;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class PembeliController extends Controller
{
    //

    public function index()
    {
        if (Auth::guard('pembeli')->check()) {
            $user = Auth::guard('pembeli')->user();

            return view('pages.landing', ['user' => $user]);
        }
        return view('pages.landing');
    }

    public function form_login()
    {
        return view('pages.login');
    }
    public function form_register()
    {
        return view('pages.daftar');
    }

    public function register(Request $req)
    {
        // $reqVal = $req->validate([
        //     'uname' => 'required|string|unique:pembeli,username|max:30',
        //     'fullname' => 'required|string|max:255',
        //     'password' => 'required|min:3|confirmed'
        // ]);
        $isExist = Pembeli::where('username', $req->uname)->first();
        if ($isExist) {
            return redirect()->back()->with('error', 'username sudah terdaftar, gunakan username lain.');
        }

        // $reqVal['password'] = Hash::make($reqVal['password']);

        Pembeli::create([
            'username' => $req->uname,
            'nama' => $req->fullname,
            'password' => Hash::make(
                $req->password
            )
        ]);

        return redirect()->route('login.page')->with('req_ok', true);
    }

    public function auth_login(Request $req)
    {
        // $reqVal = $req->validate([
        //     'username' => 'required|string',
        //     'password' => 'required|string',
        // ]);

        if (Auth::guard('pembeli')->attempt($req->only('username', 'password'))) {
            $req->session()->regenerate();

            // session()->getHandler()->destroy(Auth::guard('pembeli')->user()->getRememberToken());

            return redirect()->route('home');
        }

        return back()->with(['error' => 'username/password salah']);
    }

    public function logout(Request $req)
    {
        Auth::guard('pembeli')->logout();
        $req->session()->invalidate();
        $req->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function show_profile()
    {
        if (Auth::guard('pembeli')->check()) {
            $user = Auth::guard('pembeli')->user();

            return view('pages.profil', ['user' => $user]);
        }
    }
    public function update_profile(Request $req, $id)
    {
        $user = Pembeli::findOrFail($id);

        $user->username = $req->uname;
        $user->nama = $req->fullname;
        if ($req->password != '')
            $user->password = Hash::make($req->password);

        $user->save();

        return redirect()->back();
    }
}
