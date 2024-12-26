<x-layout>
    <x-slot:title>Buat Akun</x-slot:title>
    <x-slot:exclass>grid place-items-center h-[100vh]</x-slot:exclass>

    <div
        class="bg-white h-70% sm:h-[80%] w-[85%] sm:w-auto sm:aspect-[10/14] rounded-[8px] shadow-lg p-4 overflow-hidden">
        {{-- <h1>hello</h1> --}}
        <form action="{{ route('sign.submit') }}" method="POST">
            @csrf
            <div class="grid grid-cols-[auto_1fr] gap-3 place-items-center">
                <div class="col-span-2 flex flex-col items-center pb-3 opacity-85"><img
                        src="{{ asset('images/icon-tokoline.png') }}" class="w-[40%]" alt="">
                    <h3 class="font-[600]">TokoLine</h3>
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
                <div class="col-span-2 w-[80%] flex flex-col items-stretch mt-[5%]">
                    <button
                        class="rounded-[5px] bg-[#7ac607] hover:bg-[#70dd28] shadow-sm py-4 font-bold text-[#004000]">daftar</button>
                    <a class="text-center mt-2 text-[#5215e2]" href="{{ route('login.page') }}">Sudah punya akun? Masuk
                        di sini</a>

                </div>
            </div>
        </form>
    </div>
</x-layout>



<script>
    window.onload = function() {
        @if (session('error'))

            Swal.fire({
                icon: 'error',
                title: 'gagal mendaftar',
                text: "{{ session('error') }}",
                showCancelButton: false,
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
            })
        @endif

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
        @endif
    }
</script>
