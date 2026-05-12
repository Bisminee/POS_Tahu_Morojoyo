@props(['title' => 'POS Kasir'])

<x-layouts.app :title="$title">

    {{-- CDN Libraries --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f4f5f7;
        }

        .pos-wrap {
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 0;
            height: 100vh;
            max-height: 100vh;
            overflow: hidden;
        }

        .pos-left {
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            background: #f4f5f7;
        }

        .pos-right {
            border-left: 1px solid #e5e7eb;
            background: #fff;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            padding: 18px 20px;
            border: 1px solid #e9eaec;
        }

        .card-title {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 14px;
        }

        .topbar {
            background: #fff;
            border-radius: 16px;
            padding: 14px 20px;
            border: 1px solid #e9eaec;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .topbar h1 {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
        }

        .topbar p {
            font-size: 12px;
            color: #6b7280;
            margin-top: 2px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 12px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 500;
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .badge-green {
            background: #dcfce7;
            color: #15803d;
            border-color: #bbf7d0;
        }

        .btn-logout {
            padding: 7px 16px;
            border-radius: 99px;
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
        }

        .btn-logout:hover {
            background: #fee2e2;
        }

        .stat-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 16px 18px;
            border: 1px solid #e9eaec;
        }

        .stat-card .s-label {
            font-size: 11px;
            color: #9ca3af;
            font-weight: 500;
        }

        .stat-card .s-val {
            font-size: 22px;
            font-weight: 700;
            margin-top: 6px;
        }

        .s-emerald {
            color: #059669;
        }

        .s-indigo {
            color: #4f46e5;
        }

        .s-amber {
            color: #d97706;
        }

        .alert {
            border-radius: 14px;
            padding: 12px 16px;
            font-size: 13px;
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-warning {
            background: #fffbeb;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .pay-methods {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .pay-btn {
            padding: 8px 18px;
            border-radius: 99px;
            border: 1.5px solid #e5e7eb;
            background: #fff;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            color: #6b7280;
            transition: all .15s;
        }

        .pay-btn:hover {
            border-color: #a5b4fc;
            color: #4f46e5;
        }

        .pay-btn.active {
            background: #eef2ff;
            border-color: #6366f1;
            color: #4338ca;
            font-weight: 600;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(155px, 1fr));
            gap: 10px;
        }

        .menu-card {
            border-radius: 14px;
            border: 1.5px solid #e9eaec;
            background: #fafafa;
            padding: 14px;
            cursor: pointer;
            transition: all .18s;
            text-align: left;
            width: 100%;
        }

        .menu-card:hover {
            border-color: #6366f1;
            background: #eef2ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, .12);
        }

        .menu-card h3 {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
            line-height: 1.4;
        }

        .menu-card p {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 5px;
            line-height: 1.5;
        }

        .menu-price-tag {
            display: inline-block;
            margin-top: 10px;
            font-size: 14px;
            font-weight: 700;
            color: #059669;
        }

        .stock-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 8px;
        }

        .stk {
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 12px;
            border: 1px solid;
        }

        .stk h4 {
            font-weight: 600;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .stk-ok {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #166534;
        }

        .stk-warn {
            background: #fffbeb;
            border-color: #fde68a;
            color: #92400e;
        }

        .stk-low {
            background: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }

        .rp-header {
            padding: 14px 18px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cart-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 14px 16px;
        }

        .cart-empty-box {
            border: 1.5px dashed #d1d5db;
            border-radius: 14px;
            padding: 32px 16px;
            text-align: center;
            color: #9ca3af;
            font-size: 13px;
        }

        .cart-item-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 9px 0;
            border-bottom: 1px solid #f3f4f6;
            gap: 8px;
        }

        .ci-name {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
        }

        .ci-qty {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 2px;
        }

        .ci-price {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
            text-align: right;
        }

        .ci-remove {
            font-size: 10px;
            font-weight: 600;
            color: #dc2626;
            cursor: pointer;
            margin-top: 4px;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .ci-remove:hover {
            color: #991b1b;
        }

        .custom-badge {
            display: inline-block;
            background: #eef2ff;
            color: #4338ca;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            padding: 1px 7px;
            border-radius: 99px;
            margin-left: 5px;
            vertical-align: middle;
        }

        .custom-section {
            padding: 12px 16px;
            border-top: 1px solid #f0f0f0;
            background: #fafafa;
        }

        .custom-section .ct {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 8px;
        }

        .input-sm {
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 7px 10px;
            font-size: 13px;
            font-family: inherit;
            color: #111827;
            background: #fff;
            outline: none;
            transition: border-color .15s;
        }

        .input-sm:focus {
            border-color: #6366f1;
        }

        .input-sm::placeholder {
            color: #d1d5db;
        }

        .custom-row {
            display: flex;
            gap: 6px;
            margin-top: 6px;
        }

        .custom-row .input-sm {
            flex: 1;
        }

        .btn-add-custom {
            background: #4f46e5;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 7px 14px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: background .15s;
        }

        .btn-add-custom:hover {
            background: #4338ca;
        }

        .cart-summary {
            padding: 12px 16px;
            border-top: 1px solid #f0f0f0;
            background: #fafafa;
            font-size: 13px;
        }

        .disc-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }

        .disc-row label {
            font-size: 12px;
            color: #9ca3af;
            white-space: nowrap;
        }

        .sum-line {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            color: #6b7280;
        }

        .sum-line.big {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
            padding-top: 8px;
            margin-top: 4px;
            border-top: 1px solid #e5e7eb;
        }

        .money-section {
            padding: 10px 16px;
            border-top: 1px solid #f0f0f0;
        }

        .money-section label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #9ca3af;
            display: block;
            margin-bottom: 5px;
        }

        .change-row {
            display: flex;
            justify-content: space-between;
            margin-top: 6px;
            font-size: 13px;
            color: #6b7280;
        }

        .change-amt {
            font-weight: 700;
            color: #059669;
        }

        .checkout-bar {
            padding: 12px 16px;
            border-top: 1px solid #f0f0f0;
            background: #fff;
        }

        .btn-checkout {
            width: 100%;
            background: #059669;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 13px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: inherit;
            transition: background .15s, transform .1s;
        }

        .btn-checkout:hover {
            background: #047857;
        }

        .btn-checkout:active {
            transform: scale(.98);
        }

        .btn-checkout:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }

        .btn-checkout svg {
            width: 18px;
            height: 18px;
        }

        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .5);
            z-index: 200;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            backdrop-filter: blur(3px);
            animation: fadeIn .15s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(18px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal {
            background: #fff;
            border-radius: 20px;
            padding: 24px;
            width: 100%;
            max-width: 520px;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideUp .2s ease;
            border: 1px solid #e9eaec;
        }

        .modal-wide {
            max-width: 620px;
        }

        .modal-sm {
            max-width: 420px;
        }

        .modal-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
        }

        .modal-head h2 {
            font-size: 17px;
            font-weight: 700;
            color: #111827;
        }

        .modal-head p {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 3px;
        }

        .modal-close {
            flex-shrink: 0;
            width: 28px;
            height: 28px;
            border-radius: 99px;
            background: #f3f4f6;
            border: none;
            cursor: pointer;
            font-size: 14px;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .15s;
        }

        .modal-close:hover {
            background: #e5e7eb;
        }

        .modal-section {
            margin-bottom: 16px;
        }

        .modal-section-title {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 10px;
        }

        .modal-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .mstk {
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 12px;
            border: 1px solid;
            margin-bottom: 7px;
        }

        .mstk h5 {
            font-weight: 600;
            margin-bottom: 2px;
        }

        .mstk-ok {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #166534;
        }

        .mstk-warn {
            background: #fffbeb;
            border-color: #fde68a;
            color: #92400e;
        }

        .mstk-low {
            background: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }

        .order-box {
            background: #f8fafc;
            border-radius: 14px;
            padding: 16px;
            border: 1px solid #e9eaec;
        }

        .price-display {
            font-size: 22px;
            font-weight: 700;
            color: #059669;
            margin: 8px 0 14px;
        }

        .qty-control {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background: #fff;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #374151;
            line-height: 1;
            transition: background .1s;
        }

        .qty-btn:hover {
            background: #f3f4f6;
        }

        .qty-val {
            font-size: 18px;
            font-weight: 700;
            min-width: 32px;
            text-align: center;
        }

        .btn-add-cart {
            width: 100%;
            background: #4f46e5;
            color: #fff;
            border: none;
            border-radius: 11px;
            padding: 12px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 14px;
            font-family: inherit;
            transition: background .15s;
        }

        .btn-add-cart:hover {
            background: #4338ca;
        }

        .confirm-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .confirm-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 7px 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 13px;
        }

        .confirm-list li .cn {
            font-weight: 600;
            color: #111827;
        }

        .confirm-list li .cq {
            font-size: 11px;
            color: #9ca3af;
        }

        .confirm-list li .cs {
            font-weight: 600;
        }

        .confirm-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 16px;
            font-weight: 700;
            padding-top: 10px;
            margin-top: 6px;
            border-top: 2px solid #111827;
            color: #111827;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-modal-cancel {
            flex: 1;
            padding: 11px;
            border-radius: 11px;
            border: 1.5px solid #e5e7eb;
            background: #fff;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            color: #374151;
            font-family: inherit;
            transition: background .15s;
        }

        .btn-modal-cancel:hover {
            background: #f3f4f6;
        }

        .btn-modal-confirm {
            flex: 2;
            padding: 11px;
            border-radius: 11px;
            border: none;
            background: #059669;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: background .15s;
        }

        .btn-modal-confirm:hover {
            background: #047857;
        }

        .btn-modal-confirm:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }

        .receipt-paper {
            background: #fff;
            border: 1px dashed #d1d5db;
            border-radius: 14px;
            padding: 20px;
            font-size: 13px;
        }

        .receipt-brand {
            text-align: center;
            margin-bottom: 14px;
            padding-bottom: 12px;
            border-bottom: 1px dashed #d1d5db;
        }

        .receipt-brand h3 {
            font-size: 16px;
            font-weight: 700;
        }

        .receipt-brand p {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 3px;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
        }

        .receipt-row.big {
            font-size: 15px;
            font-weight: 700;
            border-top: 1.5px solid #e5e7eb;
            margin-top: 6px;
            padding-top: 8px;
        }

        .receipt-divider {
            border: none;
            border-top: 1px dashed #d1d5db;
            margin: 8px 0;
        }

        .change-box {
            margin-top: 12px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            font-weight: 700;
            color: #166534;
            font-size: 14px;
        }

        .receipt-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-top: 12px;
        }

        .btn-receipt-action {
            padding: 10px;
            border-radius: 11px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            transition: background .15s;
            text-align: center;
            border: 1.5px solid;
        }

        .btn-print-browser {
            background: #fff;
            border-color: #e5e7eb;
            color: #374151;
        }

        .btn-print-browser:hover {
            background: #f3f4f6;
        }

        .btn-download-pdf {
            background: #eef2ff;
            border-color: #a5b4fc;
            color: #4338ca;
        }

        .btn-download-pdf:hover {
            background: #e0e7ff;
        }

        .btn-laporan-pdf {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #dc2626;
        }

        .btn-laporan-pdf:hover {
            background: #fee2e2;
        }

        .btn-laporan-excel {
            background: #f0fdf4;
            border-color: #86efac;
            color: #16a34a;
        }

        .btn-laporan-excel:hover {
            background: #dcfce7;
        }

        .btn-new-trx {
            width: 100%;
            margin-top: 8px;
            padding: 11px;
            border-radius: 11px;
            border: 1.5px solid #bbf7d0;
            background: #ecfdf5;
            color: #047857;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: background .15s;
        }

        .btn-new-trx:hover {
            background: #d1fae5;
        }

        .laporan-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #9ca3af;
            text-align: center;
            margin-top: 10px;
            margin-bottom: 6px;
            padding-top: 10px;
            border-top: 1px dashed #e5e7eb;
        }

        /* Loading spinner */
        .spinner {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, .4);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media print {

            .pos-wrap,
            .pos-left,
            .pos-right {
                display: none !important;
            }

            .overlay {
                display: block !important;
                position: static !important;
                background: none !important;
            }

            .modal {
                box-shadow: none !important;
                border: none !important;
                max-height: none !important;
            }

            .modal-close,
            .receipt-actions,
            .laporan-label,
            .btn-new-trx,
            .modal-actions {
                display: none !important;
            }
        }

        @media (max-width: 768px) {
            .pos-wrap {
                grid-template-columns: 1fr;
                grid-template-rows: 1fr auto;
                max-height: none;
                height: auto;
            }

            .stat-row {
                grid-template-columns: 1fr 1fr;
            }

            .modal-2col {
                grid-template-columns: 1fr;
            }

            .receipt-actions {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>

    <div class="pos-wrap">

        {{-- ══ LEFT PANEL ══ --}}
        <div class="pos-left">

            {{-- Topbar --}}
            <div class="topbar">
                <div>
                    <h1>POS Kasir</h1>
                    <p>Sistem penjualan harian, inventori stok, dan checkout.</p>
                </div>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                    <span class="badge">{{ auth()->user()->name }}</span>
                    <span class="badge badge-green">{{ auth()->user()->role }}</span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-logout">Keluar</button>
                    </form>
                </div>
            </div>

            {{-- Alerts --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Ada masalah:</strong>
                    <ul style="margin-top:6px;padding-left:18px">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success" id="alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-warning" id="alert-error">{{ session('error') }}</div>
            @endif
            <div class="alert alert-danger" id="ajax-error" style="display:none"></div>

            {{-- Stats --}}
            <div class="stat-row">
                <div class="stat-card">
                    <div class="s-label">Total Penjualan Hari Ini</div>
                    <div class="s-val s-emerald" id="stat-sales">Rp{{ number_format($todaySales ?? 0, 0, ',', '.') }}
                    </div>
                </div>
                <div class="stat-card">
                    <div class="s-label">Jumlah Transaksi</div>
                    <div class="s-val s-indigo" id="stat-trx">{{ $todayTransactions ?? 0 }}</div>
                </div>
                <div class="stat-card">
                    <div class="s-label">Menu Terjual</div>
                    <div class="s-val s-amber" id="stat-items">{{ $todayItems ?? 0 }}</div>
                </div>
            </div>

            {{-- Metode pembayaran --}}
            <div class="card">
                <div class="card-title">Metode Pembayaran</div>
                <div class="pay-methods" id="pay-methods">
                    @foreach ($paymentMethods as $index => $method)
                        <button class="pay-btn {{ $index === 0 ? 'active' : '' }}"
                            onclick="setPayment(this, '{{ strtolower(str_replace(' ', '', $method)) }}')"
                            data-method="{{ strtolower(str_replace(' ', '', $method)) }}">{{ $method }}</button>
                    @endforeach
                </div>
            </div>

            {{-- Menu cards --}}
            <div class="card">
                <div class="card-title">Pilih Menu</div>
                <div class="menu-grid">
                    @foreach ($menus as $menu)
                        <button type="button" class="menu-card" onclick="openMenuModal({{ $menu->idMenu }})">
                            <h3>{{ $menu->namaMenu }}</h3>
                            <p>{{ $menu->deskripsi ?: 'Tidak ada deskripsi.' }}</p>
                            <span class="menu-price-tag" data-menu-id="{{ $menu->idMenu }}">—</span>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Stok inventori --}}
            <div class="card">
                <div class="card-title">Stok Inventori</div>

                <div class="stock-grid" id="stock-grid">
                    @foreach ($stocks as $s)
                        @php
                            $stok = $s->jumlah_stok;
                        @endphp

                        <div class="stk {{ $stok <= 5 ? 'stk-low' : ($stok <= 15 ? 'stk-warn' : 'stk-ok') }}">
                            <h4>{{ $s->pcsTahu?->nama_pcs ?? '—' }}</h4>

                            <span>
                                {{ $stok }} pcs

                                @if ($stok <= 5)
                                    ⚠ hampir habis
                                @elseif ($stok <= 15)
                                    sisa sedikit
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>{{-- /pos-left --}}

        {{-- ══ RIGHT PANEL — CART ══ --}}
        <div class="pos-right">
            <div class="rp-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1" />
                    <circle cx="20" cy="21" r="1" />
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                </svg>
                Keranjang
            </div>

            <div class="cart-scroll" id="cart-scroll">
                <div class="cart-empty-box" id="cart-empty">
                    Keranjang masih kosong.<br>
                    <span style="font-size:11px;color:#d1d5db">Klik kartu menu untuk menambahkan.</span>
                </div>
                <div id="cart-items-container"></div>
            </div>

            {{-- Custom menu --}}
            <div class="custom-section">
                <div class="ct">Custom Menu</div>
                <input id="custom-name" class="input-sm" type="text" placeholder="Nama menu custom...">
                <div class="custom-row">
                    <input id="custom-price" class="input-sm" type="number" min="0" placeholder="Harga (Rp)">
                    <input id="custom-qty" class="input-sm" type="number" min="1" value="1"
                        style="max-width:64px">
                    <button class="btn-add-custom" onclick="addCustomMenu()">+ Tambah</button>
                </div>
            </div>

            {{-- Summary --}}
            <div class="cart-summary" id="cart-summary" style="display:none">
                <div class="disc-row">
                    <label for="discount">Diskon (Rp)</label>
                    <input id="discount" class="input-sm" type="number" min="0" value="0"
                        oninput="renderCart()">
                </div>
                <div class="sum-line"><span>Total item</span><span id="sum-items">0</span></div>
                <div class="sum-line"><span>Subtotal</span><span id="sum-sub">Rp0</span></div>
                <div class="sum-line"><span>Diskon</span><span id="sum-disc">Rp0</span></div>
                <div class="sum-line big"><span>Total</span><span id="sum-total">Rp0</span></div>
            </div>

            {{-- Uang dibayar --}}
            <div class="money-section" id="money-section" style="display:none">
                <label for="money-paid">Uang Dibayar</label>
                <input id="money-paid" class="input-sm" type="number" min="0" value="0"
                    oninput="calcChange()">
                <div class="change-row">
                    <span>Kembalian</span>
                    <span id="change-display" class="change-amt">Rp0</span>
                </div>
            </div>

            {{-- Checkout button --}}
            <div class="checkout-bar">
                <button class="btn-checkout" id="btn-checkout" onclick="openCheckoutModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 7.5M7 13l-4-8m4 8h10m0 0l1.5 7.5M17 13v0" />
                    </svg>
                    Checkout
                </button>
            </div>
        </div>{{-- /pos-right --}}

    </div>{{-- /pos-wrap --}}

    {{-- ═══════════════════════════════════════════════════════════
     MODAL 1 — PILIH MENU
═══════════════════════════════════════════════════════════ --}}
    <div class="overlay" id="modal-menu" style="display:none" onclick="closeModalOutside(event,'modal-menu')">
        <div class="modal modal-wide">
            <div class="modal-head">
                <div>
                    <h2 id="mm-title">Nama Menu</h2>
                    <p id="mm-desc">Deskripsi menu</p>
                </div>
                <button class="modal-close" onclick="closeModal('modal-menu')">✕</button>
            </div>
            <div class="modal-2col">
                <div class="modal-section">
                    <div class="modal-section-title">Stok bahan inventori</div>
                    <div id="mm-stocks"></div>
                </div>
                <div class="modal-section">
                    <div class="modal-section-title">Detail pesanan</div>
                    <div class="order-box">
                        <div style="font-size:12px;color:#9ca3af">Harga saat ini</div>
                        <div class="price-display" id="mm-price">Rp0</div>
                        <div style="font-size:12px;color:#9ca3af;margin-bottom:8px">Jumlah pesanan</div>
                        <div class="qty-control">
                            <button class="qty-btn" onclick="adjModalQty(-1)">−</button>
                            <span class="qty-val" id="mm-qty">1</span>
                            <button class="qty-btn" onclick="adjModalQty(1)">+</button>
                        </div>
                        <button class="btn-add-cart" onclick="addToCartFromModal()">Tambah ke Keranjang</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
     MODAL 2 — KONFIRMASI CHECKOUT
═══════════════════════════════════════════════════════════ --}}
    <div class="overlay" id="modal-checkout" style="display:none"
        onclick="closeModalOutside(event,'modal-checkout')">
        <div class="modal modal-wide">
            <div class="modal-head">
                <div>
                    <h2>Konfirmasi Checkout</h2>
                    <p>Periksa pesanan & stok sebelum menyimpan transaksi.</p>
                </div>
                <button class="modal-close" onclick="closeModal('modal-checkout')">✕</button>
            </div>

            <div class="modal-section">
                <div class="modal-section-title">Stok inventori yang akan dikurangi</div>
                <div id="co-stocks"></div>
            </div>

            <div class="modal-section">
                <div class="modal-section-title">Pesanan</div>
                <ul class="confirm-list" id="co-items"></ul>
                <div class="confirm-total-row">
                    <span>Total</span>
                    <span id="co-total" style="color:#059669"></span>
                </div>
            </div>

            <div
                style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:14px;padding:14px;background:#f8fafc;border-radius:12px;border:1px solid #e9eaec;font-size:13px">
                <div>
                    <div style="color:#9ca3af;font-size:11px;text-transform:uppercase;letter-spacing:.08em">Metode
                    </div>
                    <div style="font-weight:700;margin-top:4px" id="co-method">—</div>
                </div>
                <div>
                    <div style="color:#9ca3af;font-size:11px;text-transform:uppercase;letter-spacing:.08em">Kembalian
                    </div>
                    <div style="font-weight:700;color:#059669;margin-top:4px" id="co-change">Rp0</div>
                </div>
            </div>

            <div class="modal-actions">
                <button class="btn-modal-cancel" id="btn-cancel-checkout"
                    onclick="closeModal('modal-checkout')">Batal</button>
                <button class="btn-modal-confirm" id="btn-confirm-checkout" onclick="saveTransaction()">✓ Ya, Simpan
                    Transaksi</button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
     MODAL 3 — STRUK
