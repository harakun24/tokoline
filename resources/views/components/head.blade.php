 <div
     class="bg-[#70dd28] py-2 sm:px-4 pl-0 ml-0  grid gap-5 place-items-center text-[#234a0a] grid-cols-[repeat(5,20%)] sm:grid-cols-[80px_auto_1fr_1fr_auto]">
     <a href="{{ route('home') }}">

         <img src="{{ asset('images/icon-tokoline.png') }}" class="col-start-2 sm:col-start-1 self-end opacity-[85%]"
             alt="">
     </a>
     <a href="{{ route('home') }}">
         <h2 class="font-bold sm:col-span-4 col-start-3 sm:col-start-2 sm:col-end-3 text-[#0b4053b8] underline">TokoLine
         </h2>
     </a>
     <div
         class="w-[100%] flex justify-end gap-3 sm:justify-around sm:gap-0 col-start-1 col-end-4 md:col-start-3 md:col-end-4 sm:col-start-1 sm:col-end-3">
         <select class=" bg-transparent" name="kategori" id="">
             <option value="0">Kategori</option>
         </select>
         <a href="#">Diskon</a>
         <a href="#">Pengiriman</a>
     </div>
     <div class=" relative col-start-1 col-end-5 md:col-start-4 md:col-end-5 sm:col-start-3 sm:col-end:5">
         {{-- search --}}
         <form action="{{ route('search') }}">
             <input type="text" class="outline-none  rounded-[5px] p-2 w-[100%] peer" placeholder="Cari barang"
                 name="query">
         </form>
         <i
             class="fa fa-search absolute top-[50%] -translate-y-[50%] -translate-x-[100%] right-0 peer-placeholder-shown:opacity-100 opacity-0"></i>
     </div>
     <div class="flex gap-3 col-start-2 col-end-4 sm:col-start-5 sm:col-end-6">
         @if (isset($user))
             <div class="relative inline-block text-left">
                 <button class="dropdown-btn">
                     <i class="fa-regular fa-user"></i>
                     {{ $user }} <i class="fa fa-caret-down"></i></button>

                 <div
                     class="absolute dropdown-menu mt-2 rounded-md bg-white shadow-md hidden py-2 px-4 flex flex-col gap-3 whitespace-nowrap">
                     <a href="{{ route('profil.show') }}" class="dropdown-item"><i class="fa fa-user-alt mr-2"></i>
                         Profil</a>
                     <a href="#" class="dropdown-item">
                         <i class="fa fa-heart text-red-600 mr-2"></i>Favorit</a>
                     <a href="#" onclick="confirmOut()" class="dropdown-item"><i
                             class="fa fa-power-off mr-2 text-red-700"></i>Keluar</a>
                 </div>
             </div>
             <script>
                 document.querySelector('.dropdown-btn').addEventListener('click', function() {
                     document.querySelector('.dropdown-menu').classList.toggle('hidden');
                 })
             </script>


             <script>
                 function confirmOut() {
                     Swal.fire({
                         title: 'Yakin ingin keluar?',
                         text: 'tekan lanjut untuk keluar.',
                         showCancelButton: true,
                         confirmButtonText: 'Lanjut',
                         cancelButtonText: 'batal',
                         icon: 'warning',
                     }).then((result) => {
                         if (result.isConfirmed)
                             document.querySelector('.rm_session').click();
                     })
                 }
             </script>

             {{-- keranjang --}}
             <a href="{{ route('keranjang.show') }}"> <i class="fa fa-shopping-cart p-1"></i> Cart</a>
         @else
             {{-- <h3></h3> --}}
             <a href="{{ route('login.page') }}"> <i class="fa-regular fa-user p-1"></i> Masuk</a>
             <a href="{{ route('login.page') }}"> <i class="fa fa-shopping-cart p-1"></i> Cart</a>
         @endif
         <form action="{{ route('logout') }}" method="POST">
             @csrf
             <button type="submit" class="rm_session"></button>
         </form>
     </div>
 </div>
