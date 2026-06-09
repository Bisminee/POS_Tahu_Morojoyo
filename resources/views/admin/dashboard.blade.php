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
                'value'    => $totalCabang ?? 0,
                'desc'     => 'Jumlah cabang terdaftar',
                'badge'    => 'Aktif',
                'icon'     => '🏪',
                'color'    => '#C0271A',
                'bar_bg'   => '#C0271A',
                'card_bg'  => '#FFF8E7',
                'badge_bg' => '#fef2f2',
                'badge_cl' => '#C0271A',
                'progress' => min(100, ($totalCabang ?? 0) * 20),
                'href'     => \Filament\Facades\Filament::getUrl() . '/cabangs',
            ],
            [
                'label'    => 'Total Menu',
                'value'    => $totalMenu ?? 0,
                'desc'     => 'Jumlah menu tersedia',
                'badge'    => 'Aktif',
                'icon'     => '🍢',
                'color'    => '#C0271A',
                'bar_bg'   => '#C0271A',
                'card_bg'  => '#FFF8E7',
                'badge_bg' => '#fef2f2',
                'badge_cl' => '#C0271A',
                'progress' => min(100, ($totalMenu ?? 0) * 20),
                'href'     => \Filament\Facades\Filament::getUrl() . '/menus',
            ],
            [
                'label'    => 'Total Karyawan',
                'value'    => $totalKaryawan ?? 0,
                'desc'     => 'Jumlah karyawan aktif',
                'badge'    => 'Aktif',
                'icon'     => '👥',
                'color'    => '#16a34a',
                'bar_bg'   => '#4ade80',
                'card_bg'  => '#f0fdf4',
                'badge_bg' => '#f0fdf4',
                'badge_cl' => '#16a34a',
                'progress' => min(100, ($totalKaryawan ?? 0) * 10),
                'href'     => \Filament\Facades\Filament::getUrl() . '/karyawans',
            ],
            [
                'label'    => 'Total Stok PCS',
                'value'    => $totalStok ?? 0,
                'desc'     => 'Akumulasi seluruh stok PCS',
                'badge'    => 'Stok',
                'icon'     => '📦',
                'color'    => '#2563eb',
                'bar_bg'   => '#60a5fa',
                'card_bg'  => '#eff6ff',
                'badge_bg' => '#eff6ff',
                'badge_cl' => '#2563eb',
                'progress' => min(100, (($totalStok ?? 0) / 500) * 100),
                'href'     => \Filament\Facades\Filament::getUrl() . '/stok-pcs',
            ],
            [
                'label'    => 'Menu Belum Punya Harga',
                'value'    => $menuTanpaHarga ?? 0,
                'desc'     => 'Perlu dilengkapi data harga',
                'badge'    => 'Perlu diisi',
                'icon'     => '💰',
                'color'    => '#ca8a04',
                'bar_bg'   => '#facc15',
                'card_bg'  => '#fefce8',
                'badge_bg' => '#fefce8',
                'badge_cl' => '#ca8a04',
                'progress' => ($totalMenu ?? 0) > 0 ? min(100, (($menuTanpaHarga ?? 0) / $totalMenu) * 100) : 0,
                'href'     => \Filament\Facades\Filament::getUrl() . '/hargas',
            ],
            [
                'label'    => 'Stok Menipis',
                'value'    => $stokMenipis ?? 0,
                'desc'     => 'Stok 10 pcs ke bawah',
                'badge'    => '⚠ Warning',
                'icon'     => '⚠️',
                'color'    => '#dc2626',
                'bar_bg'   => '#f87171',
                'card_bg'  => '#fef2f2',
                'badge_bg' => '#fef2f2',
                'badge_cl' => '#dc2626',
                'progress' => min(100, ($stokMenipis ?? 0) * 20),
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
            </div>

            @php
                $activities = [
                    ['icon'=>'🍢','bg'=>'#C0271A','text'=>'Menu baru ditambahkan', 'meta'=>'Aktivitas sistem', 'badge'=>'Menu', 'bbg'=>'#fef2f2','bcl'=>'#C0271A'],
                    ['icon'=>'💰','bg'=>'#F5C518','text'=>'Data harga diperbarui', 'meta'=>'Aktivitas sistem', 'badge'=>'Harga', 'bbg'=>'#fefce8','bcl'=>'#ca8a04'],
                    ['icon'=>'📦','bg'=>'#4ade80','text'=>'Stok cabang diperbarui', 'meta'=>'Aktivitas sistem', 'badge'=>'Stok', 'bbg'=>'#f0fdf4','bcl'=>'#16a34a'],
                    ['icon'=>'👤','bg'=>'#60a5fa','text'=>'Absensi karyawan tercatat', 'meta'=>'Cek rekap absensi', 'badge'=>'Absen', 'bbg'=>'#eff6ff','bcl'=>'#2563eb'],
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
                    @forelse (($stokMenipisDetail ?? collect()) as $stok)
                        <div style="display:flex;align-items:center;gap:1rem;padding:1rem;
                                    background:#fef2f2;border:1px solid #fecaca;border-radius:.75rem;">

                            <div style="width:2.5rem;height:2.5rem;background:#fee2e2;border-radius:.75rem;
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:1.25rem;flex-shrink:0;">
                                ⚠️
                            </div>

                            <div style="flex:1;min-width:0;">
                                <p style="font-size:.875rem;font-weight:700;color:#b91c1c;margin:0;">
                                    {{ $stok->pcsTahu->nama ?? '-' }} — {{ $stok->cabang->namaCabang ?? $stok->cabang->nama ?? '-' }}
                                </p>

                                <div style="display:flex;align-items:center;gap:.5rem;margin-top:.375rem;">
                                    <div style="flex:1;height:8px;background:#fecaca;border-radius:99px;overflow:hidden;">
                                        <div style="height:8px;background:#ef4444;border-radius:99px;
                                                    width:{{ min(100, $stok->jumlah ?? 0) }}%;"></div>
                                    </div>

                                    <span style="font-size:.75rem;font-weight:700;color:#dc2626;flex-shrink:0;">
                                        {{ $stok->jumlah ?? 0 }} pcs
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

                    @foreach (($menuTanpaHargaDetail ?? collect()) as $menu)
                        <div style="display:flex;align-items:center;gap:1rem;padding:1rem;
                                    background:#fefce8;border:1px solid #fef08a;border-radius:.75rem;">

                            <div style="width:2.5rem;height:2.5rem;background:#fef9c3;border-radius:.75rem;
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:1.25rem;flex-shrink:0;">
                                💡
                            </div>

                            <div style="flex:1;min-width:0;">
                                <p style="font-size:.875rem;font-weight:700;color:#a16207;margin:0;">
                                    {{ $menu->nama ?? '-' }} — belum ada harga
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

            {{-- Akses Cepat / Sidebar Fitur Owner --}}
            <div style="background:#fff;border-radius:1rem;padding:1.5rem;
                        border:1px solid #f3f4f6;box-shadow:0 1px 3px rgba(0,0,0,.06);">

                <h2 style="font-family:'Bebas Neue',sans-serif;font-size:1.5rem;
                           color:#C0271A;letter-spacing:.05em;margin:0 0 1rem;">
                    SIDEBAR FITUR OWNER
                </h2>

                @php
                    $quick = [
                        [
                            'icon' => '📸',
                            'label' => 'Face ID Karyawan',
                            'desc' => 'Daftar atau update wajah karyawan',
                            'href' => route('owner.karyawan.list'),
                            'bg' => '#fef2f2',
                            'color' => '#C0271A',
                        ],
                        [
                            'icon' => '📊',
                            'label' => 'Rekap Absensi',
                            'desc' => 'Lihat dan unduh absensi ke sheet',
                            'href' => route('attendance.owner'),
                            'bg' => '#eff6ff',
                            'color' => '#2563eb',
                        ],
                        [
                            'icon' => '🍢',
                            'label' => 'Tambah Menu',
                            'desc' => 'Tambah data menu baru',
                            'href' => \Filament\Facades\Filament::getUrl() . '/menus/create',
                            'bg' => '#FFF8E7',
                            'color' => '#C0271A',
                        ],
                        [
                            'icon' => '📦',
                            'label' => 'Update Stok',
                            'desc' => 'Kelola stok PCS tahu',
                            'href' => \Filament\Facades\Filament::getUrl() . '/stok-pcs',
                            'bg' => '#f0fdf4',
                            'color' => '#16a34a',
                        ],
                        [
                            'icon' => '💰',
                            'label' => 'Kelola Harga',
                            'desc' => 'Atur harga menu',
                            'href' => \Filament\Facades\Filament::getUrl() . '/hargas',
                            'bg' => '#fefce8',
                            'color' => '#ca8a04',
                        ],
                        [
                            'icon' => '👥',
                            'label' => 'Kelola Karyawan',
                            'desc' => 'Tambah atau edit data karyawan',
                            'href' => \Filament\Facades\Filament::getUrl() . '/karyawans',
                            'bg' => '#faf5ff',
                            'color' => '#9333ea',
                        ],
                    ];
                @endphp

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
                    @foreach ($quick as $q)
                        <a href="{{ $q['href'] }}"
                           style="display:flex;align-items:flex-start;gap:.75rem;padding:1rem;
                                  background:{{ $q['bg'] }};border-radius:.75rem;text-decoration:none;
                                  transition:transform .2s, box-shadow .2s;"
                           onmouseenter="this.style.transform='translateY(-3px)';this.style.boxShadow='0 10px 25px rgba(0,0,0,.08)'"
                           onmouseleave="this.style.transform='translateY(0)';this.style.boxShadow='none'">

                            <span style="font-size:1.5rem;line-height:1;">
                                {{ $q['icon'] }}
                            </span>

                            <span style="display:flex;flex-direction:column;gap:.125rem;">
                                <span style="font-size:.875rem;font-weight:800;color:{{ $q['color'] }};">
                                    {{ $q['label'] }}
                                </span>

                                <span style="font-size:.72rem;font-weight:600;color:#6b7280;line-height:1.25;">
                                    {{ $q['desc'] }}
                                </span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

</x-filament-panels::page>