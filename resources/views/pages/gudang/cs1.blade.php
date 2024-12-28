<x-layout>
    <x-slot:title>Super Panel</x-slot:title>
    <x-slot:exclass>flex justify-start flex-col items-center items-start h-[100vh] grid-cols-1 gap-4</x-slot:exclass>

    <div class="bg-white min-w-[80%] mt-[20px] rounded-[5px] grid grid-cols-[auto_1fr_auto] p-4 gap-2 overflow-x-scroll">

        <span>Customer Service 1 <br>{{ $user->nama }}</span>
        <span></span>
        <form action="{{ route('kelola.logout') }}" method="POST">
            @csrf
            <button class="col-3 py-2 px-4 rounded-[5px] border-2" style="background: #f44c35;color:#630909">keluar <i
                    class="fa fa-power-off"></i></button>
        </form>
        <table class="col-span-3">
            <thead>
                <tr>
                    <th class="px-4 py-2 border">no. </th>
                    <th class="px-4 py-2 border">pembeli</th>
                    <th class="px-4 py-2 border">detail</th>
                    <th class="px-4 py-2 border">total</th>
                    <th class="px-4 py-2 border">tanggal checkout</th>
                    <th class="px-4 py-2 border">jatuh tempo</th>
                    <th class="px-4 py-2 border">bukti bayar</th>
                    <th class="px-4 py-2 border">opsi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $d)
                    <tr class="border">
                        <td class="p-2 text-center">{{ $loop->index + 1 }}</td>
                        <td class="p-2 text-center border">{{ $d->pembeli->nama }}</td>
                        <td class="p-2 text-center border"><button
                                class="bg-[#3d94f0] p-1 whitespace-nowrap px-2 rounded-[5px]"
                                onclick='show_detail({!! json_encode($d->transaksiDetail) !!})'>lihat</button></td>
                        <td class="p-2 text-center border">
                            Rp
                            {{ number_format(
                                $d->transaksiDetail->sum(function ($e) {
                                    return $e->jumlah * $e->barang->harga;
                                }),
                                0,
                                ',',
                                '.',
                            ) }}
                        </td>
                        <td class="p-2 text-center border">
                            {{ $d->created_at }}
                        </td>
                        <td class="p-2 text-center border">
                            {{ $d->created_at->addDay() }}
                        </td>
                        <td class="p-2 text-center border">
                            @if ($d->bukti)
                                <button class="bg-[#3d94f0] p-1 whitespace-nowrap px-2 rounded-[5px]"
                                    onclick="show_bukti('{{ asset('storage/' . $d->bukti) }}')">lihat</button>
                            @else
                                <span class="text-center">belum upload</span>
                            @endif
                        </td>
                        <td>
                            <div class="p-2 place-items-center flex justify-center gap-2">
                                <form action="{{ route('kelola.panel.cancel', $d->id) }}" method="POST">
                                    @csrf
                                    <button onclick="return confirm('anda yakin ingin membatalkan?')"
                                        class="border p-2 px-3 bg-[#f55454] rounded-[5px] text-[#540505]"
                                        type="submit">batalkan</button>
                                </form>
                                @if ($d->bukti)
                                    <form action="{{ route('kelola.delete.user', $d->id) }}" method="POST">
                                        @csrf
                                        <button onclick="return confirm('anda yakin ingin konfirmasi transaksi?')"
                                            class="border p-2 px-3 bg-[#29f165] rounded-[5px] text-[#1e4f30]">konfirmasi</button>
                                    </form>
                                @else
                                    {{--  --}}
                                @endif

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
        @elseif (session('batal'))
            Swal.fire({
                icon: 'success',
                title: 'transaksi dibatalkan',
                text: "transaksi dibatalkan",
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

    function show_bukti(id) {
        Swal.fire({
            html: `
             <img src="${id}" class="h-[50vh] w-auto"
                                    alt="kesalahan" width="50">
            `
        })
    }

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
</script>
