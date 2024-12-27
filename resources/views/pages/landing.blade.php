<x-layout>
    <x-slot:title>Toko Online Sederhana</x-slot:title>

    <div class="grid grid-cols-1 grid-rows-[auto_auto]">
        <x-head>
            @if (isset($user))
                <x-slot:user>{{ explode(' ', $user->nama)[0] }}</x-slot:user>
                <x-slot:uname>{{ $user->username }}</x-slot:uname>
            @endif
        </x-head>
        <div class=" w-[100%] grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-6 grid-rows-3 p-2">
            @forelse($barang as $b)
                <div class="bg-white rounded-[5px] shadow-md p-3 flex flex-col items-stretch">
                    @if ($b->cover)
                        <img src="{{ asset('storage/' . $b->cover) }}" class="aspect-[1] shadow-lg"
                            alt="{{ $b->nama }}">
                    @else
                        <img src="{{ asset('images/icon-tokoline.png') }}" class="aspect-[1] w-[100%] shadow-lg"
                            alt="{{ $b->nama }}" width="50">
                    @endif
                    <div class="flex justify-between items-center">
                        <h4>{{ $b->nama }}</h4>
                        <span class="opacity-[70%]">{{ $b->kategori->nama }}</span>
                    </div>
                    <div class="flex justify-end gap-2">
                        @if (isset($user))
                            <form action="{{ route('keranjang.add', $b->id) }}" method="POST">
                                @csrf

                                <button class="p-2 text-[#5d9f06]"><i class="fa fa-shopping-cart"></i></button>
                            </form>
                        @else
                            <a href="{{ route('login.page') }}" class="p-2 text-[#5d9f06]"><i
                                    class="fa fa-shopping-cart"></i></a>

                            <a href="{{ route('login.page') }}" class="p-2 text-[#d41b24]"><i
                                    class="fa fa-heart"></i></a>
                        @endif
                        <a href="#" class="p-2 text-[#d41b24]"><i class="fa fa-heart"></i></a>
                    </div>
                </div>
            @empty
                <h4>Belum ada barang yang dijual.</h4>
            @endforelse
            <div class="col-2 sm:col-span-3 md:col-span-6 mt-2">
                {{ $barang->links() }}
            </div>
        </div>
    </div>
</x-layout>

<script>
    window.onload = function() {
        @if (session('req_ok'))
            Swal.fire({
                icon: 'success',
                title: 'Selamat datang {{ $user->nama }}',
                text: "Berhasil masuk",
                showCancelButton: false,
                showConfirmButton: false,
                timer: 2200,
                timerProgressBar: true,
            })
        @endif
        @if (session('add-cart'))
            Swal.fire({
                icon: 'success',
                text: 'ditambahkan ke keranjang',
                showCancelButton: false,
                showConfirmButton: false,
                timer: 1000,
                timerProgressBar: true,
            })
        @endif
    }
</script>
