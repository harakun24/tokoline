<x-layout>
    <x-slot:title>Profil</x-slot:title>
    <x-head>
        <x-slot:user>{{ explode(' ', $user->nama)[0] }}</x-slot:user>
    </x-head>
    <x-slot:exclass>h-[100vh] grid grid-rows-[auto_1fr] grid-cols-1</x-slot:exclass>
    <div class="grid place-items-center">
        <div
            class="grid  max-w-[85%] overflow-x-scroll bg-white min-h-[280px] min-w-[55%] rounded-lg shadow-lg grid-cols-[repeat(3,auto)] gap-3 items-center content-start p-[20px] relative">
            <table class="col-span-3">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border">no. </th>
                        <th class="px-4 py-2 border">bukti</th>
                        <th class="px-4 py-2 border">tangga checkout</th>
                        <th class="px-4 py-2 border">jatuh tempo</th>
                        <th class="px-4 py-2 border">total</th>
                        <th class="px-4 py-2 border">status</th>
                        <th class="px-4 py-2 border">detail</th>
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
                                    <span class="text-center p-1">belum unggah</span>
                                @endif
                            </td>
                            <td class="border text-center py-1 px-2">{{ $d->created_at }}</td>
                            <td class="border text-center py-1 px-2">{{ $d->created_at->addDay() }}</td>
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
                            <td class="border text-center py-1 px-2">{{ $d->status }}</td>
                            <td class="border p-2">
                                <div class="w-[100%] grid place-items-center">
                                    <button class="bg-[#1db3b3] p-2 px-3 rounded-[4px]"
                                        onclick='show_detail({!! json_encode($d->transaksiDetail) !!})'>lihat <i
                                            class="fa fa-eye"></i></button>
                                </div>
                            </td>
                            @if ($d->status == 'menunggu')
                                <td class="border">
                                    <div class="p-2 place-items-center flex justify-center gap-2">

                                        <form action="{{ route('transaksi.remove', $d->id) }}" method="POST">
                                            @csrf
                                            <button class="border p-2 px-3 bg-[#f55454] rounded-[5px] text-[#540505]"
                                                type="submit">batalkan</button>
                                        </form>
                                        @if (!$d->bukti)
                                            <button class="border p-2 px-3 bg-[#29f165] rounded-[5px] text-[#1e4f30]"
                                                onclick="unggah('{{ route('transaksi.upload', $d->id) }}')"
                                                type="submit">pembayaran</button>
                                        @endif
                                    </div>
                                </td>
                            @elseif($d->status == 'dikirim')
                                <td class="border">
                                    <div class="p-2 place-items-center flex justify-center gap-2">

                                        <form action="{{ route('kelola.panel.sampai', $d->id) }}" method="POST">
                                            @csrf
                                            <button class="border p-2 px-3 bg-[#54f584] rounded-[5px] text-[#055423]"
                                                onclick="confirm('yakin barang sudah diterima?')" type="submit">
                                                sampai</button>
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
            {{ $data->links() }}
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
        @elseif (session('unggah'))
            Swal.fire({
                icon: 'success',
                title: 'berhasil unggah',
                text: "berhasil upload bukti bayar",
                showCancelButton: false,
                showConfirmButton: false,
                timer: 1800,
                timerProgressBar: true,
            })
        @elseif (session('sampai'))
            Swal.fire({
                icon: 'success',
                title: 'transaksi selesai',
                text: "barang sampai ke tujuan",
                showCancelButton: false,
                showConfirmButton: false,
                timer: 1800,
                timerProgressBar: true,
            })
        }
    @endif
    function show_detail(data) {
        console.log(data)
        let arr = '';
        data.forEach((item) => {
            arr += `
          <tr>
            <td class="p-2 border">${item.barang.nama}</td>
            <td class="p-2 border">
                <div class="flex justify-center">
                    <img src="/storage/${ item.barang.cover }" cover="aspect-[1]"
                    alt="${item.barang.id }" width="50">
                    </div>
                                        </td>
            <td class="p-2 border">Rp ${item.barang.harga*1}</td>
            <td class="p-2 border">${item.jumlah}</td>
            <td class="p-2 border">Rp ${item.jumlah*item.barang.harga}</td>
            </tr>
          `;
        });
        Swal.fire({
            html: `
            <table class="w-[100%]">
                <thead>
                    <tr>
                        <th class="p-2 border">barang</th>
                        <th class="p-2 border">cover</th>
                        <th class="p-2 border">harga</th>
                        <th class="p-2 border">jumlah</th>
                        <th class="p-2 border">total</th>
                    </tr>
                </thead>
                <tbody>
                    ${arr}
                    </tbody>
            </table>
            `,
            confirmButtonText: 'tutup'
        })
    }

    function unggah(id) {
        Swal.fire({
            html: `
            <form action="${id}" enctype="multipart/form-data" method="POST">
                @csrf
                    <label>unggah bukti</label>
                    <input required class="p-4" name="bukti" type="file"/>
                     <button
                        class="rounded-[5px] bg-[#7ac607] hover:bg-[#70dd28] shadow-sm py-2 px-4 font-bold text-[#004000]">unggah</button>
                </form>
            `,
            showConfirmButton: false
        })
    }

    window.onload = function() {
        listen(transaksi, '{{ route('transaksi.get', $user->id) }}', function(data) {
            fetch(window.location).then(e => e.text()).then(e => {
                e = e.split('<tbody>')[1].split('</tbody>')[0];
                document.querySelector('tbody').innerHTML = e;
            })
        });
    }

    let transaksi = {!! json_encode($transaksi) !!};
</script>
