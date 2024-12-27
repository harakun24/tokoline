<x-admin>
    <x-slot:page></x-slot:page>
    <div class="col-span-3 grid grid-cols-[auto_auto_auto] gap-2">

        <a href="{{ route('kelola.panel.kategori') }}"
            class="border-2 py-2 px-4 border-[#069806] rounded-[5px] text-[#069806] flex justify-around items-center">
            <span>

                kelola kategori
            </span>
            <i class="fa fa-chart-diagram fa-xl"></i>
        </a>
        <span></span>
        <a href="{{ route('kelola.panel.barang') }}"
            class="border-2 py-2 px-4 border-[#069806] rounded-[5px] text-[#069806] flex justify-around items-center">
            <span>

                kelola barang
            </span>
            <i class="fa fa-box fa-xl"></i>
        </a>
    </div>
</x-admin>
