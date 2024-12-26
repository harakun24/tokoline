<x-admin>
    <x-slot:page></x-slot:page>
    <div class="col-span-3 grid grid-cols-[auto_1fr] gap-2">

        <a href="{{ route('kelola.panel.kategori') }}"
            class="border-2 py-2 px-4 border-[#069806] rounded-[5px] text-[#069806]">kelola kategori</a>
        <span></span>
        <a href="{{ route('kelola.panel.barang') }}"
            class="border-2 py-2 px-4 border-[#069806] rounded-[5px] text-[#069806]">kelola barang</a>
    </div>
</x-admin>
