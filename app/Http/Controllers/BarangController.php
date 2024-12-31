<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BarangController extends Controller
{
    //
    public function show_barang()
    {
        return redirect()->route('kelola.panel.barang');
    }
    public function filter_barang(Request $req)
    {
        if (Kategori::all()->count() == 0)
            return redirect()->route('kelola.panel.kategori')->with('null', true);


        $cari = $req->input('query') ?? '';
        $filter = Barang::where('nama', 'like', '%' . $cari . '%')->orWhereHas('kategori', function ($query) use ($cari) {
            $query->where('nama', 'like', '%' . $cari . '%');
        })->paginate(6);

        return view('pages.gudang.barang', ['data' => $filter, 'user' => Auth::guard('karyawan')->user(), 'kategori' => Kategori::all(), 'cari' => $cari]);
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
            // $nama_kategori
            //     = $sheet->getCell('B' . $rowIndex)->getValue();

            // $temp = Kategori::where('nama', $nama_kategori)->get();
            // if ($temp->count() == 0) {
            //     Kategori::create(['nama' => $nama_kategori]);
            // }
            // $kategori = Kategori::where('nama', $sheet->getCell('B' . $rowIndex)->getValue())->first();
            $nama_kategori = $sheet->getCell('B' . $rowIndex)->getValue();
            $kategori = Kategori::firstOrCreate(['nama' => $nama_kategori]);

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
    function kemas_barang(Request $req, $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->status = 'dikemas';
        $transaksi->save();
        return redirect()->route('kelola.panel.cs2')->with('kemas', true);
    }
    function kirim_barang(Request $req, $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->status = 'dikirim';
        $transaksi->save();
        return redirect()->route('kelola.panel.cs2')->with('kemas', true);
    }
    function sampai_barang(Request $req, $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->status = 'sampai';
        $transaksi->save();
        return redirect()->route('transaksi.show')->with('sampai', true);
    }
}
