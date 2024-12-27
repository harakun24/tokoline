<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use App\Models\Karyawan;
use App\Models\Kategori;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
    public function filter_admin(Request $req)
    {
        if (!session()->get('su'))
            return redirect()->route('kelola.login.show');

        $cari = $req->input('query');
        $filter = Karyawan::where('nama', 'like', '%' . $cari . '%')->orWhere('username', 'like', '%' . $cari . '%')->paginate(6);

        return view('pages.gudang.super', ['data' => $filter]);
    }
    // kategori
    public function show_kategori()
    {
        return view('pages.gudang.kategori', ['data' => Kategori::paginate(7), 'user' => Auth::guard('karyawan')->user()]);
    }
    public function filter_kategori(Request $req)
    {
        $cari = $req->input('query');
        $filter = Kategori::where('nama', 'like', '%' . $cari . '%')->paginate(6);

        return view('pages.gudang.kategori', ['data' => $filter, 'user' => Auth::guard('karyawan')->user(), 'kategori' => Kategori::all()]);
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

    // barang
    public function show_barang()
    {
        if (Kategori::all()->count() == 0)
            return redirect()->route('kelola.panel.kategori')->with('null', true);
        return view('pages.gudang.barang', ['data' => Barang::paginate(6), 'user' => Auth::guard('karyawan')->user(), 'kategori' => Kategori::all()]);
    }
    public function filter_barang(Request $req)
    {
        $cari = $req->input('query');
        $filter = Barang::where('nama', 'like', '%' . $cari . '%')->orWhereHas('kategori', function ($query) use ($cari) {
            $query->where('nama', 'like', '%' . $cari . '%');
        })->paginate(6);

        return view('pages.gudang.barang', ['data' => $filter, 'user' => Auth::guard('karyawan')->user(), 'kategori' => Kategori::all()]);
    }
    public function add_barang(Request $req)
    {
        $req->validate([
            'nama' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'harga' => 'required|numeric',
            'stok' => 'required|integer',
            'cover' => 'nullable|image|max:12132',
        ]);
        $data = $req->all();
        if ($req->hasFile('cover')) {
            $data['cover'] = $req->file('cover')->store('covers', 'public');
        }

        Barang::create($data);

        return redirect()->route('kelola.panel.barang')->with('add', true);
    }
    public function update_barang(Request $req, $id)
    {
        $user = Barang::findOrFail($id);

        $req->validate([
            'nama' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'harga' => 'required|numeric',
            'stok' => 'required|integer',
            'cover' => 'nullable|image|max:12132',
        ]);

        $data = $req->only(['nama', 'kategori_id', 'harga', 'stok']);

        if ($req->hasFile('cover')) {
            if ($user->cover && Storage::disk('public')->exists($user->cover)) {
                Storage::disk('public')->delete($user->cover);
            }
            $data['cover'] = $req->file('cover')->store('covers', 'public');
            $user->cover = $data['cover'];
        }
        $user->nama = $data['nama'];
        $user->kategori_id = $data['kategori_id'];
        $user->harga = $data['harga'];
        $user->stok = $data['stok'];

        $user->save();

        return redirect()->back()->with('up', true);
    }
    public function delete_barang($id)
    {
        $user = Barang::findOrFail($id);
        $user->delete();

        if ($user->cover && Storage::disk('public')->exists($user->cover)) {
            Storage::disk('public')->delete($user->cover);
        }
        return redirect()->route('kelola.panel.barang')->with('del', true);
    }
    public function bulk_template()
    {
        $kategori = Kategori::pluck('nama')->toArray();

        $spreadsheet = new Spreadsheet();

        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'nama');
        $sheet->setCellValue('B1', 'kategori');
        $sheet->setCellValue('C1', 'harga');
        $sheet->setCellValue('D1', 'stok');

        $this->dropDown($sheet, 'B2:B10000', $kategori);

        $file = new Xlsx($spreadsheet);

        $path = storage_path('app/public/template-' . time() . '.xlsx');

        $file->save($path);

        return response()->download($path)->deleteFileAfterSend(true);
    }
    private function dropDown(Worksheet $sheet, $range, $opt)
    {
        // $drop = $sheet->getCell('B2:B10000')->getDataValidation();

        $drop = new DataValidation();

        $drop->setType(DataValidation::TYPE_LIST);
        $drop->setErrorStyle(DataValidation::STYLE_STOP);
        $drop->setAllowBlank(false);
        $drop->setShowDropDown(true);
        $drop->setFormula1('"' . implode(',', $opt) . '"');

        [$startCell, $endCell] = explode(':', $range);
        [$startCol, $startRow] = sscanf($startCell, '%[A-Z]%d');
        [$endCol, $endRow] = sscanf($endCell, '%[A-Z]%d');

        for ($row = $startRow; $row <= $endRow; $row++) {
            $cell = $startCol . $row;
            $sheet->getCell($cell)->setDataValidation(clone $drop);
        }
    }
    public function import_data(Request $req)
    {
        $req->validate(['excel' => 'required|mimes:xlsx,xls,csv|max:12082']);

        $file = $req->file('excel');
        $spread = IOFactory::load($file);
        $sheet = $spread->getActiveSheet();
        $data = [];
        foreach ($sheet->getRowIterator() as $rowIndex => $row) {
            if ($rowIndex == 1)
                continue;
            $nama = $sheet->getCell('A' . $rowIndex)->getValue();
            $nama_kategori
                = $sheet->getCell('B' . $rowIndex)->getValue();

            $temp = Kategori::where('nama', $nama_kategori)->get();
            if ($temp->count() == 0) {
                echo $nama_kategori;
                Kategori::create(['nama' => $sheet->getCell('B' . $rowIndex)->getValue()]);
            }
            $kategori = Kategori::where('nama', $sheet->getCell('B' . $rowIndex)->getValue())->first();

            $harga = $sheet->getCell('C' . $rowIndex)->getValue();
            $stok = $sheet->getCell('D' . $rowIndex)->getValue();
            $data[] = [
                'harga' => $harga,
                'kategori_id' => $kategori->id,
                'nama' => $nama,
                'stok' => $stok,
            ];
        }
        Barang::insert($data);
        return redirect()->back()->with('bulk', true);
    }
}
