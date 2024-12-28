<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Pembeli;
use App\Models\Keranjang;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class PembeliController extends Controller
{
    //

    public function index()
    {
        return redirect()->route('search');
    }

    public function filter_index(Request $req)
    {
        $kategori = Kategori::all();
        $cari = $req->input('query') ?? '';

        $barang = Barang::with('kategori')->where('nama', 'like', '%' . $cari . '%')->orWhereHas('kategori', function ($query) use ($cari) {
            $query->where('nama', 'like', '%' . $cari . '%');
        })->paginate(6);
        if (Auth::guard('pembeli')->check()) {
            $user = Auth::guard('pembeli')->user();

            return view('pages.landing', ['user' => $user, 'barang' => $barang, 'cari' => $cari, 'kategori' => $kategori]);
        }
        return view('pages.landing', ['barang' => $barang, 'cari' => $cari, 'kategori' => $kategori]);
    }

    public function form_login()
    {
        if (Auth::guard('pembeli')->check())
            return redirect()->route('home');
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
        $user = Auth::guard('pembeli')->user();

        return view('pages.profil', ['user' => $user]);
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



        return redirect()->back()->with('dec-cart', true);
    }

    function show_cart()
    {
        $user = Auth::guard('pembeli')->user();
        $keranjang = Keranjang::where('pembeli_id', $user->id)->get();
        return view('pages.keranjang',  ['user' => Auth::guard('pembeli')->user(), 'data' => $keranjang, 'total' => $keranjang->sum(function ($e) {
            return $e->jumlah * $e->barang->harga;
        })]);
    }
    // function transaksi_showFor($id)
    // {
    //     $transaksi = Transaksi::with('transaksiDetail.barang')->findOrFail($id);

    //     return view('transaksi', ['transaksi' => $transaksi]);
    // }
    function transaksi_show()
    {
        $transaksi = Transaksi::with('transaksiDetail.barang')->where('pembeli_id', Auth::guard('pembeli')->user()->id)->orderByRaw("
        CASE
            WHEN status = 'menunggu' THEN 1
            WHEN status = 'pengemasan' THEN 2
            WHEN status = 'pengiriman' THEN 3
            WHEN status = 'selesai' THEN 4
            WHEN status = 'dibatalkan' THEN 5
            ELSE 6
        END
    ")
            ->orderBy('created_at', 'desc')->paginate(10);

        return view('pages.transaksi', ['data' => $transaksi, 'user' => Auth::guard('pembeli')->user()]);
    }
    function transaksi_create()
    {
        $keranjang = Keranjang::where('pembeli_id', Auth::guard('pembeli')->user()->id)->get();

        if ($keranjang->count() == 0)
            return redirect()->route('keranjang.show');

        DB::beginTransaction();
        try {


            $transaksi = Transaksi::create([
                'pembeli_id' => Auth::guard('pembeli')->user()->id,
                'status' => 'menunggu'
            ]);

            foreach ($keranjang as $k) {
                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'barang_id' => $k->barang->id,
                    'jumlah' => $k->jumlah
                ]);
            }
            Keranjang::where('pembeli_id', $transaksi->pembeli_id)->delete();
            DB::commit();
            return redirect()->route('transaksi.show');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('transaksi.show')->with('error', $e->getMessage());
        }
    }
    function transaksi_remove(Request $req, $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->status = 'dibatalkan';
        $transaksi->save();
        return redirect()->route('transaksi.show')->with('del', true);
    }
    function transaksi_unggah_bukti(Request $req, $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $req->validate([
            'bukti' => 'nullable|image|max:12132',
        ]);
        if ($req->hasFile('bukti')) {
            $transaksi['bukti'] = $req->file('bukti')->store('bukti', 'public');
        }
        $transaksi->save();
        return redirect()->route('transaksi.show')->with('unggah', true);
    }
}
