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
        <li><a href="{{ route('home') }}" class="nav-link active font-body text-gray-800 font-bold text-sm tracking-wide hover:text-brand-red transition-colors">Home</a></li>
        <li><a href="{{ route('menu') }}" class="nav-link font-body text-gray-600 font-semibold text-sm tracking-wide hover:text-brand-red transition-colors">Menu</a></li>
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

  <!-- HERO -->
  <section id="home" class="hero-bg relative overflow-hidden min-h-screen flex items-center">
    <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-white opacity-5"></div>
    <div class="absolute -bottom-32 -right-20 w-[500px] h-[500px] rounded-full bg-black opacity-10"></div>
    <div class="absolute top-10 right-10 w-40 h-40 rounded-full border-4 border-brand-yellow opacity-20"></div>

    <div class="max-w-6xl mx-auto px-6 py-20 grid md:grid-cols-2 gap-12 items-center relative z-10">
      <div>
        <span class="inline-block bg-brand-yellow text-brand-darkred font-display text-sm tracking-widest px-4 py-1.5 rounded-full mb-5 stagger-1">
            KULINER KHAS MALANG
        </span>
        <h1 class="font-display text-white text-6xl md:text-7xl leading-none mb-4 stagger-2">
          TAHU BAKSO<br/>
          <span class="text-brand-yellow">MOROJOYO</span>
        </h1>
        <p class="text-red-100 font-body text-base leading-relaxed mb-8 max-w-md stagger-3">
          Cita rasa otentik tahu bakso Malang yang sudah dipercaya sejak lama. Dibuat segar setiap hari dari bahan pilihan terbaik.
        </p>
        <div class="flex flex-wrap gap-4 stagger-4">
          <a href="{{ route('menu') }}" class="bg-brand-yellow text-brand-darkred font-display text-lg px-8 py-3 rounded-full hover:brightness-110 transition shadow-lg">
            Lihat Menu →
          </a>
          <a href="{{ route('about') }}" class="border-2 border-white text-white font-body font-bold px-8 py-3 rounded-full hover:bg-white hover:text-brand-red transition">
            Temukan Kami
          </a>
        </div>
      </div>

      <!-- CAROUSEL -->
      <div
        x-data="{
            active: 1,
            items: [
            { emoji: 'img/ori.png', name: 'TAHU BAKSO ORIGINAL',  label: 'Best Seller', price: 'Rp 3.000' },
            { emoji: 'img/pedas.png', name: 'TAHU BAKSO PEDAS',     label: 'Pedas Menggigit',     price: 'Rp 3.500' },
            { emoji: 'img/keju.png', name: 'TAHU BAKSO KEJU',      label: 'Keju Lumer Gurih',  price: 'Rp 3.500' },
            { emoji: 'img/mix.png', name: 'TAHU BAKSO MIX',   label: 'Paling Lengkap',    price: 'Rp 3.500' },
            { emoji: 'img/s_cheese.png', name: 'TAHU BAKSO S.CHEESE',       label: 'Spicy Cheese', price: 'Rp 3.500' },
            ],
            prev() { this.active = this.active === 0 ? this.items.length - 1 : this.active - 1 },
            next() { this.active = this.active === this.items.length - 1 ? 0 : this.active + 1 },
            pos(i) {
            let diff = i - this.active;
            if (diff < -(this.items.length/2)) diff += this.items.length;
            if (diff > (this.items.length/2))  diff -= this.items.length;
            return diff;
            }
        }"
        x-init="setInterval(() => next(), 3500)"
        class="relative flex justify-center items-center h-80 select-none"
        style="perspective: 900px;"
        >
        <button @click="prev()" class="absolute left-0 z-20 w-9 h-9 rounded-full bg-white/20 hover:bg-white/40 text-white flex items-center justify-center transition backdrop-blur-sm border border-white/30">
            ‹
        </button>

        <template x-for="(item, i) in items" :key="i">
            <div
            :style="{
                position: 'absolute',
                transition: 'all 0.5s cubic-bezier(0.4,0,0.2,1)',
                transform: `translateX(${pos(i) * 68}%) scale(${pos(i) === 0 ? 1 : 0.78}) translateZ(${pos(i) === 0 ? 0 : -80}px)`,
                opacity: Math.abs(pos(i)) > 1 ? 0 : (pos(i) === 0 ? 1 : 0.55),
                zIndex: 10 - Math.abs(pos(i)),
                pointerEvents: pos(i) === 0 ? 'auto' : 'none',
            }"
            class="w-52 h-72 bg-white rounded-3xl shadow-2xl flex flex-col items-center justify-center p-5"
            >
            <div class="w-32 h-32 bg-brand-cream rounded-full flex items-center justify-center mb-4 overflow-hidden px-2">
                <template x-if="item.emoji.includes('/')">
                    <img :src="item.emoji" class="w-24 h-24 object-contain">
                </template>
                
                <template x-if="!item.emoji.includes('/')">
                    <span class="text-6xl" x-text="item.emoji"></span>
                </template>
            </div>

            <p class="font-display text-brand-red text-xl text-center leading-tight uppercase" x-text="item.name"></p>
            <p class="text-gray-500 text-xs mt-1 font-body" x-text="item.label"></p>
            
            <div class="mt-3 bg-brand-yellow text-brand-darkred font-display text-lg px-5 py-1.5 rounded-full shadow-sm" x-text="item.price"></div>
            </div>
        </template>

        <button @click="next()" class="absolute right-0 z-20 w-9 h-9 rounded-full bg-white/20 hover:bg-white/40 text-white flex items-center justify-center transition backdrop-blur-sm border border-white/30">
            ›
        </button>

        <div class="absolute -bottom-8 left-0 right-0 flex justify-center gap-2">
            <template x-for="(item, i) in items" :key="i">
            <button
                @click="active = i"
                :class="active === i ? 'bg-brand-yellow w-5' : 'bg-white/40 w-2'"
                class="h-2 rounded-full transition-all duration-300"
            ></button>
            </template>
        </div>
    </div>

    
  </section>

  <!-- MARQUEE -->
  <section class="bg-brand-yellow py-3 overflow-hidden">
    <div class="marquee-inner">
      <span class="font-display text-brand-darkred text-lg tracking-widest mx-8">TAHU BAKSO SEGAR</span>
      <span class="font-display text-brand-darkred text-lg tracking-widest mx-8">CITA RASA ASLI MALANG</span>
      <span class="font-display text-brand-darkred text-lg tracking-widest mx-8">DIBUAT SETIAP HARI</span>
      <span class="font-display text-brand-darkred text-lg tracking-widest mx-8">KUALITAS PREMIUM HARGA TERJANGKAU</span>
      <span class="font-display text-brand-darkred text-lg tracking-widest mx-8">TAHU BAKSO SEGAR</span>
      <span class="font-display text-brand-darkred text-lg tracking-widest mx-8">CITA RASA ASLI MALANG</span>
      <span class="font-display text-brand-darkred text-lg tracking-widest mx-8">DIBUAT SETIAP HARI</span>
      <span class="font-display text-brand-darkred text-lg tracking-widest mx-8">KUALITAS PREMIUM HARGA TERJANGKAU</span>
    </div>
  </section>

  <!-- KEUNGGULAN -->
  <section class="bg-white py-20">
    <div class="max-w-6xl mx-auto px-6">
      <div class="grid md:grid-cols-3 gap-8">
        <div class="text-center group">
          <div class="w-16 h-16 bg-brand-yellow rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-brand-red transition-colors duration-300">
            <span class="text-3xl"><img src="{{ asset('img/badge.png') }}" class="w-7 h-full"></span>
          </div>
          <h3 class="font-display text-brand-red text-2xl mb-2">BAHAN SEGAR</h3>
          <p class="text-gray-500 font-body text-sm leading-relaxed">Dipilih langsung dari pasar setiap pagi untuk menjamin kesegaran dan kelezatan.</p>
        </div>
        <div class="text-center group">
          <div class="w-16 h-16 bg-brand-yellow rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-brand-red transition-colors duration-300">
            <span class="text-3xl"><img src="{{ asset('img/chef.png') }}" class="w-7 h-full"></span>
          </div>
          <h3 class="font-display text-brand-red text-2xl mb-2">RASA AUTENTIK</h3>
          <p class="text-gray-500 font-body text-sm leading-relaxed">Bumbu khas turun-temurun yang menghadirkan cita rasa autentik Malang.</p>
        </div>
        <div class="text-center group">
          <div class="w-16 h-16 bg-brand-yellow rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:bg-brand-red transition-colors duration-300">
            <span class="text-3xl"><img src="{{ asset('img/pack.png') }}" class="w-7 h-full"></span>
          </div>
          <h3 class="font-display text-brand-red text-2xl mb-2">KEMASAN AMAN</h3>
          <p class="text-gray-500 font-body text-sm leading-relaxed">Produk dikemas secara higienis sehingga tetap terjaga kualitas dan rasanya sampai di rumah Anda.</p>
        </div>
      </div>
    </div>
  </section>

          <!-- PROMO CARDS -->
  <section class="bg-brand-cream py-16">
    <div class="max-w-6xl mx-auto px-6">
      <div class="text-center mb-10">
        <span class="text-brand-red font-body font-bold text-sm tracking-widest uppercase">Penawaran Terbatas</span>
        <h2 class="font-display text-brand-red text-5xl mt-2">PROMO SPESIAL</h2>
        <div class="w-16 h-1 bg-brand-yellow mx-auto mt-3 rounded-full"></div>
      </div>
      <div class="grid md:grid-cols-2 gap-6">
 
        <!-- Promo Card 1 -->
        <div class="bg-brand-red rounded-3xl overflow-hidden shadow-lg card-hover flex flex-col">
            <img src="{{ asset('img/promo2.jpg') }}" class="w-full">
        </div>
 
        <!-- Promo Card 2 -->
        <div class="rounded-3xl overflow-hidden shadow-lg card-hover flex flex-col">
            <img src="{{ asset('img/promo1.jpg') }}" class="w-full">
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