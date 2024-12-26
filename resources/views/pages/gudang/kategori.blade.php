<x-admin>
    <x-slot:page> {{ $data->links() }}</x-slot:page>
    <div class="col-span-2 flex gap-2">
        <a href="{{ route('kelola.panel.admin') }}" class="border p-2 px-4 rounded-[5px] bg-[#9bf498] text-[#063e22]"> <i
                class="fa fa-arrow-left"></i> kembali</a>
        <button class="border p-2 px-4 rounded-[5px] bg-[#ff1535] text-[#3e0606]" onclick="add_item()">tambah <i
                class="fa fa-plus"></i></button>
    </div>
    <h3>kategori</h3>
    <table class="col-span-3">
        <thead>
            <tr>
                <th class="px-4 py-2 border">no. </th>
                <th class="px-4 py-2 border">nama</th>
                <th class="px-4 py-2 border">opsi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $d)
                <tr class="border">
                    <td class="border text-center py-1">{{ $loop->index + 1 }}</td>
                    <td class="border text-center py-1">{{ $d->nama }}</td>
                    <td>
                        <div class="p-2 place-items-center flex justify-center gap-2">
                            <button
                                onclick="edit_item({{ json_encode($d) }},'{{ route('kelola.update.kategori', $d->id) }}')"
                                class="border p-2 px-3 bg-[#29f165] rounded-[5px] text-[#1e4f30]">edit <i
                                    class="fa fa-pencil-alt"></i></button>

                            <form action="{{ route('kelola.delete.kategori', $d->id) }}" method="POST">
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
               <form action="{{ route('kelola.add.kategori') }}" method="POST">
            @csrf
            <div class="grid grid-cols-[auto_1fr] gap-3 place-items-center">

                <div class="col-span-2 mt-[2%]"></div>
                <label for="nama">kategori</label>
                <input type="text" name="nama" required id="username" placeholder="Contoh: makanan"
                    class="focus:outline-none rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e]">

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
                text: "kategori tersimpan",
                showCancelButton: false,
                showConfirmButton: false,
                timer: 1800,
                timerProgressBar: true,
            })
        @elseif (session('del'))
            Swal.fire({
                icon: 'success',
                title: 'berhasil menghapus',
                text: "kategori berhasil dihapus",
                showCancelButton: false,
                showConfirmButton: false,
                timer: 1800,
                timerProgressBar: true,
            })
        @elseif (session('up'))
            Swal.fire({
                icon: 'success',
                title: 'berhasil mengubah',
                text: "kategori berhasil diubah",
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
                <label for="nama">kategori</label>
                <input type="text" name="nama" required value="${data.nama}" id="username" placeholder="Contoh: makanan"
                    class="focus:outline-none rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e]">

                    <div class="col-span-2 w-[80%] flex flex-col items-stretch mt-[5%]">
                    <button
                        class="rounded-[5px] bg-[#7ac607] hover:bg-[#70dd28] shadow-sm py-4 font-bold text-[#004000]">ubah</button>

                </div>
            </div>
        </form>
            `
        });
    }
</script>
