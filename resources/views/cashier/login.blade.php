@props(['title' => 'Login Kasir'])
<script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            red:        '#C0271A',
                            'red-dk':   '#96281B',
                            cream:      '#FAF6EF',
                            'cream-dk': '#F0E9DC',
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; background: #FAF6EF; }</style>
    
<x-layouts.app :title="$title">
    <div class="min-h-screen bg-[#FFF8E7] flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">

            {{-- Logo --}}
            <div class="flex justify-center mb-8">
                @if(isset($identitas) && $identitas->logo)
                    <img src="{{ asset('storage/' . $identitas->logo) }}" alt="{{ $identitas->nama_brand ?? 'Logo' }}"class="h-20 w-auto mx-auto block">
                @endif
            </div>

            {{-- Card --}}
            <div class="bg-white rounded-3xl shadow-xl border-t-4 border-[#C0271A] overflow-hidden">

                {{-- Card header --}}
                <div class="bg-[#C0271A] px-8 py-6 text-center">
    <h1 class="text-white font-black text-2xl leading-tight" style="font-family:'Bebas Neue',sans-serif">
        LOGIN KASIR
    </h1>
    <p class="text-red-200 text-xs mt-1">
        Masuk ke sistem POS Morojoyo
    </p>
</div>

                <div class="px-8 py-7">

                    {{-- Info box --}}
                    <div class="bg-[#FFF8E7] border border-[#F5C518]/40 rounded-2xl px-4 py-3 mb-6 flex gap-3 items-start">
                        <p class="text-gray-600 text-xs leading-relaxed">
                            Gunakan akun kasir cabang untuk masuk.<br/>
                            Contoh: <strong class="text-[#C0271A]">kasir.cabang1@gmail.com</strong> untuk Dinoyo &amp; <strong class="text-[#C0271A]">kasir.cabang2@gmail.com</strong> untuk Suhat.
                        </p>
                    </div>

                    {{-- Error --}}
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-2xl px-4 py-3 mb-6">
                            <p class="text-red-700 text-sm font-bold flex items-center gap-2">
                                <span></span> Terjadi kesalahan:
                            </p>
                            <ul class="mt-2 list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li class="text-red-600 text-xs">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Form --}}
                    <form action="{{ route('cashier.login.submit') }}" method="POST" class="space-y-5">
                        @csrf

                        <div>
                            <label class="block text-xs font-bold text-[#C0271A] mb-2 uppercase tracking-wide">
                                Email Kasir <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    placeholder="kasir@gmail.com"
                                    class="w-full border-2 border-gray-200 rounded-2xl pl-10 pr-4 py-3 text-sm outline-none transition focus:border-[#C0271A] focus:ring-4 focus:ring-[#C0271A]/10 bg-gray-50 font-medium"
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-[#C0271A] mb-2 uppercase tracking-wide">
                                Password <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    type="password"
                                    name="password"
                                    required
                                    placeholder="••••••••"
                                    class="w-full border-2 border-gray-200 rounded-2xl pl-10 pr-4 py-3 text-sm outline-none transition focus:border-[#C0271A] focus:ring-4 focus:ring-[#C0271A]/10 bg-gray-50 font-medium"
                                />
                            </div>
                        </div>

                        <button
    type="submit"
    class="w-full bg-[#C0271A] hover:bg-[#9B1E13] active:scale-[.98] text-white font-black text-base md:text-sm py-3.5 rounded-2xl transition-all shadow-lg shadow-red-200 flex items-center justify-center gap-2 mt-2"
    style="font-family:'Bebas Neue',sans-serif; letter-spacing:.04em"
>
    <span>MASUK SEBAGAI KASIR</span>
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14"/>
    </svg>
</button>
                    </form>

                    {{-- Footer note --}}
                    <p class="text-center text-xs text-gray-400 mt-6">
                        Lupa password? Hubungi
                        <span class="text-[#C0271A] font-bold">Administrator</span>
                    </p>
                </div>
            </div>

            {{-- Copyright --}}
            <p class="text-center text-xs text-gray-400 mt-6">
                © 2026 Tahu Bakso Morojoyo · Kelompok 4
            </p>
        </div>
    </div>
</x-layouts.app>