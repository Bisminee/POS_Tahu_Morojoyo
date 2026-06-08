<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tahu Bakso Morojoyo</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.3/cdn.min.js" defer></script>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              red: '#C0271A',
              darkred: '#9B1E13',
              yellow: '#F5C518',
              cream: '#FFF8E7',
            }
          },
          fontFamily: {
            display: ['Bebas Neue', 'sans-serif'],
            body: ['Nunito', 'sans-serif'],
          },
          keyframes: {
            fadeUp: {
              '0%': { opacity: '0', transform: 'translateY(30px)' },
              '100%': { opacity: '1', transform: 'translateY(0)' },
            },
            float: {
              '0%, 100%': { transform: 'translateY(0px)' },
              '50%': { transform: 'translateY(-10px)' },
            },
          },
          animation: {
            'fade-up': 'fadeUp 0.7s ease-out forwards',
            'float': 'float 3s ease-in-out infinite',
          }
        }
      }
    }
  </script>
  <style>
    body { font-family: 'Nunito', sans-serif; }
    .hero-bg {
      background: radial-gradient(ellipse at 60% 50%, #d63a2a 0%, #C0271A 50%, #8a1a10 100%);
    }
    .card-hover {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card-hover:hover {
      transform: translateY(-6px);
      box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }
    .nav-link {
      position: relative;
    }
    .nav-link::after {
      content: '';
      position: absolute;
      bottom: -4px;
      left: 0;
      width: 0;
      height: 2px;
      background: #F5C518;
      transition: width 0.3s ease;
    }
    .nav-link:hover::after, .nav-link.active::after {
      width: 100%;
    }
    .stagger-1 { animation: fadeUp 0.7s ease-out 0.1s forwards; opacity: 0; }
    .stagger-2 { animation: fadeUp 0.7s ease-out 0.25s forwards; opacity: 0; }
    .stagger-3 { animation: fadeUp 0.7s ease-out 0.4s forwards; opacity: 0; }
    .stagger-4 { animation: fadeUp 0.7s ease-out 0.55s forwards; opacity: 0; }
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
    }
    .float-1 { animation: float 3s ease-in-out infinite; }
    .float-2 { animation: float 3s ease-in-out 0.3s infinite; }
    .float-3 { animation: float 3s ease-in-out 0.6s infinite; }
  </style>
</head>
<body class="bg-white overflow-x-hidden" x-data="{ menuOpen: false }">

  <!-- NAVBAR -->
  <nav class="sticky top-0 z-50 bg-white border-b-4 border-brand-red shadow-md">
    <div class="max-w-6xl mx-auto px-6 py-5 flex items-center justify-between">
      <a href="#home" class="flex items-center gap-2 group">
          @if(isset($identitas) && $identitas->logo)
            <img src="{{ asset('storage/' . $identitas->logo) }}" alt="{{ $identitas->nama_brand ?? 'Logo' }}"class="h-10 w-auto">
          @endif
      </a>
      <ul class="hidden md:flex items-center gap-8">
        <li><a href="{{ route('home') }}" class="nav-link font-body text-gray-600 font-bold text-sm tracking-wide hover:text-brand-red transition-colors">Home</a></li>
        <li><a href="{{ route('menu') }}" class="nav-link active font-body text-gray-800 font-semibold text-sm tracking-wide hover:text-brand-red transition-colors">Menu</a></li>
        <li><a href="{{ route('contact') }}" class="nav-link font-body text-gray-600 font-semibold text-sm tracking-wide hover:text-brand-red transition-colors">Contact</a></li>
        <li><a href="{{ route('about') }}" class="nav-link font-body text-gray-600 font-semibold text-sm tracking-wide hover:text-brand-red transition-colors">Location</a></li>
        <li>
          <a href="#menu" class="bg-brand-red text-white font-bold text-sm px-5 py-2 rounded-full hover:bg-brand-darkred transition-colors shadow-md">
            Pesan Sekarang
          </a>
        </li>
      </ul>
      <button @click="menuOpen = !menuOpen" class="md:hidden text-brand-red focus:outline-none">
        <svg x-show="!menuOpen" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        <svg x-show="menuOpen" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
    <div x-show="menuOpen" x-transition class="md:hidden bg-white border-t border-gray-100 px-6 py-4 flex flex-col gap-4">
      <a href="{{ route('home') }}" class="font-bold text-brand-red text-sm">Home</a>
      <a href="{{ route('menu') }}" class="font-semibold text-gray-700 text-sm">Menu</a>
      <a href="{{ route('contact') }}" class="font-semibold text-gray-700 text-sm">Contact</a>
      <a href="{{ route('about') }}" class="font-semibold text-gray-700 text-sm">Location</a>
      <a href="" class="bg-brand-red text-white font-bold text-sm px-5 py-2 rounded-full text-center">Pesan Sekarang</a>
    </div>
  </nav>

<!-- MENU -->
<section id="menu" class="bg-brand-cream py-20">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-14">
            <span class="text-brand-red font-body font-bold text-sm tracking-widest uppercase">Pilihan Kami</span>
            <h2 class="font-display text-brand-red text-5xl mt-2">MENU</h2>
            <div class="w-16 h-1 bg-brand-yellow mx-auto mt-3 rounded-full"></div>
        </div>

        <div class="flex flex-wrap justify-center gap-8">
            @forelse ($menus as $menu)
                <div class="bg-white rounded-3xl overflow-hidden shadow-md card-hover w-full sm:w-[calc(50%-1rem)] lg:w-[calc(33.333%-1.4rem)]">

                    <div class="h-64 bg-brand-red flex items-center justify-center relative p-8">
                        @if($menu->foto)
                            <img src="{{ asset('storage/' . $menu->foto) }}"
                                 alt="{{ $menu->namaMenu }}"
                                 class="w-full h-full object-contain float-1">
                        @else
                            <span class="text-8xl float-1">🍢</span>
                        @endif

                        @if($menu->tagline)
                            <span class="absolute top-3 right-3 bg-brand-yellow text-brand-darkred font-display text-xs px-3 py-1 rounded-full">
                                {{ $menu->tagline }}
                            </span>
                        @endif
                    </div>

                    <div class="p-5">
                        <h3 class="font-display text-brand-red text-2xl">{{ strtoupper($menu->namaMenu) }}</h3>
                        @if($menu->deskripsi)
                            <p class="text-gray-500 text-sm mt-1 font-body">{{ $menu->deskripsi }}</p>
                        @endif
                        <div class="mt-4 text-right">
                            @if($menu->harga)
                                <span class="font-display text-brand-red text-3xl">
                                    Rp {{ number_format($menu->harga->harga_normal, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="font-display text-gray-400 text-xl">Harga belum diset</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-400 font-body py-10">
                    <span class="text-5xl block mb-3">🍢</span>
                    Belum ada menu tersedia.
                </div>
            @endforelse
        </div>
    </div>
</section>

  <!-- FOOTER -->
  <footer class="bg-white border-t-4 border-brand-red pt-12 pb-0">
      <div class="max-w-6xl mx-auto px-6">
          <div class="grid grid-cols-2 md:grid-cols-4 gap-8 pb-10">

              <!-- Logo + desc -->
              <div class="col-span-2 md:col-span-1">
                  <div class="rounded-xl inline-block mb-4">
                      @if(isset($identitas) && $identitas->logo)
                          <img src="{{ asset('storage/' . $identitas->logo) }}"
                              alt="{{ $identitas->nama_brand ?? 'Logo' }}"
                              class="h-10 w-auto">
                      @endif
                  </div>
                  @if(isset($identitas) && $identitas->deskripsi_brand)
                      <p class="font-body text-gray-500 text-sm leading-relaxed">
                          {{ $identitas->deskripsi_brand }}
                      </p>
                  @endif
              </div>

              <!-- Navigation -->
              <div>
                  <h4 class="font-display text-brand-red text-xl mb-4">Navigation</h4>
                  <ul class="space-y-2">
                      <li><a href="{{ route('home') }}"    class="font-body text-gray-500 text-sm hover:text-brand-red transition">Home</a></li>
                      <li><a href="{{ route('menu') }}"    class="font-body text-gray-500 text-sm hover:text-brand-red transition">Menu</a></li>
                      <li><a href="{{ route('contact') }}" class="font-body text-gray-500 text-sm hover:text-brand-red transition">Contact</a></li>
                      <li><a href="{{ route('about') }}"   class="font-body text-gray-500 text-sm hover:text-brand-red transition">Location</a></li>
                  </ul>
              </div>

              <!-- Get in Touch -->
              <div>
                  <h4 class="font-display text-brand-red text-xl mb-4">Get in Touch</h4>
                  <ul class="space-y-2">
                      @if(isset($identitas) && $identitas->link_ig)
                          <li>
                              <a href="{{ $identitas->link_ig }}" target="_blank"
                                class="font-body text-gray-500 text-sm hover:text-brand-red transition flex items-center gap-2">
                                  <img src="{{ asset('img/ig.png') }}" class="h-5 w-5 object-contain">
                                  {{ $identitas->nama_ig ?? '@tahubakso.morojoyo' }}
                              </a>
                          </li>
                      @endif
                      @if(isset($identitas) && $identitas->link_wa)
                          <li>
                              <a href="{{ $identitas->link_wa }}" target="_blank"
                                class="font-body text-gray-500 text-sm hover:text-brand-red transition flex items-center gap-2">
                                  <img src="{{ asset('img/wa.png') }}" class="h-5 w-5 object-contain">
                                  {{ $identitas->nomor_whatsapp ?? '' }}
                              </a>
                          </li>
                      @endif
                  </ul>
              </div>

              <!-- Jam Buka -->
              <div>
                  <h4 class="font-display text-brand-red text-xl mb-4">Jam Buka</h4>
                  @if(isset($identitas))
                      <ul class="space-y-1">
                          <li class="font-body text-gray-500 text-sm">Setiap Hari</li>
                          <li class="font-body text-brand-red text-sm font-bold">
                              {{ $identitas->jam_buka ?? '10:00' }} – {{ $identitas->jam_tutup ?? '21:00' }} WIB
                          </li>
                      </ul>
                  @endif
              </div>

          </div>
      </div>

      <!-- Copyright bar -->
      <div class="bg-brand-red">
          <div class="max-w-6xl mx-auto px-6 py-4 flex flex-col md:flex-row items-center justify-between gap-2">
              <p class="font-body text-white text-sm">Copyright © 2026 Kelompok 4</p>
              <p class="font-body text-red-200 text-xs">
                  {{ isset($identitas) ? $identitas->nama_brand : 'Tahu Bakso Morojoyo' }} — Malang, Jawa Timur
              </p>
          </div>
      </div>
  </footer>

</body>
</html>