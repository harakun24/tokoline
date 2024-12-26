<x-layout>
    <x-slot:title>Admin Panel</x-slot:title>
    <x-slot:exclass>flex justify-start flex-col items-center items-start h-[100vh] grid-cols-1 gap-4</x-slot:exclass>

    <div class="bg-white min-w-[80%] mt-[20px] rounded-[5px] grid grid-cols-[auto_1fr_auto] p-4 gap-2">
        <h3 class="self-center text-center">Panel Admin</h3>
        <span></span>
        <form action="{{ route('kelola.logout') }}" method="POST">
            @csrf
            <button class="col-3 py-2 px-4 rounded-[5px] border-2" style="background: #f44c35;color:#630909">keluar <i
                    class="fa fa-power-off"></i></button>
        </form>
        {{ $slot }}
    </div>
    {{ $page }}
</x-layout>
