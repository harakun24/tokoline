<x-admin>
    <x-slot:page> {{ $data->links() }}</x-slot:page>
    <div class="col-span-2 flex gap-2">
        <a href="{{ route('kelola.panel.admin') }}" class="border p-2 px-4 rounded-[5px] bg-[#9bf498] text-[#063e22]"> <i
                class="fa fa-arrow-left"></i> kembali</a>
        <button class="border p-2 px-4 rounded-[5px] bg-[#ff1535] text-[#3e0606]" onclick="add_item()">tambah <i
                class="fa fa-plus"></i></button>
        <button class="border p-2 px-4 rounded-[5px] bg-[#e87f15] text-[#3e0606]" onclick="add_item_bulk()">bulk <i
                class="fa fa-plus"></i></button>
        <form action="{{ route('kelola.panel.filter.barang') }}" method="GET"
            class="border grid place-items-center px-2">
            <input type="text" name="query" placeholder="cari" />
        </form>
    </div>
    <h3>barang</h3>
    <table class="col-span-3">
        <thead>
            <tr>
                <th class="px-4 py-2 border">no. </th>
                <th class="px-4 py-2 border">cover</th>
                <th class="px-4 py-2 border">nama</th>
                <th class="px-4 py-2 border">kategori</th>
                <th class="px-4 py-2 border">harga</th>
                <th class="px-4 py-2 border">stok</th>
                <th class="px-4 py-2 border">opsi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $d)
                <tr class="border">
                    <td class="border text-center py-1">{{ $loop->index + 1 }}</td>
                    <td class="border text-center py-1">
                        @if ($d->cover)
                            <img src="{{ asset('storage/' . $d->cover) }}" cover="aspect-[1]" alt="{{ $d->nama }}"
                                width="50">
                        @else
                            <h4 class="text-center">no photo</h4>
                        @endif
                    </td>
                    <td class="border text-center py-1">{{ $d->nama }}</td>
                    <td class="border text-center py-1">{{ $d->kategori->nama }}</td>
                    <td class="border px-2 py-1 text-right">Rp {{ number_format($d->harga, 0, ',', '.') }}</td>
                    <td class="border text-center py-1">{{ $d->stok }}</td>
                    <td>
                        <div class="p-2 place-items-center flex justify-center gap-2">
                            <button
                                onclick="edit_item({{ json_encode($d) }},'{{ route('kelola.update.barang', $d->id) }}')"
                                class="border p-2 px-3 bg-[#29f165] rounded-[5px] text-[#1e4f30]">edit <i
                                    class="fa fa-pencil-alt"></i></button>

                            <form action="{{ route('kelola.delete.barang', $d->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('anda yakin ingin menghapus?')"
                                    class="border p-2 px-3 bg-[#f55454] rounded-[5px] text-[#540505]"
                                    type="submit">hapus <i class="fa fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr class="border">
                    <td colspan="7" class="py-3 text-center">data kosong</td>
                </tr>
            @endforelse
        </tbody>

    </table>
</x-admin>