═══════════════════════════════════════════════════════════ --}}
    <div class="overlay" id="modal-receipt" style="display:none">
        <div class="modal modal-sm">
            <div class="modal-head">
                <div>
                    <h2>🧾 Struk Transaksi</h2>
                    <p>Tunjukkan ke customer atau cetak struk.</p>
                </div>
            </div>

            <div class="receipt-paper" id="receipt-content">
                <div class="receipt-brand">
                    <h3>Warung Tahu Bakso</h3>
                    <p id="rc-datetime">—</p>
                    <p id="rc-trxno">No. Transaksi: —</p>
                    <p id="rc-method" style="font-weight:600">—</p>
                </div>
                <div id="rc-items"></div>
                <hr class="receipt-divider">
                <div class="receipt-row"><span>Subtotal</span><span id="rc-sub">Rp0</span></div>
                <div class="receipt-row"><span>Diskon</span><span id="rc-disc">Rp0</span></div>
                <div class="receipt-row big"><span>Total</span><span id="rc-total">Rp0</span></div>
                <hr class="receipt-divider">
                <div class="receipt-row"><span>Dibayar</span><span id="rc-paid">Rp0</span></div>
                <div class="change-box" id="rc-change">Kembalian: Rp0</div>
                <p style="text-align:center;font-size:11px;color:#9ca3af;margin-top:12px">Terima kasih sudah
                    berbelanja! 🙏</p>
            </div>

            <div class="receipt-actions">
                <button class="btn-receipt-action btn-print-browser" onclick="window.print()">🖨 Print</button>
                <button class="btn-receipt-action btn-download-pdf" onclick="downloadStrukPdf()">⬇ PDF Struk</button>
            </div>

            <div class="laporan-label">Laporan Hari Ini</div>
            <div class="receipt-actions">
                <button class="btn-receipt-action btn-laporan-pdf" onclick="downloadLaporanPdf()">📄 Laporan
                    PDF</button>
                <button class="btn-receipt-action btn-laporan-excel" onclick="downloadLaporanExcel()">📊 Laporan
                    Excel</button>
            </div>

            <button class="btn-new-trx" onclick="resetAndClose()">✓ Selesai & Transaksi Baru</button>
        </div>
    </div>

    {{-- CSRF token untuk AJAX --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        /* ════════════════════════════════════════════
                           DATA DARI SERVER
                        ════════════════════════════════════════════ */
        @php
            $menuData = $menus
                ->map(function ($menu) use ($stocks) {
                    return [
                        'id' => $menu->idMenu,
                        'name' => $menu->namaMenu,
                        'desc' => $menu->deskripsi,
                        'prices' => [
                            'normal' => (float) (optional($menu->hargas->first())->harga_normal ?? 0),
                            'gofood' => (float) (optional($menu->hargas->first())->harga_gofood ?? 0),
                            'shopeefood' => (float) (optional($menu->hargas->first())->harga_shopeefood ?? 0),
                        ],
                        'details' => $menu->menuDetails
                            ->map(function ($d) use ($stocks) {
                                // ✅ FIX: pakai ->get() bukan [] untuk hindari "Undefined array key"
                                $stokGroup = $stocks->get($d->id_pcs);
                                return [
                                    'pcs_id' => $d->id_pcs,
                                    'pcs_name' => $d->pcsTahu?->nama_pcs ?? 'Bahan tidak dikenal',
                                    'qty' => (int) $d->jumlah_pcs,
                                    'stock' => (int) (optional($stokGroup?->first())->jumlah_stok ?? 0),
                                ];
                            })
                            ->values()
                            ->toArray(),
                    ];
                })
                ->values()
                ->toArray();

            $todayTrxFull = \App\Models\Transaction::with('items')
                ->whereDate('created_at', today())
                ->get()
                ->map(
                    fn($t) => [
                        'id' => $t->id,
                        'created_at' => $t->created_at->format('d M Y H:i'),
                        'payment_method' => $t->payment_method,
                        'kasir' => optional(auth()->user())->name ?? '—',
                        'sub_total' => (int) $t->sub_total,
                        'discount' => (int) $t->discount,
                        'total' => (int) $t->total,
                        'money_paid' => (int) ($t->money_paid ?? $t->total),
                        'items' => $t->items
                            ->map(
                                fn($i) => [
                                    'nama_item' => $i->nama_item,
                                    'qty' => (int) $i->qty,
                                    'unit_price' => (int) $i->unit_price,
                                    'subtotal' => (int) $i->subtotal,
                                ],
                            )
                            ->toArray(),
                    ],
                )
                ->values()
                ->toArray();
        @endphp

        const MENU_DATA = @json($menuData);
        const KASIR_NAME = @json(auth()->user()->name ?? '—');
        const TANGGAL_HARI = @json(now()->translatedFormat('d F Y'));
        const CHECKOUT_URL = "{{ route('cashier.pos.checkout') }}";
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

        let todayTrxList = @json($todayTrxFull);

        /* ════════════════════════════════════════════
           STATE
        ════════════════════════════════════════════ */
        let cart = [];
        let activeMenu = null;
        let modalQty = 1;
        let currentPay = '{{ strtolower(str_replace(' ', '', $paymentMethods[0] ?? 'normal')) }}';
        let lastTrxSnapshot = null;

        /* ════════════════════════════════════════════
           CONSTANTS
        ════════════════════════════════════════════ */
        const BRAND = 'Warung Tahu Bakso';
        const ADDRESS = 'Jl. Contoh No.1, Malang';
        const METHOD_LABELS = {
            normal: 'Normal (Tunai / Transfer)',
            gofood: 'GoFood',
            shopeefood: 'ShopeeFood',
        };

        /* ════════════════════════════════════════════
           HELPERS
        ════════════════════════════════════════════ */
        function fmt(n) {
            return 'Rp' + Number(n || 0).toLocaleString('id-ID', {
                maximumFractionDigits: 0
            });
        }

        function mStkClass(s) {
            return s <= 5 ? 'mstk mstk-low' : s <= 15 ? 'mstk mstk-warn' : 'mstk mstk-ok';
        }

        function getPrice(menu) {
            return Number(menu.prices[currentPay]) || Number(Object.values(menu.prices)[0]) || 0;
        }

        function nowStr() {
            const d = new Date();
            return d.toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                }) +
                ' ' + d.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
        }

        function trxNo(id) {
            return 'TRX-' + String(id || 0).padStart(5, '0');
        }

        /* ════════════════════════════════════════════
           STATS UPDATE (tanpa reload halaman)
        ════════════════════════════════════════════ */
        function updateStats() {
            const totalSales = todayTrxList.reduce((s, t) => s + (t.total || 0), 0);
            const totalTrx = todayTrxList.length;
            const totalItems = todayTrxList.reduce((s, t) =>
                s + (t.items || []).reduce((si, i) => si + (i.qty || 0), 0), 0);

            document.getElementById('stat-sales').textContent = fmt(totalSales);
            document.getElementById('stat-trx').textContent = totalTrx;
            document.getElementById('stat-items').textContent = totalItems;
        }

        /* ════════════════════════════════════════════
           PAYMENT METHOD
        ════════════════════════════════════════════ */
        function setPayment(btn, method) {
            document.querySelectorAll('.pay-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentPay = method;
            cart = cart.map(item => {
                if (item.custom) return item;
                const m = MENU_DATA.find(x => x.id === item.menuId);
                if (!m) return item;
                const up = getPrice(m);
                return {
                    ...item,
                    unitPrice: up,
                    subtotal: up * item.qty
                };
            });
            renderMenuPrices();
            renderCart();
        }

        function renderMenuPrices() {
            document.querySelectorAll('.menu-price-tag').forEach(el => {
                const id = Number(el.dataset.menuId);
                const m = MENU_DATA.find(x => x.id === id);
                el.textContent = m ? fmt(getPrice(m)) : '—';
            });
        }

        /* ════════════════════════════════════════════
           MODAL HELPERS
        ════════════════════════════════════════════ */
        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        function closeModalOutside(event, id) {
            if (event.target.id === id) closeModal(id);
        }

        /* ════════════════════════════════════════════
           MODAL 1 — PILIH MENU
        ════════════════════════════════════════════ */
        function openMenuModal(menuId) {
            activeMenu = MENU_DATA.find(m => m.id === menuId);
            if (!activeMenu) return;
            modalQty = 1;

            document.getElementById('mm-title').textContent = activeMenu.name;
            document.getElementById('mm-desc').textContent = activeMenu.desc || 'Tidak ada deskripsi.';
            document.getElementById('mm-price').textContent = fmt(getPrice(activeMenu));
            document.getElementById('mm-qty').textContent = '1';

            const stocksEl = document.getElementById('mm-stocks');
            stocksEl.innerHTML = '';
            if (!activeMenu.details.length) {
                stocksEl.innerHTML = '<p style="color:#9ca3af;font-size:12px">Tidak ada data bahan terdaftar.</p>';
            } else {
                activeMenu.details.forEach(d => {
                    stocksEl.innerHTML += `
                <div class="${mStkClass(d.stock)}">
                    <h5>${d.pcs_name}</h5>
                    <span>Per porsi: ${d.qty} pcs &nbsp;·&nbsp; Sisa stok: <strong>${d.stock} pcs</strong>${d.stock <= 5 ? ' ⚠' : ''}</span>
                </div>`;
                });
            }
            openModal('modal-menu');
        }

        function adjModalQty(delta) {
            modalQty = Math.max(1, modalQty + delta);
            document.getElementById('mm-qty').textContent = modalQty;
        }

        function addToCartFromModal() {
            if (!activeMenu) return;
            const up = getPrice(activeMenu);
            const ex = cart.find(i => i.menuId === activeMenu.id && !i.custom);
            if (ex) {
                ex.qty += modalQty;
                ex.subtotal = ex.unitPrice * ex.qty;
            } else {
                cart.push({
                    menuId: activeMenu.id,
                    name: activeMenu.name,
                    qty: modalQty,
                    unitPrice: up,
                    subtotal: up * modalQty,
                    custom: false,
                    details: activeMenu.details,
                });
            }
            closeModal('modal-menu');
            renderCart();
        }

        /* ════════════════════════════════════════════
           CUSTOM MENU
        ════════════════════════════════════════════ */
        function addCustomMenu() {
            const name = document.getElementById('custom-name').value.trim();
            const price = parseFloat(document.getElementById('custom-price').value) || 0;
            const qty = Math.max(1, parseInt(document.getElementById('custom-qty').value) || 1);
            if (!name) {
                alert('Masukkan nama custom menu.');
                return;
            }
            if (price <= 0) {
                alert('Masukkan harga yang valid.');
                return;
            }
            cart.push({
                menuId: null,
                name,
                qty,
                unitPrice: price,
                subtotal: price * qty,
                custom: true,
                details: []
            });
            document.getElementById('custom-name').value = '';
            document.getElementById('custom-price').value = '';
            document.getElementById('custom-qty').value = 1;
            renderCart();
        }

        function removeCartItem(index) {
            cart.splice(index, 1);
            renderCart();
        }

        /* ════════════════════════════════════════════
           RENDER CART
        ════════════════════════════════════════════ */
        function renderCart() {
            const container = document.getElementById('cart-items-container');
            const empty = document.getElementById('cart-empty');
            const summary = document.getElementById('cart-summary');
            const moneyEl = document.getElementById('money-section');

            container.innerHTML = '';

            if (!cart.length) {
                empty.style.display = '';
                summary.style.display = 'none';
                moneyEl.style.display = 'none';
                return;
            }

            empty.style.display = 'none';
            summary.style.display = '';
            moneyEl.style.display = '';

            cart.forEach((item, i) => {
                const div = document.createElement('div');
                div.className = 'cart-item-row';
                div.innerHTML = `
            <div>
                <div class="ci-name">
                    ${item.name}
                    ${item.custom ? '<span class="custom-badge">custom</span>' : ''}
                </div>
                <div class="ci-qty">x${item.qty} &times; ${fmt(item.unitPrice)}</div>
            </div>
            <div style="text-align:right">
                <div class="ci-price">${fmt(item.subtotal)}</div>
                <div class="ci-remove" onclick="removeCartItem(${i})">✕ hapus</div>
            </div>`;
                container.appendChild(div);
            });

            const disc = Math.max(0, parseFloat(document.getElementById('discount').value) || 0);
            const sub = cart.reduce((s, i) => s + i.subtotal, 0);
            const total = Math.max(0, sub - disc);
            const items = cart.reduce((s, i) => s + i.qty, 0);

            document.getElementById('sum-items').textContent = items;
            document.getElementById('sum-sub').textContent = fmt(sub);
            document.getElementById('sum-disc').textContent = fmt(disc);
            document.getElementById('sum-total').textContent = fmt(total);

            calcChange();
        }

        function calcChange() {
            const disc = Math.max(0, parseFloat(document.getElementById('discount').value) || 0);
            const sub = cart.reduce((s, i) => s + i.subtotal, 0);
            const total = Math.max(0, sub - disc);
            const paid = parseFloat(document.getElementById('money-paid').value) || 0;
            document.getElementById('change-display').textContent = fmt(Math.max(0, paid - total));
        }

        /* ════════════════════════════════════════════
           MODAL 2 — KONFIRMASI CHECKOUT
        ════════════════════════════════════════════ */
        function openCheckoutModal() {
            if (!cart.length) {
                alert('Keranjang masih kosong. Tambahkan menu terlebih dahulu.');
                return;
            }

            const disc = Math.max(0, parseFloat(document.getElementById('discount').value) || 0);
            const sub = cart.reduce((s, i) => s + i.subtotal, 0);
            const total = Math.max(0, sub - disc);
            const paid = parseFloat(document.getElementById('money-paid').value) || 0;
            const change = Math.max(0, paid - total);

            const stockImpact = {};
            cart.filter(i => !i.custom).forEach(item => {
                item.details.forEach(d => {
                    if (!stockImpact[d.pcs_name]) stockImpact[d.pcs_name] = {
                        stock: d.stock,
                        used: 0
                    };
                    stockImpact[d.pcs_name].used += d.qty * item.qty;
                });
            });

            const coStocks = document.getElementById('co-stocks');
            coStocks.innerHTML = '';
            if (!Object.keys(stockImpact).length) {
                coStocks.innerHTML =
                    '<p style="color:#9ca3af;font-size:12px">Tidak ada bahan inventori yang terpengaruh (hanya custom menu).</p>';
            } else {
                Object.entries(stockImpact).forEach(([name, v]) => {
                    const sisa = v.stock - v.used;
                    coStocks.innerHTML += `
                <div class="${mStkClass(sisa)}" style="margin-bottom:6px">
                    <h5>${name}</h5>
                    <span>Sisa saat ini: ${v.stock} pcs &nbsp;→&nbsp; dikurangi ${v.used} pcs &nbsp;→&nbsp; sisa <strong>${sisa} pcs</strong>${sisa <= 5 ? ' ⚠' : ''}</span>
                </div>`;
                });
            }

            document.getElementById('co-items').innerHTML = cart.map(item => `
        <li>
            <div><div class="cn">${item.name}</div><div class="cq">x${item.qty}</div></div>
            <div class="cs">${fmt(item.subtotal)}</div>
        </li>`).join('');

            document.getElementById('co-total').textContent = fmt(total);
            document.getElementById('co-change').textContent = fmt(change);
            document.getElementById('co-method').textContent = METHOD_LABELS[currentPay] ?? currentPay;

            openModal('modal-checkout');
        }

        /* ════════════════════════════════════════════
           SIMPAN TRANSAKSI — AJAX (bukan form submit)
           Supaya struk bisa tampil tanpa redirect halaman
        ════════════════════════════════════════════ */
        async function saveTransaction() {
            const btnConfirm = document.getElementById('btn-confirm-checkout');
            const btnCancel = document.getElementById('btn-cancel-checkout');

            btnConfirm.disabled = true;
            btnConfirm.innerHTML = '<span class="spinner"></span> Menyimpan...';
            btnCancel.disabled = true;

            const disc = Math.max(0, parseFloat(document.getElementById('discount').value) || 0);
            const sub = cart.reduce((s, i) => s + i.subtotal, 0);
            const total = Math.max(0, sub - disc);
            const paid = parseFloat(document.getElementById('money-paid').value) || 0;
            const change = Math.max(0, paid - total);

            try {
                const formData = new FormData();
                formData.append('_token', CSRF_TOKEN);
                formData.append('payment_method', currentPay);
                formData.append('discount', disc);
                formData.append('cart', JSON.stringify(cart));

                const response = await fetch(CHECKOUT_URL, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                });

                const result = await response.json();

                if (!response.ok || result.success === false) {
                    throw new Error(result.message || 'Transaksi gagal.');
                }

                const newId = result.id || Date.now();

                lastTrxSnapshot = {
                    id: newId,
                    created_at: nowStr(),
                    payment_method: currentPay,
                    sub_total: sub,
                    discount: disc,
                    total: total,
                    money_paid: paid,
                    kasir: KASIR_NAME,
                    items: cart.map(item => ({
                        nama_item: item.name,
                        qty: item.qty,
                        unit_price: item.unitPrice,
                        subtotal: item.subtotal,
                    })),
                };

                todayTrxList = [...todayTrxList, lastTrxSnapshot];
                updateStats();

                closeModal('modal-checkout');
                showReceipt(sub, disc, total, paid, change, newId);

                await refreshStocks(); // ✅ REFRESH DATA MENU & STOK DARI SERVER SETELAH TRANSAKSI, SUPAYA UPDATE

                document.getElementById('ajax-error').style.display = 'none';

            } catch (err) {
                document.getElementById('ajax-error').textContent =
                    err.message || 'Terjadi kesalahan. Coba lagi';

                document.getElementById('ajax-error').style.display = '';

                closeModal('modal-checkout');
            } finally {
                btnConfirm.disabled = false;
                btnConfirm.innerHTML = '✓ Ya, Simpan Transaksi';
                btnCancel.disabled = false;
            }
        }

        async function refreshStocks() {

            try {

                const response = await fetch(window.location.href, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const html = await response.text();

                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newStockGrid = doc.querySelector('#stock-grid');

                if (newStockGrid) {
                    document.querySelector('#stock-grid').innerHTML =
                        newStockGrid.innerHTML;
                }

            } catch (err) {
                console.error('Gagal refresh stok:', err);
            }
        }

        /* ════════════════════════════════════════════
           MODAL 3 — STRUK
        ════════════════════════════════════════════ */
        function showReceipt(sub, disc, total, paid, change, id) {
            document.getElementById('rc-datetime').textContent = nowStr();
            document.getElementById('rc-trxno').textContent = 'No. Transaksi: ' + trxNo(id);
            document.getElementById('rc-method').textContent = METHOD_LABELS[currentPay] ?? currentPay;

            document.getElementById('rc-items').innerHTML = cart.map(item => `
        <div class="receipt-row">
            <span>${item.name} <span style="color:#9ca3af">x${item.qty}</span></span>
            <span>${fmt(item.subtotal)}</span>
        </div>`).join('');

            document.getElementById('rc-sub').textContent = fmt(sub);
            document.getElementById('rc-disc').textContent = fmt(disc);
            document.getElementById('rc-total').textContent = fmt(total);
            document.getElementById('rc-paid').textContent = fmt(paid);
            document.getElementById('rc-change').textContent = 'Kembalian: ' + fmt(change);

            openModal('modal-receipt');
        }

        function resetAndClose() {
            cart = [];
            document.getElementById('discount').value = 0;
            document.getElementById('money-paid').value = 0;
            renderCart();
            closeModal('modal-receipt');
        }

        /* ════════════════════════════════════════════════════════════════════
           POS REPORTS — STRUK PDF, LAPORAN PDF, LAPORAN EXCEL
        ════════════════════════════════════════════════════════════════════ */
        function _rp(n) {
            return 'Rp' + Number(n || 0).toLocaleString('id-ID', {
                maximumFractionDigits: 0
            });
        }

        function _methodLabel(m) {
            return METHOD_LABELS[m] ?? m ?? '—';
        }

        function _trxNo(id) {
            return 'TRX-' + String(id || 0).padStart(5, '0');
        }

        function _dashed(doc, x1, x2, y) {
            doc.setDrawColor(209, 213, 219);
            doc.setLineDash([1.5, 1.5]);
            doc.line(x1, y, x2, y);
            doc.setLineDash([]);
        }

        function _solid(doc, x1, x2, y, rgb) {
            doc.setDrawColor(...(rgb || [17, 24, 39]));
            doc.setLineDash([]);
            doc.line(x1, y, x2, y);
        }

        function _row(doc, lm, rm, y, label, value) {
            doc.text(label, lm, y);
            doc.text(value, rm, y, {
                align: 'right'
            });
        }

        function _sectionTitle(doc, x, y, text) {
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(8);
            doc.setTextColor(79, 70, 229);
            doc.text(text, x, y);
            doc.setDrawColor(79, 70, 229);
            doc.setLineWidth(0.4);
            doc.line(x, y + 1, x + doc.getTextWidth(text) + 2, y + 1);
            doc.setLineWidth(0.2);
            doc.setTextColor(17, 24, 39);
        }

        function _tableHeader(doc, lm, y, cols, rowH, rgb) {
            rgb = rgb || [79, 70, 229];
            const totalW = cols.reduce((s, c) => s + c.w, 0);
            doc.setFillColor(...rgb);
            doc.rect(lm, y, totalW, rowH, 'F');
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(7.5);
            doc.setTextColor(255, 255, 255);
            let cx = lm;
            cols.forEach(col => {
                const tx = col.align === 'right' ? cx + col.w - 2 : col.align === 'center' ? cx + col.w / 2 : cx +
                    2;
                doc.text(col.label, tx, y + 4.8, {
                    align: col.align === 'center' ? 'center' : col.align === 'right' ? 'right' : 'left'
                });
                cx += col.w;
            });
            return y + rowH;
        }

        function _tableRow(doc, lm, y, cols, rowH, fillRGB, values) {
            const totalW = cols.reduce((s, c) => s + c.w, 0);
            doc.setFillColor(...fillRGB);
            doc.rect(lm, y, totalW, rowH, 'F');
            doc.setDrawColor(229, 231, 235);
            doc.rect(lm, y, totalW, rowH, 'S');
            doc.setFont('helvetica', 'normal');
            doc.setFontSize(7.5);
            doc.setTextColor(17, 24, 39);
            let cx = lm;
            cols.forEach((col, i) => {
                const val = values[i] ?? '';
                const tx = col.align === 'right' ? cx + col.w - 2 : col.align === 'center' ? cx + col.w / 2 : cx +
                    2;
                const truncated = doc.splitTextToSize(String(val), col.w - 3)[0] || '';
                doc.text(truncated, tx, y + 4.8, {
                    align: col.align === 'center' ? 'center' : col.align === 'right' ? 'right' : 'left'
                });
                cx += col.w;
            });
        }

        /* ── 1. STRUK PDF ── */
        function downloadStrukPdf() {
            const trx = lastTrxSnapshot;
            if (!trx) {
                alert('Tidak ada data transaksi.');
                return;
            }
            const {
                jsPDF
            } = window.jspdf;
            const itemCount = (trx.items || []).length;
            const pageHeight = Math.max(140, 65 + itemCount * 12 + 55);
            const doc = new jsPDF({
                unit: 'mm',
                format: [80, pageHeight],
                orientation: 'portrait'
            });
            const W = 80,
                lm = 5,
                rm = 75;
            let y = 10;
            const DARK = [17, 24, 39],
                GRAY = [107, 114, 128],
                GREEN = [5, 150, 105];
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(12);
            doc.setTextColor(...DARK);
            doc.text(BRAND, W / 2, y, {
                align: 'center'
            });
            y += 5;
            doc.setFont('helvetica', 'normal');
            doc.setFontSize(7);
            doc.setTextColor(...GRAY);
            doc.text(ADDRESS, W / 2, y, {
                align: 'center'
            });
            y += 4;
            doc.text(trx.created_at || nowStr(), W / 2, y, {
                align: 'center'
            });
            y += 3.5;
            doc.text(_methodLabel(trx.payment_method), W / 2, y, {
                align: 'center'
            });
            y += 4;
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(8);
            doc.setTextColor(...DARK);
            doc.text(_trxNo(trx.id), W / 2, y, {
                align: 'center'
            });
            y += 4;
            _dashed(doc, lm, rm, y);
            y += 3;
            doc.setFontSize(7.5);
            doc.setFont('helvetica', 'bold');
            doc.setTextColor(...GRAY);
            doc.text('ITEM', lm, y);
            doc.text('QTY', 50, y, {
                align: 'center'
            });
            doc.text('SUBTOTAL', rm, y, {
                align: 'right'
            });
            y += 1.5;
            _solid(doc, lm, rm, y, GRAY);
            y += 3;
            doc.setFont('helvetica', 'normal');
            doc.setTextColor(...DARK);
            for (const item of (trx.items || [])) {
                const lines = doc.splitTextToSize(item.nama_item, 40);
                lines.forEach((line, i) => doc.text(line, lm, y + i * 4));
                const rowH = lines.length * 4,
                    midY = y + rowH / 2 - 1.5;
                doc.text(String(item.qty), 50, midY, {
                    align: 'center'
                });
                doc.text(_rp(item.subtotal), rm, midY, {
                    align: 'right'
                });
                y += rowH + 1.5;
            }
            y += 2;
            _dashed(doc, lm, rm, y);
            y += 4;
            doc.setFontSize(8);
            doc.setTextColor(...GRAY);
            _row(doc, lm, rm, y, 'Subtotal', _rp(trx.sub_total));
            y += 4.5;
            _row(doc, lm, rm, y, 'Diskon', _rp(trx.discount));
            y += 3;
            _solid(doc, lm, rm, y, DARK);
            y += 3;
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(11);
            doc.setTextColor(...DARK);
            _row(doc, lm, rm, y, 'TOTAL', _rp(trx.total));
            y += 6;
            const change = Math.max(0, (trx.money_paid || 0) - (trx.total || 0));
            doc.setFont('helvetica', 'normal');
            doc.setFontSize(8);
            doc.setTextColor(...GRAY);
            _row(doc, lm, rm, y, 'Dibayar', _rp(trx.money_paid));
            y += 4.5;
            doc.setFont('helvetica', 'bold');
            doc.setTextColor(...GREEN);
            _row(doc, lm, rm, y, 'Kembalian', _rp(change));
            y += 7;
            _dashed(doc, lm, rm, y);
            y += 4;
            doc.setFont('helvetica', 'normal');
            doc.setFontSize(7);
            doc.setTextColor(...GRAY);
            doc.text('Terima kasih sudah berbelanja!', W / 2, y, {
                align: 'center'
            });
            doc.save(`struk-${_trxNo(trx.id)}.pdf`);
        }

        /* ── 2. LAPORAN PDF ── */
        function downloadLaporanPdf() {
            if (!todayTrxList.length) {
                alert('Belum ada transaksi hari ini.');
                return;
            }
            const {
                jsPDF
            } = window.jspdf;
            const tanggal = TANGGAL_HARI;
            const doc = new jsPDF({
                unit: 'mm',
                format: 'a4',
                orientation: 'portrait'
            });
            const W = 210,
                lm = 18,
                rm = W - 18,
                cw = rm - lm;
            let y = 18;
            const totalRev = todayTrxList.reduce((s, t) => s + (t.total || 0), 0);
            const totalDisc = todayTrxList.reduce((s, t) => s + (t.discount || 0), 0);
            const totalItems = todayTrxList.reduce((s, t) => s + (t.items || []).reduce((si, i) => si + (i.qty || 0), 0),
                0);
            const itemAgg = {};
            todayTrxList.forEach(t => {
                (t.items || []).forEach(i => {
                    if (!itemAgg[i.nama_item]) itemAgg[i.nama_item] = {
                        qty: 0,
                        revenue: 0
                    };
                    itemAgg[i.nama_item].qty += i.qty || 0;
                    itemAgg[i.nama_item].revenue += i.subtotal || 0;
                });
            });
            doc.setFillColor(79, 70, 229);
            doc.rect(lm, y - 3, cw, 18, 'F');
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(16);
            doc.setTextColor(255, 255, 255);
            doc.text(BRAND, lm + 6, y + 5);
            doc.setFont('helvetica', 'normal');
            doc.setFontSize(9);
            doc.text('Laporan Transaksi Harian — ' + tanggal, lm + 6, y + 11);
            y += 22;
            doc.setFontSize(8);
            doc.setTextColor(107, 114, 128);
            doc.text('Dicetak: ' + nowStr(), lm, y);
            y += 8;
            _sectionTitle(doc, lm, y, 'RINGKASAN');
            y += 6;
            const sumItems = [{
                    label: 'Jumlah Transaksi',
                    val: String(todayTrxList.length),
                    color: [79, 70, 229]
                },
                {
                    label: 'Total Item Terjual',
                    val: String(totalItems),
                    color: [79, 70, 229]
                },
                {
                    label: 'Total Diskon',
                    val: _rp(totalDisc),
                    color: [217, 119, 6]
                },
                {
                    label: 'Total Pendapatan',
                    val: _rp(totalRev),
                    color: [5, 150, 105]
                },
            ];
            const boxW = (cw - 6) / 2,
                boxH = 18;
            sumItems.forEach((s, idx) => {
                const bx = lm + (idx % 2) * (boxW + 6),
                    by = y + Math.floor(idx / 2) * (boxH + 4);
                doc.setFillColor(248, 250, 252);
                doc.roundedRect(bx, by, boxW, boxH, 2, 2, 'F');
                doc.setDrawColor(229, 231, 235);
                doc.roundedRect(bx, by, boxW, boxH, 2, 2, 'S');
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(7.5);
                doc.setTextColor(107, 114, 128);
                doc.text(s.label, bx + 4, by + 5.5);
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(12);
                doc.setTextColor(...s.color);
                doc.text(s.val, bx + 4, by + 14);
            });
            y += boxH * 2 + 4 + 10;
            _sectionTitle(doc, lm, y, 'DETAIL TRANSAKSI');
            y += 6;
            const cols = [{
                    label: 'No.',
                    w: 10,
                    align: 'center'
                }, {
                    label: 'No. TRX',
                    w: 28,
                    align: 'left'
                },
                {
                    label: 'Waktu',
                    w: 35,
                    align: 'left'
                }, {
                    label: 'Metode',
                    w: 32,
                    align: 'left'
                },
                {
                    label: 'Diskon',
                    w: 22,
                    align: 'right'
                }, {
                    label: 'Total',
                    w: 27,
                    align: 'right'
                },
            ];
            const rowH = 7;
            y = _tableHeader(doc, lm, y, cols, rowH);
            todayTrxList.forEach((t, idx) => {
                if (y > 265) {
                    doc.addPage();
                    y = 18;
                }
                _tableRow(doc, lm, y, cols, rowH, idx % 2 === 0 ? [255, 255, 255] : [248, 250, 252], [
                    String(idx + 1), _trxNo(t.id), String(t.created_at || '').slice(0, 16),
                    _methodLabel(t.payment_method), _rp(t.discount), _rp(t.total),
                ]);
                y += rowH;
            });
            const totalColX = lm + cols.slice(0, 4).reduce((s, c) => s + c.w, 0);
            doc.setFillColor(17, 24, 39);
            doc.rect(lm, y, cw, rowH, 'F');
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(8);
            doc.setTextColor(255, 255, 255);
            doc.text('TOTAL', lm + 4, y + 4.8);
            doc.text(_rp(totalDisc), totalColX + cols[4].w - 2, y + 4.8, {
                align: 'right'
            });
            doc.text(_rp(totalRev), totalColX + cols[4].w + cols[5].w - 2, y + 4.8, {
                align: 'right'
            });
            y += rowH + 10;
            if (y > 230) {
                doc.addPage();
                y = 18;
            }
            _sectionTitle(doc, lm, y, 'REKAPITULASI PER MENU');
            y += 6;
            const mcols = [{
                    label: 'No.',
                    w: 10,
                    align: 'center'
                }, {
                    label: 'Nama Menu',
                    w: 80,
                    align: 'left'
                },
                {
                    label: 'Qty Terjual',
                    w: 28,
                    align: 'center'
                }, {
                    label: 'Total Pendapatan',
                    w: 38,
                    align: 'right'
                },
                {
                    label: '% Pendapatan',
                    w: 18,
                    align: 'right'
                },
            ];
            y = _tableHeader(doc, lm, y, mcols, rowH, [5, 150, 105]);
            Object.entries(itemAgg).sort((a, b) => b[1].revenue - a[1].revenue).forEach(([name, v], idx) => {
                if (y > 265) {
                    doc.addPage();
                    y = 18;
                }
                const pct = totalRev ? ((v.revenue / totalRev) * 100).toFixed(1) + '%' : '0%';
                _tableRow(doc, lm, y, mcols, rowH, idx % 2 === 0 ? [255, 255, 255] : [240, 253, 244],
                    [String(idx + 1), name, String(v.qty), _rp(v.revenue), pct]);
                y += rowH;
            });
            const totalPages = doc.getNumberOfPages();
            for (let p = 1; p <= totalPages; p++) {
                doc.setPage(p);
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(7);
                doc.setTextColor(156, 163, 175);
                doc.text(`${BRAND} · Laporan ${tanggal} · Hal ${p}/${totalPages}`, W / 2, 290, {
                    align: 'center'
                });
            }
            doc.save(`laporan-${tanggal.replace(/\s+/g, '-')}.pdf`);
        }

        /* ── 3. LAPORAN EXCEL ── */
        function downloadLaporanExcel() {
            if (!todayTrxList.length) {
                alert('Belum ada transaksi hari ini.');
                return;
            }
            const XLSX = window.XLSX,
                tanggal = TANGGAL_HARI,
                wb = XLSX.utils.book_new();
            const totalRev = todayTrxList.reduce((s, t) => s + (t.total || 0), 0);
            const totalDisc = todayTrxList.reduce((s, t) => s + (t.discount || 0), 0);
            const totalItems = todayTrxList.reduce((s, t) => s + (t.items || []).reduce((si, i) => si + (i.qty || 0), 0),
                0);
            const ws1 = XLSX.utils.aoa_to_sheet([
                ['Laporan Transaksi Harian — ' + BRAND],
                ['Tanggal: ' + tanggal + '   |   Dicetak: ' + nowStr()],
                [],
                ['Indikator', 'Nilai'],
                ['Jumlah Transaksi', todayTrxList.length],
                ['Total Item Terjual', totalItems],
                ['Total Diskon (Rp)', totalDisc],
                ['Total Pendapatan (Rp)', totalRev],
            ]);
            ws1['!cols'] = [{
                wch: 28
            }, {
                wch: 24
            }];
            XLSX.utils.book_append_sheet(wb, ws1, 'Ringkasan');
            const detHdr = ['No.', 'No. TRX', 'Waktu', 'Kasir', 'Metode Bayar', 'Sub Total (Rp)', 'Diskon (Rp)',
                'Total (Rp)', 'Uang Dibayar (Rp)', 'Kembalian (Rp)'
            ];
            const detRows = todayTrxList.map((t, idx) => {
                const change = Math.max(0, (t.money_paid || 0) - (t.total || 0));
                return [idx + 1, _trxNo(t.id), String(t.created_at || '').slice(0, 16), t.kasir || '—',
                    _methodLabel(t.payment_method), t.sub_total || 0, t.discount || 0, t.total || 0, t
                    .money_paid || 0, change
                ];
            });
            const totRow = ['', '', '', '', 'TOTAL',
                detRows.reduce((s, r) => s + r[5], 0), detRows.reduce((s, r) => s + r[6], 0),
                detRows.reduce((s, r) => s + r[7], 0), detRows.reduce((s, r) => s + r[8], 0), detRows.reduce((s, r) =>
                    s + r[9], 0)
            ];
            const ws2 = XLSX.utils.aoa_to_sheet([detHdr, ...detRows, totRow]);
            ws2['!cols'] = [{
                wch: 6
            }, {
                wch: 16
            }, {
                wch: 20
            }, {
                wch: 16
            }, {
                wch: 22
            }, {
                wch: 18
            }, {
                wch: 14
            }, {
                wch: 16
            }, {
                wch: 18
            }, {
                wch: 16
            }];
            ws2['!autofilter'] = {
                ref: 'A1:J1'
            };
            XLSX.utils.book_append_sheet(wb, ws2, 'Detail Transaksi');
            const itemAgg = {};
            todayTrxList.forEach(t => {
                (t.items || []).forEach(i => {
                    if (!itemAgg[i.nama_item]) itemAgg[i.nama_item] = {
                        qty: 0,
                        revenue: 0
                    };
                    itemAgg[i.nama_item].qty += i.qty || 0;
                    itemAgg[i.nama_item].revenue += i.subtotal || 0;
                });
            });
            const menuHdr = ['No.', 'Nama Menu', 'Total Qty Terjual', 'Total Pendapatan (Rp)', '% dari Pendapatan'];
            const menuRows = Object.entries(itemAgg).sort((a, b) => b[1].revenue - a[1].revenue)
                .map(([name, v], idx) => [idx + 1, name, v.qty, v.revenue, totalRev ? +(v.revenue / totalRev).toFixed(4) :
                    0
                ]);
            const menuTot = ['', 'TOTAL', menuRows.reduce((s, r) => s + r[2], 0), menuRows.reduce((s, r) => s + r[3], 0),
                1
            ];
            const ws3 = XLSX.utils.aoa_to_sheet([menuHdr, ...menuRows, menuTot]);
            ws3['!cols'] = [{
                wch: 6
            }, {
                wch: 36
            }, {
                wch: 20
            }, {
                wch: 24
            }, {
                wch: 18
            }];
            for (let r = 2; r <= menuRows.length + 2; r++) {
                const cell = ws3[`E${r}`];
                if (cell) cell.z = '0.0%';
            }
            XLSX.utils.book_append_sheet(wb, ws3, 'Rekapitulasi Menu');
            XLSX.writeFile(wb, `laporan-${tanggal.replace(/\s+/g, '-')}.xlsx`);
        }

        /* ════════════════════════════════════════════
           INIT
        ════════════════════════════════════════════ */
        renderMenuPrices();
        renderCart();
    </script>

</x-layouts.app>
