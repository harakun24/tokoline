<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KategoriController extends Controller
{
    //
    public function show_kategori()
    {
        return redirect()->route('kelola.panel.filter.kategori');
    }
    public function filter_kategori(Request $req)
    {
        $cari = $req->input('query') ?? '';
        $filter = Kategori::where('nama', 'like', '%' . $cari . '%')->paginate(6);

        return view('pages.gudang.kategori', ['data' => $filter, 'user' => Auth::guard('karyawan')->user(), 'kategori' => Kategori::all(), 'cari' => $cari]);
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