<script>
    function add_item() {
        Swal.fire({
            showConfirmButton: false,
            html: `
               <form action="{{ route('kelola.add.barang') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-[auto_1fr] gap-3 place-items-center">

                <div class="col-span-2 mt-[2%]"></div>
                <label for="nama">barang</label>
                <input type="text" name="nama" required id="username" placeholder="Contoh: kaos XL"
                    class="focus:outline-none rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e]">

                     <label for="kategori_id">kategori</label>
        <select id="kategori_id" name="kategori_id" required class="focus:outline-none rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e] w-[65%] bg-white">
            @foreach ($kategori as $kat)
            <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
            @endforeach
        </select>

         <label for="harga">harga</label>
                <input type="number" name="harga" required id="harga" placeholder="Contoh: 10000"
                    class="focus:outline-none rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e]">
         <label for="stok">stok</label>
                <input type="number" name="stok" required id="harga" placeholder="Contoh: 7"
                    class="focus:outline-none rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e]">
         <label for="cover">gambar</label>
                <input type="file" name="cover" id="cover" placeholder="Boleh dikosongkan"
                    class="focus:outline-none rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e] w-[65%]">


                    <div class="col-span-2 w-[80%] flex flex-col items-stretch mt-[5%]">
                    <button
                        class="rounded-[5px] bg-[#7ac607] hover:bg-[#70dd28] shadow-sm py-4 font-bold text-[#004000]">tambah</button>

                </div>
            </div>
        </form>
            `
        });
    }

    window.onload = function() {
        @if ($errors->any())

            Swal.fire({
                icon: 'error',
                title: 'operasi gagal',
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
        @elseif (session('add'))
            Swal.fire({
                icon: 'success',
                title: 'berhasil menambah',
                text: "barang tersimpan",
                showCancelButton: false,
                showConfirmButton: false,
                timer: 1800,
                timerProgressBar: true,
            })
        @elseif (session('del'))
            Swal.fire({
                icon: 'success',
                title: 'berhasil menghapus',
                text: "barang berhasil dihapus",
                showCancelButton: false,
                showConfirmButton: false,
                timer: 1800,
                timerProgressBar: true,
            })
        @elseif (session('up'))
            Swal.fire({
                icon: 'success',
                title: 'berhasil mengubah',
                text: "barang berhasil diubah",
                showCancelButton: false,
                showConfirmButton: false,
                timer: 1800,
                timerProgressBar: true,
            })
        @elseif (session('bulk'))
            Swal.fire({
                icon: 'success',
                title: 'berhasil menambah data',
                text: "data ditambah secara batch",
                showCancelButton: false,
                showConfirmButton: false,
                timer: 1800,
                timerProgressBar: true,
            })
        @endif
    }

    function edit_item(data, dest) {
        Swal.fire({
            showConfirmButton: false,
            html: `
               <form action="${dest}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-[auto_1fr] gap-3 place-items-center">

                <div class="col-span-2 mt-[2%]"></div>
                <label for="nama">barang</label>
                <input type="text" name="nama" required value="${data.nama}" id="username" placeholder="Contoh: kaos XL"
                    class="focus:outline-none rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e]">

                     <label for="kategori_id">kategori</label>
        <select id="kategori_id" name="kategori_id" required class="focus:outline-none rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e] w-[65%] bg-white">
            @foreach ($kategori as $kat)
            <option value="{{ $kat->id }}" ${({{ $kat->id }}==data.kategori_id)?'selected':''}>{{ $kat->nama }}</option>
            @endforeach
        </select>

         <label for="harga">harga</label>
                <input type="number" name="harga" required  value="${data.harga}" id="harga" placeholder="Contoh: 10000"
                    class="focus:outline-none rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e]">
         <label for="stok">stok</label>
                <input type="number" name="stok" required  value="${data.stok}" id="harga" placeholder="Contoh: 7"
                    class="focus:outline-none rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e]">
         <label for="cover">gambar</label>
                <input type="file" name="cover" id="cover" placeholder="Boleh dikosongkan"
                    class="focus:outline-none rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e] w-[65%]">


                    <div class="col-span-2 w-[80%] flex flex-col items-stretch mt-[5%]">
                    <button
                        class="rounded-[5px] bg-[#7ac607] hover:bg-[#70dd28] shadow-sm py-4 font-bold text-[#004000]">ubah</button>

                </div>
            </div>
        </form>
            `
        });
    }

    function add_item_bulk() {
        Swal.fire({
            showConfirmButton: false,
            html: `
               <form action="{{ route('kelola.add.bulk') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-[auto_1fr] gap-3 place-items-center">
                <a href="{{ route('kelola.panel.template') }}" target='_blank' class="py-2 px-3 m-2 rounded-[5px] border-2 col-span-2">download template</a>
         <label for="cover">Excel file</label>
                <input type="file" name="excel" id="cover"
                    class="focus:outline-none rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e] w-[65%]">


                    <div class="col-span-2 w-[80%] flex flex-col items-stretch mt-[5%]">
                    <button
                        class="rounded-[5px] bg-[#7ac607] hover:bg-[#70dd28] shadow-sm py-4 font-bold text-[#004000]">tambah</button>

                </div>
            </div>
        </form>
            `
        });
    }
</script>
