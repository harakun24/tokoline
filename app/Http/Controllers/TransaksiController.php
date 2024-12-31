<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Keranjang;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    //
    function transaksi_batal($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        if ($transaksi->status == 'menunggu')
            $transaksi->status = 'dibatalkan';

        $transaksi->save();

        return redirect()->route('kelola.panel.cs1')->with('batal', true);
    }
    function get_transaksi_all()
    {
        return json_encode(Transaksi::with('pembeli')->with('transaksiDetail.barang')->where('status', 'menunggu')->get());
    }
    function get_proses()
    {
        return json_encode(Transaksi::with('pembeli')->with('transaksiDetail.barang')->where('status', 'diproses')->orWhere('status', 'dikemas')->orWhere('status', 'dikirim')->orWhere('status', 'sampai')->orderBy('updated_at', 'desc')->get());
    }
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
            ->orderBy('updated_at', 'desc')->paginate(10);

        return view('pages.transaksi', ['data' => $transaksi, 'user' => Auth::guard('pembeli')->user(), 'transaksi' => Transaksi::with('transaksiDetail.barang')->where('pembeli_id', Auth::guard('pembeli')->user()->id)->orderByRaw("
        CASE
            WHEN status = 'menunggu' THEN 1
            WHEN status = 'pengemasan' THEN 2
            WHEN status = 'pengiriman' THEN 3
            WHEN status = 'selesai' THEN 4
            WHEN status = 'dibatalkan' THEN 5
            ELSE 6
        END
        ")
            ->orderBy('updated_at', 'desc')->get()]);
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
            // broadcast(new autoRefresh(Transaksi::where('pembeli_id', $transaksi->pembeli_id)))->toOthers();
            DB::commit();
            // AutoRefresh::dispatch(Transaksi::where('pembeli_id', $transaksi->pembeli_id)->get());
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
    function get_transaksi(Request $req, $id)
    {
        return json_encode(Transaksi::with('transaksiDetail.barang')->where('pembeli_id', $id)->orderByRaw("
        CASE
            WHEN status = 'menunggu' THEN 1
            WHEN status = 'pengemasan' THEN 2
            WHEN status = 'pengiriman' THEN 3
            WHEN status = 'selesai' THEN 4
            WHEN status = 'dibatalkan' THEN 5
            ELSE 6
        END
        ")
            ->orderBy('updated_at', 'desc')->get());
    }
    function confirm_transaksi(Request $req, $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        foreach ($transaksi->transaksiDetail as $detail) {
            $barang = Barang::findOrFail($detail->barang_id);
            $barang->stok = $barang->stok - $detail->jumlah;
        }
        $transaksi->status = 'diproses';
        $transaksi->save();
        return redirect()->route('kelola.panel.cs1')->with('proses', true);
    }
}
