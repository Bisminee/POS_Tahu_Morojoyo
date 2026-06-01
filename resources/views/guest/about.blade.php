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

    @keyframes marquee {
      from { transform: translateX(0); }
      to { transform: translateX(-50%); }
    }
    .marquee-inner {
      display: flex;
      animation: marquee 18s linear infinite;
      width: max-content;
    }
  </style>
</head>
<body class="bg-white overflow-x-hidden" x-data="{ menuOpen: false }">

  <!-- NAVBAR -->
  <nav class="sticky top-0 z-50 bg-white border-b-4 border-brand-red shadow-md">
    <div class="max-w-6xl mx-auto px-6 py-3 flex items-center justify-between">
      <a href="#home" class="flex items-center gap-2 group">
        <div class="bg-brand-red rounded-xl px-4 py-2 group-hover:bg-brand-darkred transition-colors">
          <p class="text-brand-yellow text-[9px] font-display tracking-[0.25em] text-center leading-none">· TAHU BAKSO ·</p>
          <p class="text-brand-yellow font-display text-2xl leading-none tracking-wider">MOROJOYO</p>
        </div>
      </a>
      <ul class="hidden md:flex items-center gap-8">
        <li><a href="{{ route('home') }}" class="nav-link font-body text-gray-600 font-semibold text-sm tracking-wide hover:text-brand-red transition-colors">Home</a></li>
        <li><a href="{{ route('menu') }}" class="nav-link font-body text-gray-600 font-semibold text-sm tracking-wide hover:text-brand-red transition-colors">Menu</a></li>
        <li><a href="{{ route('contact') }}" class="nav-link font-body text-gray-600 font-semibold text-sm tracking-wide hover:text-brand-red transition-colors">Contact</a></li>
        <li><a href="{{ route('about') }}" class="nav-link active font-body text-gray-800 font-bold text-sm tracking-wide hover:text-brand-red transition-colors">Location</a></li>
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
      <a href="#home" class="font-bold text-brand-red text-sm">Home</a>
      <a href="#menu" class="font-semibold text-gray-700 text-sm">Menu</a>
      <a href="#contact" class="font-semibold text-gray-700 text-sm">Contact</a>
      <a href="#location" class="font-semibold text-gray-700 text-sm">Location</a>
      <a href="#menu" class="bg-brand-red text-white font-bold text-sm px-5 py-2 rounded-full text-center">Pesan Sekarang</a>
    </div>
  </nav>


