<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pembeli;
use App\Models\Keranjang;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class PembeliController extends Controller
{
    //

    public function index()
    {
        $barang = Barang::with('kategori')->paginate(6);
        if (Auth::guard('pembeli')->check()) {
            $user = Auth::guard('pembeli')->user();

            return view('pages.landing', ['user' => $user, 'barang' => $barang]);
        }
        return view('pages.landing', ['barang' => $barang]);
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
        $req['username'] = $req->uname;
        $req->validate([
            'username' => 'required|string|unique:pembeli,username|max:12',
            'fullname' => 'required|string|max:255',
            'password' => 'required|string|min:3'
        ]);
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
        $req['username'] = $req->uname;
        $req->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('pembeli')->attempt($req->only('username', 'password'))) {
            $req->session()->regenerate();

            // session()->getHandler()->destroy(Auth::guard('pembeli')->user()->getRememberToken());

            return redirect()->route('home')->with('req_ok', true);
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

        $req->validate([
            'uname' => 'string|unique:pembeli,username|max:12',
            'fullname' => 'required|string|max:255',
        ]);

        $user->username = $req->uname;
        $user->nama = $req->fullname;
        if ($req->password != '')
            $user->password = Hash::make($req->password);

        $user->save();

        return redirect()->back();
    }

    // keranjang
    public function add_cart(Request $req, $barang_id)
    {
        $user = Auth::guard('pembeli')->user();
        $keranjang = Keranjang::where('pembeli_id', $user->id)->where('barang_id', $barang_id)->first();

        if ($keranjang) {
            $keranjang->increment('jumlah');
        } else {
            Keranjang::create(['pembeli_id' => $user->id, 'barang_id' => $barang_id, 'jumlah' => 1]);
        }


        // $keranjang->barang()->syncWithoutDetaching([
        //     $barang_id => [
        //         'jumlah' => $keranjang->barang()->find($barang_id)?->pivot->jumlah + 1 ?? 1
        //     ]
        // ]);



        return redirect()->back()->with('add-cart', true);
    }
    public function decreased_cart(Request $req, $barang_id)
    {
        $keranjang = Keranjang::where('pembeli_id', Auth::guard('pembeli')->user()->id)->where('barang_id', $barang_id)->first();

        if ($keranjang && $keranjang->jumlah > 1) {
            $keranjang->decrement('jumlah');
        } elseif ($keranjang) {
            $keranjang->delete();
        }


        // $keranjang->barang()->syncWithoutDetaching([
        //     $barang_id => [
        //         'jumlah' => $keranjang->barang()->find($barang_id)?->pivot->jumlah + 1 ?? 1
        //     ]
        // ]);



        return redirect()->back()->with('dec-cart', true);
    }
}
