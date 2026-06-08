 <!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tahu Bakso Morojoyo</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.3/cdn.min.js" defer></script>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet"/>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white overflow-x-hidden" x-data="{ menuOpen: false }">
<x-filament-panels::page>

    {{-- ═══ HEADER ═══ --}}
    <div class="mb-8">
        <p style="font-family:'Nunito',sans-serif;font-size:.875rem;
                  font-weight:600;color:#9ca3af;">
            Selamat datang kembali, Administrator 👋
        </p>
        <h1 style="font-family:'Bebas Neue',sans-serif;font-size:3.5rem;
                   line-height:1;color:#C0271A;letter-spacing:.06em;">
            DASHBOARD
        </h1>
        <div style="width:3.5rem;height:6px;background:#F5C518;
                    border-radius:99px;margin-top:.4rem;"></div>
    </div>

    {{-- ═══ STATS GRID ═══ --}}
    @php
        $stats = [
            [
                'label'    => 'Total Cabang',
                'value'    => $totalCabang,
                'desc'     => 'Jumlah cabang terdaftar',
                'badge'    => 'Aktif',
                'icon'     => '🏪',
                'color'    => '#C0271A',
                'bar_bg'   => '#C0271A',
                'card_bg'  => '#FFF8E7',
                'badge_bg' => '#fef2f2',
                'badge_cl' => '#C0271A',
                'progress' => min(100, $totalCabang * 20),
                'href'     => \Filament\Facades\Filament::getUrl() . '/cabangs',
            ],
            [
                'label'    => 'Total Menu',
                'value'    => $totalMenu,
                'desc'     => 'Jumlah menu tersedia',
                'badge'    => 'Aktif',
                'icon'     => '🍢',
                'color'    => '#C0271A',
                'bar_bg'   => '#C0271A',
                'card_bg'  => '#FFF8E7',
                'badge_bg' => '#fef2f2',
                'badge_cl' => '#C0271A',
                'progress' => min(100, $totalMenu * 20),
                'href'     => \Filament\Facades\Filament::getUrl() . '/menus',
            ],
            [
                'label'    => 'Total Karyawan',
                'value'    => $totalKaryawan,
                'desc'     => 'Jumlah karyawan aktif',
                'badge'    => 'Aktif',
                'icon'     => '👥',
                'color'    => '#16a34a',
                'bar_bg'   => '#4ade80',
                'card_bg'  => '#f0fdf4',
                'badge_bg' => '#f0fdf4',
                'badge_cl' => '#16a34a',
                'progress' => min(100, $totalKaryawan * 10),
                'href'     => \Filament\Facades\Filament::getUrl() . '/karyawans',
            ],
            [
                'label'    => 'Total Stok PCS',
                'value'    => $totalStok,
                'desc'     => 'Akumulasi seluruh stok PCS',
                'badge'    => 'Stok',
                'icon'     => '📦',
                'color'    => '#2563eb',
                'bar_bg'   => '#60a5fa',
                'card_bg'  => '#eff6ff',
                'badge_bg' => '#eff6ff',
                'badge_cl' => '#2563eb',
                'progress' => min(100, ($totalStok / 500) * 100),
                'href'     => \Filament\Facades\Filament::getUrl() . '/stok-pcs',
            ],
            [
                'label'    => 'Menu Belum Punya Harga',
                'value'    => $menuTanpaHarga,
                'desc'     => 'Perlu dilengkapi data harga',
                'badge'    => 'Perlu diisi',
                'icon'     => '💰',
                'color'    => '#ca8a04',
                'bar_bg'   => '#facc15',
                'card_bg'  => '#fefce8',
                'badge_bg' => '#fefce8',
                'badge_cl' => '#ca8a04',
                'progress' => $totalMenu > 0 ? min(100, ($menuTanpaHarga / $totalMenu) * 100) : 0,
                'href'     => \Filament\Facades\Filament::getUrl() . '/hargas',
            ],
            [
                'label'    => 'Stok Menipis',
                'value'    => $stokMenipis,
                'desc'     => 'Stok 10 pcs ke bawah',
                'badge'    => '⚠ Warning',
                'icon'     => '⚠️',
                'color'    => '#dc2626',
                'bar_bg'   => '#f87171',
                'card_bg'  => '#fef2f2',
                'badge_bg' => '#fef2f2',
                'badge_cl' => '#dc2626',
                'progress' => min(100, $stokMenipis * 20),
                'href'     => \Filament\Facades\Filament::getUrl() . '/stok-pcs',
            ],
        ];
    @endphp

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:1.25rem;margin-bottom:2rem;">
        @foreach ($stats as $s)
            <a href="{{ $s['href'] }}"
               style="display:block;background:#fff;border-radius:1rem;padding:1.5rem;
                      border:1px solid #f3f4f6;box-shadow:0 1px 3px rgba(0,0,0,.06);
                      text-decoration:none;transition:transform .25s,box-shadow .25s;"
               onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 16px 40px rgba(0,0,0,.1)'"
               onmouseleave="this.style.transform='translateY(0)';this.style.boxShadow='0 1px 3px rgba(0,0,0,.06)'">

                {{-- Icon + Badge --}}
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:1.25rem;">
                    <div style="width:3.5rem;height:3.5rem;border-radius:.75rem;
                                background:{{ $s['card_bg'] }};display:flex;
                                align-items:center;justify-content:center;font-size:1.5rem;">
                        {{ $s['icon'] }}
                    </div>
                    <span style="font-size:.75rem;font-weight:700;padding:.25rem .75rem;
                                 border-radius:99px;background:{{ $s['badge_bg'] }};
                                 color:{{ $s['badge_cl'] }};">
                        {{ $s['badge'] }}
                    </span>
                </div>

                {{-- Value --}}
                <p style="font-family:'Bebas Neue',sans-serif;font-size:3.5rem;
                           line-height:1;color:{{ $s['color'] }};margin:0;">
                    {{ number_format($s['value']) }}
                </p>
                <p style="font-weight:700;color:#374151;margin:.25rem 0 0;font-size:1rem;">
                    {{ $s['label'] }}
                </p>
                <p style="font-size:.75rem;color:#9ca3af;margin:.125rem 0 0;">
                    {{ $s['desc'] }}
                </p>

                {{-- Progress bar --}}
                <div style="margin-top:1rem;height:6px;background:#f3f4f6;border-radius:99px;overflow:hidden;">
                    <div style="height:6px;border-radius:99px;background:{{ $s['bar_bg'] }};
                                width:{{ $s['progress'] }}%;transition:width .7s;"></div>
                </div>
            </a>
        @endforeach
    </div>

    {{-- ═══ BOTTOM ROW ═══ --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

        {{-- Aktivitas Terbaru --}}
        <div style="background:#fff;border-radius:1rem;padding:1.5rem;
                    border:1px solid #f3f4f6;box-shadow:0 1px 3px rgba(0,0,0,.06);">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;">
                <h2 style="font-family:'Bebas Neue',sans-serif;font-size:1.5rem;
                           color:#C0271A;letter-spacing:.05em;margin:0;">
                    AKTIVITAS TERBARU
                </h2>
                <span style="font-size:.75rem;color:#C0271A;font-weight:700;cursor:pointer;">
                    Lihat semua
                </span>
            </div>
            @php
                $activities = [
                    ['icon'=>'🍢','bg'=>'#C0271A','text'=>'Menu Tahu Bakso Pedas ditambahkan',     'meta'=>'2 jam lalu · oleh Admin',  'badge'=>'Menu',   'bbg'=>'#fef2f2','bcl'=>'#C0271A'],
                    ['icon'=>'💰','bg'=>'#F5C518','text'=>'Harga Tahu Bakso Original diperbarui', 'meta'=>'5 jam lalu · oleh Admin',  'badge'=>'Harga',  'bbg'=>'#fefce8','bcl'=>'#ca8a04'],
                    ['icon'=>'📦','bg'=>'#4ade80','text'=>'Stok Cabang Soekarno-Hatta diperbarui','meta'=>'1 hari lalu · oleh Admin', 'badge'=>'Stok',   'bbg'=>'#f0fdf4','bcl'=>'#16a34a'],
                    ['icon'=>'🏪','bg'=>'#c084fc','text'=>'Cabang Soekarno-Hatta ditambahkan',    'meta'=>'3 hari lalu · oleh Admin', 'badge'=>'Cabang', 'bbg'=>'#faf5ff','bcl'=>'#9333ea'],
                    ['icon'=>'👤','bg'=>'#60a5fa','text'=>'Karyawan Ahmad Fauzi absen masuk',     'meta'=>'Hari ini 07:02 WIB',       'badge'=>'Absen',  'bbg'=>'#eff6ff','bcl'=>'#2563eb'],
                ];
            @endphp
            <div style="display:flex;flex-direction:column;gap:.25rem;">
                @foreach ($activities as $act)
                    <div style="display:flex;align-items:center;gap:1rem;padding:.75rem;
                                border-radius:.75rem;cursor:pointer;transition:background .2s;"
                         onmouseenter="this.style.background='#FFF8E7'"
                         onmouseleave="this.style.background='transparent'">
                        <div style="width:2.75rem;height:2.75rem;border-radius:.75rem;
                                    background:{{ $act['bg'] }};display:flex;align-items:center;
                                    justify-content:center;font-size:1.125rem;flex-shrink:0;">
                            {{ $act['icon'] }}
                        </div>
                        <div style="flex:1;min-width:0;">
                            <p style="font-size:.875rem;font-weight:700;color:#374151;
                                      margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $act['text'] }}
                            </p>
                            <p style="font-size:.75rem;color:#9ca3af;margin:.125rem 0 0;">
                                {{ $act['meta'] }}
                            </p>
                        </div>
                        <span style="font-size:.75rem;font-weight:700;padding:.25rem .625rem;
                                     border-radius:99px;flex-shrink:0;
                                     background:{{ $act['bbg'] }};color:{{ $act['bcl'] }};">
                            {{ $act['badge'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Kolom Kanan --}}
        <div style="display:flex;flex-direction:column;gap:1.25rem;">

            {{-- Peringatan Stok --}}
            <div style="background:#fff;border-radius:1rem;padding:1.5rem;
                        border:1px solid #f3f4f6;box-shadow:0 1px 3px rgba(0,0,0,.06);">
                <h2 style="font-family:'Bebas Neue',sans-serif;font-size:1.5rem;
                           color:#C0271A;letter-spacing:.05em;margin:0 0 1rem;">
                    PERINGATAN STOK
                </h2>
                <div style="display:flex;flex-direction:column;gap:.75rem;">
                    @forelse ($stokMenipisDetail as $stok)
                        <div style="display:flex;align-items:center;gap:1rem;padding:1rem;
                                    background:#fef2f2;border:1px solid #fecaca;border-radius:.75rem;">
                            <div style="width:2.5rem;height:2.5rem;background:#fee2e2;border-radius:.75rem;
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:1.25rem;flex-shrink:0;">⚠️</div>
                            <div style="flex:1;min-width:0;">
                                <p style="font-size:.875rem;font-weight:700;color:#b91c1c;margin:0;">
                                    {{ $stok->pcsTahu->nama ?? '-' }} — {{ $stok->cabang->nama ?? '-' }}
                                </p>
                                <div style="display:flex;align-items:center;gap:.5rem;margin-top:.375rem;">
                                    <div style="flex:1;height:8px;background:#fecaca;border-radius:99px;overflow:hidden;">
                                        <div style="height:8px;background:#ef4444;border-radius:99px;
                                                    width:{{ min(100,$stok->jumlah) }}%;"></div>
                                    </div>
                                    <span style="font-size:.75rem;font-weight:700;color:#dc2626;flex-shrink:0;">
                                        {{ $stok->jumlah }} pcs
                                    </span>
                                </div>
                            </div>
                            <a href="{{ \Filament\Facades\Filament::getUrl() }}/stok-pcs"
                               style="background:#C0271A;color:#fff;font-size:.75rem;font-weight:700;
                                      padding:.375rem .75rem;border-radius:99px;flex-shrink:0;
                                      text-decoration:none;">
                                Tambah
                            </a>
                        </div>
                    @empty
                        <div style="display:flex;align-items:center;gap:.75rem;padding:1rem;
                                    background:#f0fdf4;border:1px solid #bbf7d0;border-radius:.75rem;">
                            <span style="font-size:1.25rem;">✅</span>
                            <p style="font-size:.875rem;font-weight:700;color:#15803d;margin:0;">
                                Semua stok aman
                            </p>
                        </div>
                    @endforelse

                    @foreach ($menuTanpaHargaDetail as $menu)
                        <div style="display:flex;align-items:center;gap:1rem;padding:1rem;
                                    background:#fefce8;border:1px solid #fef08a;border-radius:.75rem;">
                            <div style="width:2.5rem;height:2.5rem;background:#fef9c3;border-radius:.75rem;
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:1.25rem;flex-shrink:0;">💡</div>
                            <div style="flex:1;min-width:0;">
                                <p style="font-size:.875rem;font-weight:700;color:#a16207;margin:0;">
                                    {{ $menu->nama }} — belum ada harga
                                </p>
                                <p style="font-size:.75rem;color:#ca8a04;margin:.125rem 0 0;">
                                    Perlu dilengkapi data harga
                                </p>
                            </div>
                            <a href="{{ \Filament\Facades\Filament::getUrl() }}/hargas"
                               style="background:#F5C518;color:#9B1E13;font-size:.75rem;font-weight:700;
                                      padding:.375rem .75rem;border-radius:99px;flex-shrink:0;
                                      text-decoration:none;">
                                Isi Harga
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Akses Cepat --}}
            <div style="background:#fff;border-radius:1rem;padding:1.5rem;
                        border:1px solid #f3f4f6;box-shadow:0 1px 3px rgba(0,0,0,.06);">
                <h2 style="font-family:'Bebas Neue',sans-serif;font-size:1.5rem;
                           color:#C0271A;letter-spacing:.05em;margin:0 0 1rem;">
                    AKSES CEPAT
                </h2>
                @php
                    $quick = [
                        ['icon'=>'🍢','label'=>'Tambah Menu',  'href'=> \Filament\Facades\Filament::getUrl() . '/menus/create'],
                        ['icon'=>'📦','label'=>'Update Stok',  'href'=> \Filament\Facades\Filament::getUrl() . '/stok-pcs'],
                        ['icon'=>'💰','label'=>'Kelola Harga', 'href'=> \Filament\Facades\Filament::getUrl() . '/hargas'],
                        ['icon'=>'📸','label'=>'Absen Shift',  'href'=> \Filament\Facades\Filament::getUrl() . '/absen'],
                    ];
                @endphp
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
                    @foreach ($quick as $q)
                        <a href="{{ $q['href'] }}"
                           style="display:flex;align-items:center;gap:.75rem;padding:1rem;
                                  background:#FFF8E7;border-radius:.75rem;text-decoration:none;
                                  transition:background .2s;"
                           onmouseenter="this.style.background='#C0271A';this.querySelector('span').style.color='#fff'"
                           onmouseleave="this.style.background='#FFF8E7';this.querySelector('span').style.color='#374151'">
                            <span style="font-size:1.5rem;">{{ $q['icon'] }}</span>
                            <span style="font-size:.875rem;font-weight:700;color:#374151;transition:color .2s;">
                                {{ $q['label'] }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

</x-filament-panels::page>

</body>
</html>
