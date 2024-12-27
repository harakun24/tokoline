<x-layout>
    <x-slot:title>Profil</x-slot:title>
    <x-head>
        <x-slot:user>{{ explode(' ', $user->nama)[0] }}</x-slot:user>
    </x-head>
    <x-slot:exclass>h-[100vh] grid grid-rows-[auto_1fr] grid-cols-1</x-slot:exclass>
    <div class="grid place-items-center">
        <div
            class="grid bg-white min-h-[280px] max-w-[85%] min-w-[55%] rounded-lg shadow-lg grid-cols-[repeat(3,auto)] gap-3 items-center content-start p-[20px] relative">

            <h4>username</h4> <span>:</span>
            <h4>{{ $user->username }}</h4>
            <h4>nama</h4> <span>:</span>
            <h4>{{ $user->nama }}</h4>
            <h4>password</h4> <span>:</span>
            <h4 class="text-[#] opacity-50">[secret]</h4>
            <div class="col-span-3 row-span-2 flex justify-around pt-5 gap-2">
                <button
                    class="bg-transparent py-2 px-3 border-2 border-[#970b0b] rounded-[5px] text-red-700 hover:bg-[#970b0b] hover:text-white"><i
                        class="fa fa-heart px-2"></i>
                    favorit</button>
                <a href="{{ route('keranjang.show') }}"
                    class=" py-2 px-3 border-2 border-[#099877] rounded-[5px] hover:bg-[#09daa9] bg-[#099877] text-white"><i
                        class="fa fa-shopping-cart px-2"></i>
                    keranjang</a>
                <a href={{ route('transaksi.show') }}
                    class=" py-2 px-3 border-2 border-[#08a610] rounded-[5px] hover:text-[#08a610] bg-[#08a610] hover:bg-transparent text-white"><i
                        class="fa fa-receipt px-2"></i>
                    transaksi</a>
            </div>
            <div class="col-span-3 flex justify-end">
                <button onclick="openEdit()"
                    class="bg-[#272776] hover:bg-[#292995] text-white py-2 px-4 rounded-[5px]">ubah
                    &nbsp;<i class="fa fa-pencil"></i></button>
            </div>
        </div>
    </div>
</x-layout>

<script>
    @if ($errors->any())
        window.onload = function() {

            Swal.fire({
                icon: 'error',
                title: 'gagal mengubah profil',
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

    function openEdit() {
        Swal.fire({
            showConfirmButton: false,
            html: `
             <form action="{{ route('profil.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-[auto_1fr] gap-3 place-items-center">
                <div class="col-span-2 flex flex-col items-center pb-3 opacity-85"><img
                        src="{{ asset('images/icon-tokoline.png') }}" class="w-[40%]" alt="">
                    <h3 class="font-[600]">Edit Profil</h3>
                </div>
                <div class="col-span-2 mt-[2%]"></div>
                <label for="username">username</label>
                <input type="text" name="uname" required id="username" placeholder="username" value="{{ $user->username }}"
                    class="focus:outline-none rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e]">
                <label for="nama">nama <br> pengguna</label>
                <input type="text" name="fullname" required id="nama" placeholder="nama pengguna" value="{{ $user->nama }}"
                    class="focus:outline-none rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e]">
                <label for="password">password</label>
                <input type="password" name="password" id="password" placeholder="isi jika ingin ubah"
                    class="focus:outline-none rounded-[5px] py-3 px-4 border-2 border-black focus:border-[#1acc3e]">
                <div class="col-span-2 w-[80%] flex flex-col items-stretch mt-[5%]">
                    <button
                        class="rounded-[5px] bg-[#7ac607] hover:bg-[#70dd28] shadow-sm py-4 font-bold text-[#004000]">perbarui</button>

                </div>
            </div>
        </form>
            `
        })
    }
</script>
