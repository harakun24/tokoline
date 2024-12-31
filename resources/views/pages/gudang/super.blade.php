<x-layout>
    <x-slot:title>Super Panel</x-slot:title>
    <x-slot:exclass>flex justify-start flex-col items-center items-start h-[100vh] grid-cols-1 gap-4</x-slot:exclass>

    <div class="bg-white min-w-[80%] mt-[20px] rounded-[5px] grid grid-cols-[auto_1fr_auto] p-4 gap-2">
        <button class="py-2 px-4 rounded-[5px] border-2" style="background: #0bea1e;color:#04430f"
            onclick="add_user()">tambah <i class="fa fa-plus"></i></button>
        <h3 class="self-center text-center">Kelola User</h3>

        <form action="{{ route('kelola.logout') }}" method="POST">
            @csrf
            <button class="col-3 py-2 px-4 rounded-[5px] border-2" style="background: #f44c35;color:#630909">keluar <i
                    class="fa fa-power-off"></i></button>
        </form>
        <form action="{{ route('kelola.panel.filter.super') }}" method="GET"
            class="col-span-3 border grid place-items-center py-2 px-2">
            <input type="text" name="query" class="w-[100%]" placeholder="cari" value="{{ $cari }}" />
        </form>
        <table class="col-span-3">
            <thead>
                <tr>
                    <th class="px-4 py-2 border">No. </th>
                    <th class="px-4 py-2 border">username</th>
                    <th class="px-4 py-2 border">nama</th>
                    <th class="px-4 py-2 border">role</th>
                    <th class="px-4 py-2 border">opsi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $d)
                    <tr class="border">
                        <td class="border text-center py-1">{{ $loop->index + 1 }}</td>
                        <td class="border text-center py-1">{{ $d->username }}</td>
                        <td class="border text-center py-1">{{ $d->nama }}</td>
                        <td class="border text-center py-1">
                            {{ $d->role == 1 ? 'Customer Service 1' : '' }}
                            {{ $d->role == 2 ? 'Customer Service 2' : '' }}
                            {{ $d->role == 3 ? 'Admin' : '' }}
                        </td>
                        <td>
                            <div class="p-2 place-items-center flex justify-center gap-2">
                                <button
                                    onclick="edit_user({{ json_encode($d) }},'{{ route('kelola.update.user', $d->id) }}')"
                                    class="border p-2 px-3 bg-[#29f165] rounded-[5px] text-[#1e4f30]">edit <i
                                        class="fa fa-pencil-alt"></i></button>

                                <form action="{{ route('kelola.delete.user', $d->id) }}" method="POST">
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
                        <td colspan="5" class="py-3 text-center">data kosong</td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>
    {{ $data->links() }}
</x-layout>

<script>
    function add_user() {
        Swal.fire({
            showConfirmButton: false,
            html: `
               <form action="{{ route('kelola.add.user') }}" method="POST">
            @csrf
            <div class="grid grid-cols-[auto_1fr] gap-3 place-items-center">
                <div class="col-span-2 flex flex-col items-center pb-3 opacity-85"><img
                        src="{{ asset('images/icon-tokoline.png') }}" class="w-[40%]" alt="">
                    <h3 class="font-[600]">tambah admin</h3>
                </div>
                <div class="col-span-2 mt-[2%]"></div>
                <label for="username">username</label>
                <input type="text" name="uname" required id="username" placeholder="username"
                    class="focus:outline-none rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e]">
                <label for="nama">nama <br> pengguna</label>
                <input type="text" name="fullname" required id="nama" placeholder="nama pengguna"
                    class="focus:outline-none rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e]">
                <label for="password">password</label>
                <input type="password" name="password" required id="password" placeholder="password"
                    class="focus:outline-none rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e]">
                <label for="level">level</label>
                <select name="role" required id="level" placeholder="password"
                    class="focus:outline-none bg-white rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e]">
                    <option value="1">customer service 1</option>
                    <option value="2">customer service 2</option>
                    <option value="3">admin</option>
                    </select>
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
                title: 'gagal mendaftar',
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
                text: "silahkan masuk dengan akun yang telah anda buat.",
                showCancelButton: false,
                showConfirmButton: false,
                timer: 1800,
                timerProgressBar: true,
            })
        @elseif (session('del'))
            Swal.fire({
                icon: 'success',
                title: 'berhasil menghapus',
                text: "user berhasil dihapus",
                showCancelButton: false,
                showConfirmButton: false,
                timer: 1800,
                timerProgressBar: true,
            })
        @elseif (session('up'))
            Swal.fire({
                icon: 'success',
                title: 'berhasil mengubah',
                text: "user berhasil diubah",
                showCancelButton: false,
                showConfirmButton: false,
                timer: 1800,
                timerProgressBar: true,
            })
        @endif
    }

    function edit_user(data, dest) {
        Swal.fire({
            showConfirmButton: false,
            html: `
               <form action="${dest}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-[auto_1fr] gap-3 place-items-center">
                <div class="col-span-2 flex flex-col items-center pb-3 opacity-85"><img
                        src="{{ asset('images/icon-tokoline.png') }}" class="w-[40%]" alt="">
                    <h3 class="font-[600]">ubah admin</h3>
                </div>
                <div class="col-span-2 mt-[2%]"></div>
                <label for="username">username</label>
                <input type="text" name="uname" required id="username" placeholder="username" value="${data.username}"
                    class="focus:outline-none rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e]">
                <label for="nama">nama <br> pengguna</label>
                <input type="text" name="fullname" required id="nama" placeholder="nama pengguna" value="${data.nama}"
                    class="focus:outline-none rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e]">
                <label for="password">password</label>
                <input type="password" name="password" id="password" placeholder="isi jika ingin ubah"
                    class="focus:outline-none rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e]">
                <label for="level">level</label>
                <select name="role" required id="level" placeholder="password"
                    class="focus:outline-none bg-white rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e]">
                    <option value="1" ${data.role==1?"selected":""}>customer service 1</option>
                    <option value="2"  ${data.role==2?"selected":""}>customer service 2</option>
                    <option value="3"  ${data.role==3?"selected":""}>admin</option>
                    </select>
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
