<x-layout>
    <x-slot:title>Toko Online Sederhana</x-slot:title>

    <div class="grid grid-cols-1 grid-rows-[auto_1fr_auto]">
        <x-head>
            @if (isset($user))
                <x-slot:user>{{ explode(' ', $user->nama)[0] }}</x-slot:user>
                <x-slot:uname>{{ $user->username }}</x-slot:uname>
            @endif

        </x-head>
    </div>
</x-layout>
