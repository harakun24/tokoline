<x-layout>
    <x-slot:title>Profil</x-slot:title>
    <x-head>
        <x-slot:user>{{ explode(' ', $user->nama)[0] }}</x-slot:user>
    </x-head>
    <x-slot:exclass>h-[100vh] grid grid-rows-[auto_1fr] grid-cols-1</x-slot:exclass>
    <div class="grid place-items-center">
        <div
            class="grid bg-white min-h-[280px] max-w-[85%] min-w-[55%] rounded-lg shadow-lg grid-cols-[repeat(3,auto)] gap-3 items-center content-start p-[20px] relative">
            <table class="col-span-3">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border">no. </th>
                        <th class="px-4 py-2 border">bukti</th>
                        <th class="px-4 py-2 border">transaksi</th>
                        <th class="px-4 py-2 border">total</th>
                        <th class="px-4 py-2 border">status</th>
                        <th class="px-4 py-2 border">opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $d)
                        <tr class="border">
                            <td class="border text-center py-1">{{ $loop->index + 1 }}</td>
                            <td class="border text-center py-1">
                                @if ($d->bukti)
                                    <img src="{{ asset('storage/' . $d->bukti) }}" cover="aspect-[1]"
                                        alt="{{ $d->id }}" width="50">
                                @else
                                    <h4 class="text-center">no photo</h4>
                                @endif
                            </td>
                            <td class="border text-center py-1">{{ $d->created_at }}</td>

                            <td class="border px-2 py-1 text-right">Rp
                                {{ number_format(
                                    $d->transaksiDetail->sum(function ($e) {
                                        return $e->jumlah * $e->barang->harga;
                                    }),
                                    0,
                                    ',',
                                    '.',
                                ) }}
                            </td>
                            <td class="border text-center py-1">{{ $d->status }}</td>
                            @if ($d->status != 'dibatalkan' && $d->status != 'selesai')
                                <td>
                                    <div class="p-2 place-items-center flex justify-center gap-2">

                                        <form action="{{ route('transaksi.remove', $d->id) }}" method="POST">
                                            @csrf
                                            <button class="border p-2 px-3 bg-[#f55454] rounded-[5px] text-[#540505]"
                                                type="submit">batalkan <i class="fa fa-minus"></i></button>
                                        </form>
                                        <form action="{{ route('keranjang.add', $d->id) }}" method="POST">
                                            @csrf
                                            <button class="border p-2 px-3 bg-[#29f165] rounded-[5px] text-[#1e4f30]"
                                                type="submit"> unggah bukti pembayaran<i
                                                    class="fa fa-plus"></i></button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr class="border">
                            <td colspan="7" class="py-3 text-center">data kosong</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>
</x-layout>

<script>
    @if ($errors->any())
        window.onload = function() {

            Swal.fire({
                icon: 'error',
                title: 'terjadi kesalahan',
                text: `
                @foreach ($errors->all() as $error)
                {{ $error }}
                @endforeach
                `,
                showCancelButton: false,
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
            })
        }
    @endif
</script>
