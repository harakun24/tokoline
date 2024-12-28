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
                        <th class="px-4 py-2 border">gambar</th>
                        <th class="px-4 py-2 border">barang</th>
                        <th class="px-4 py-2 border">harga</th>
                        <th class="px-4 py-2 border">jumlah</th>
                        <th class="px-4 py-2 border">total</th>
                        <th class="px-4 py-2 border">opsi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $d)
                        <tr class="border">
                            <td class="border text-center py-1">{{ $loop->index + 1 }}</td>
                            <td class="border text-center py-1">
                                @if ($d->barang->cover)
                                    <img src="{{ asset('storage/' . $d->barang->cover) }}" cover="aspect-[1]"
                                        alt="{{ $d->barang->nama }}" width="50">
                                @else
                                    <h4 class="text-center">no photo</h4>
                                @endif
                            </td>
                            <td class="border text-center py-1">{{ $d->barang->nama }}</td>

                            <td class="border px-2 py-1 text-right">Rp
                                {{ number_format($d->barang->harga, 0, ',', '.') }}</td>
                            <td class="border text-center py-1">{{ $d->jumlah }}</td>
                            <td class="border px-2 py-1 text-right">Rp
                                {{ number_format($d->barang->harga * $d->jumlah, 0, ',', '.') }}</td>
                            <td>
                                <div class="p-2 place-items-center flex justify-center gap-2">

                                    <form action="{{ route('keranjang.dec', $d->barang->id) }}" method="POST">
                                        @csrf
                                        <button class="border p-2 px-3 bg-[#f55454] rounded-[5px] text-[#540505]"
                                            type="submit"><i class="fa fa-minus"></i></button>
                                    </form>
                                    <form action="{{ route('keranjang.add', $d->barang->id) }}" method="POST">
                                        @csrf
                                        <button class="border p-2 px-3 bg-[#29f165] rounded-[5px] text-[#1e4f30]"
                                            type="submit"><i class="fa fa-plus"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="border">
                            <td colspan="7" class="py-3 text-center">data kosong</td>
                        </tr>
                    @endforelse
                    @if ($data->count() !== 0)
                        <tr>
                            <td colspan="2" class="text-bold border p-3 text-center">total</td>
                            <td colspan="4" class="border p-3 text-right">Rp
                                {{ number_format($total, 0, ',', '.') }}
                            </td>
                            <td class="p-2 border " colspan="7">
                                <div class="flex justify-end">


                                    <form action="{{ route('transaksi.check', $d->barang->id) }}" method="POST">
                                        @csrf
                                        <button
                                            class="border p-2 px-3 bg-[#29f165] rounded-[5px] text-[#1e4f30] font-bold"
                                            type="submit">checkout</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr>




                        </tr>
                    @endif
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