<!-- LOCATION -->
  <section id="location" class="bg-brand-red py-20">
    <div class="max-w-6xl mx-auto px-6">
      <div class="text-center mb-14">
        <span class="text-brand-yellow font-body font-bold text-sm tracking-widest uppercase">Temukan Kami</span>
        <h2 class="font-display text-white text-5xl mt-2">LOKASI</h2>
        <div class="w-16 h-1 bg-brand-yellow mx-auto mt-3 rounded-full"></div>
      </div>
      <div class="grid md:grid-cols-2 gap-10 items-center">
        <div class="text-white space-y-6">
          <div>
            <h3 class="font-display text-brand-yellow text-3xl mb-2">TAHU BAKSO MOROJOYO</h3>
            <p class="font-body text-red-100 leading-relaxed">
              Jl. Morojoyo No. XX, Kecamatan Sukun,<br/>
              Kota Malang, Jawa Timur 65148
            </p>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-brand-darkred rounded-2xl p-4">
              <p class="font-display text-brand-yellow text-4xl">10+</p>
              <p class="font-body text-red-200 text-sm">Tahun Berdiri</p>
            </div>
            <div class="bg-brand-darkred rounded-2xl p-4">
              <p class="font-display text-brand-yellow text-4xl">500+</p>
              <p class="font-body text-red-200 text-sm">Pelanggan/Hari</p>
            </div>
            <div class="bg-brand-darkred rounded-2xl p-4">
              <p class="font-display text-brand-yellow text-4xl">20+</p>
              <p class="font-body text-red-200 text-sm">Varian Menu</p>
            </div>
            <div class="bg-brand-darkred rounded-2xl p-4">
              <p class="font-display text-brand-yellow text-4xl">⭐ 4.9</p>
              <p class="font-body text-red-200 text-sm">Rating Pelanggan</p>
            </div>
          </div>
          <a href="https://maps.google.com" target="_blank" class="inline-block bg-brand-yellow text-brand-darkred font-display text-xl px-8 py-3 rounded-full hover:brightness-110 transition shadow-lg">
            📍 Buka di Google Maps
          </a>
        </div>
        <div class="bg-brand-darkred rounded-3xl overflow-hidden h-72 flex items-center justify-center relative shadow-2xl">
          <div class="absolute inset-0 opacity-20" style="background-image: repeating-linear-gradient(0deg,transparent,transparent 30px,rgba(255,255,255,.1) 30px,rgba(255,255,255,.1) 31px),repeating-linear-gradient(90deg,transparent,transparent 30px,rgba(255,255,255,.1) 30px,rgba(255,255,255,.1) 31px);"></div>
          <div class="text-center relative z-10">
            <span class="text-6xl block mb-3">📍</span>
            <p class="font-display text-white text-2xl">MALANG, JAWA TIMUR</p>
            <p class="text-red-200 font-body text-sm mt-1">Klik untuk buka Google Maps</p>
          </div>
        </div>
      </div>
    </div>
  </section>

    <!-- FOOTER -->
  <footer class="bg-white border-t-4 border-brand-red pt-12 pb-0">
    <div class="max-w-6xl mx-auto px-6">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-8 pb-10">
 
        <!-- Logo + desc -->
        <div class="col-span-2 md:col-span-1">
          <div class="rounded-xl px-4 py-2 inline-block mb-4">
            <img src="{{ asset('img/logo.png') }}" class="w-full h-10">
          </div>
          <p class="font-body text-gray-500 text-sm leading-relaxed">
            Jl MT. Haryono No.43,<br/>
            Ketawanggede, Kec. Lowokwaru,<br/>
            Kota Malang, Jawa Timur 65145
          </p>
        </div>
 
        <!-- Navigation -->
        <div>
          <h4 class="font-display text-brand-red text-xl mb-4">Navigation</h4>
          <ul class="space-y-2">
            <li><a href="{{ route('home') }}" class="font-body text-gray-500 text-sm hover:text-brand-red transition">Home</a></li>
            <li><a href="{{ route('menu') }}" class="font-body text-gray-500 text-sm hover:text-brand-red transition">Menu</a></li>
            <li><a href="{{ route('contact') }}" class="font-body text-gray-500 text-sm hover:text-brand-red transition">Contact</a></li>
            <li><a href="{{ route('about') }}" class="font-body text-gray-500 text-sm hover:text-brand-red transition">Location</a></li>
          </ul>
        </div>
 
        <!-- Get in Touch -->
        <div>
          <h4 class="font-display text-brand-red text-xl mb-4">Get in Touch</h4>
          <ul class="space-y-2">
            <li><a href="https://instagram.com" class="font-body text-gray-500 text-sm hover:text-brand-red transition flex items-center gap-2"><span><img src="{{ asset('img/ig.png') }}" class="w-full h-5"></span> @tahubakso.morojoyo</a></li>
            <li><a href="https://wa.me/62812" class="font-body text-gray-500 text-sm hover:text-brand-red transition flex items-center gap-2"><span><img src="{{ asset('img/wa.png') }}" class="w-full h-5"></span> +62 812-XXXX-XXXX</a></li>
            <li><a href="https://maps.google.com" class="font-body text-gray-500 text-sm hover:text-brand-red transition flex items-center gap-2"><span><img src="{{ asset('img/gmap.png') }}" class="w-full h-5"></span> Google Maps</a></li>
          </ul>
        </div>
 
        <!-- Jam Buka -->
        <div>
          <h4 class="font-display text-brand-red text-xl mb-4">Jam Buka</h4>
          <ul class="space-y-2">
            <li class="font-body text-gray-500 text-sm">Senin – Sabtu</li>
            <li class="font-body text-brand-red text-sm font-bold">07.00 – 18.00 WIB</li>
            <li class="font-body text-gray-500 text-sm mt-2">Minggu</li>
            <li class="font-body text-brand-red text-sm font-bold">08.00 – 15.00 WIB</li>
          </ul>
        </div>
 
      </div>
    </div>
 
    <!-- Copyright bar -->
    <div class="bg-brand-red">
      <div class="max-w-6xl mx-auto px-6 py-4 flex flex-col md:flex-row items-center justify-between gap-2">
        <p class="font-body text-white text-sm">Copyright © 2026 Kelompok 4</p>
        <p class="font-body text-red-200 text-xs">Tahu Bakso Morojoyo — Malang, Jawa Timur</p>
      </div>
    </div>
  </footer>

</body>
</html>
