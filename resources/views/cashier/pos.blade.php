@props(['title' => 'POS Kasir'])

<x-layouts.app :title="$title">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Nunito:wght@400;600;700;800&display=swap"
        rel="stylesheet" />
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
                }
            }
        }
    </script>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background: #FFF8E7;
        }

        /* ── LAYOUT ── */
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
            background: #FFF8E7;
        }

        .pos-right {
            border-left: 3px solid #C0271A;
            background: #fff;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* ── CARD ── */
        .card {
            background: #fff;
            border-radius: 16px;
            padding: 18px 20px;
            border: 2px solid #f0d9a0;
            box-shadow: 0 2px 8px rgba(192, 39, 26, 0.06);
        }

        .card-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 16px;
            letter-spacing: .12em;
            color: #C0271A;
            margin-bottom: 14px;
        }

        /* ── TOPBAR ── */
        .topbar {
            background: #C0271A;
            border-radius: 16px;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            box-shadow: 0 4px 16px rgba(192, 39, 26, 0.25);
        }

        .topbar h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 26px;
            color: #F5C518;
            letter-spacing: .1em;
        }

        .topbar p {
            font-size: 11px;
            color: #fecaca;
            margin-top: 1px;
        }

        /* ── BADGE ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 12px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 700;
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .badge-green {
            background: #F5C518;
            color: #9B1E13;
            border-color: #e6b800;
        }

        /* ── BUTTONS TOPBAR ── */
        .btn-logout {
            padding: 7px 16px;
            border-radius: 99px;
            background: rgba(255, 255, 255, 0.12);
            color: #fecaca;
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: background .15s;
            font-family: 'Nunito', sans-serif;
        }

        .btn-logout:hover {
            background: rgba(255, 255, 255, 0.22);
            color: #fff;
        }

        .btn-laporan-topbar {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 99px;
            background: rgba(245, 197, 24, 0.15);
            color: #F5C518;
            border: 1px solid rgba(245, 197, 24, 0.35);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: background .15s;
            font-family: 'Nunito', sans-serif;
        }

        .btn-laporan-topbar:hover {
            background: rgba(245, 197, 24, 0.25);
        }

        .btn-sheets-topbar {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 99px;
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: background .15s;
            font-family: 'Nunito', sans-serif;
        }

        .btn-sheets-topbar:hover {
            background: rgba(255, 255, 255, 0.22);
        }

        .btn-sheets-topbar:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-sheets-topbar svg {
            width: 14px;
            height: 14px;
        }

        .shift-report {
            min-width: 260px;
            max-width: 320px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 14px;
            padding: 12px 14px;
            display: grid;
            gap: 6px;
            font-size: 12px;
            color: #fff;
        }

        .shift-report-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 13px;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #F5C518;
        }

        .shift-report-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            padding: 6px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }

        .shift-report-line:last-child {
            border-bottom: none;
        }

        .shift-report-line strong {
            font-weight: 800;
            color: #F5C518;
        }

        .shift-report-line.shift-total strong {
            color: #fff;
        }

        /* ── STAT CARDS ── */
        .stat-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 16px 18px;
            border: 2px solid #f0d9a0;
            box-shadow: 0 2px 8px rgba(192, 39, 26, 0.06);
        }

        .stat-card .s-label {
            font-size: 11px;
            color: #9ca3af;
            font-weight: 700;
        }

        .stat-card .s-val {
            font-size: 20px;
            font-weight: 800;
            margin-top: 6px;
            font-family: 'Bebas Neue', sans-serif;
            letter-spacing: .05em;
        }

        .s-emerald {
            color: #059669;
        }

        .s-indigo {
            color: #C0271A;
        }

        .s-amber {
            color: #d97706;
        }

        /* ── ALERTS ── */
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

        /* ── STOCK WARNING BANNER ── */
        .stock-warning-banner {
            border-radius: 14px;
            padding: 14px 16px;
            font-size: 13px;
            background: #fef2f2;
            color: #991b1b;
            border: 1.5px solid #fca5a5;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .stock-warning-banner .swb-icon {
            font-size: 18px;
            flex-shrink: 0;
            line-height: 1;
        }

        .stock-warning-banner .swb-title {
            font-weight: 800;
            font-size: 13px;
            margin-bottom: 4px;
        }

        .stock-warning-banner .swb-items {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 6px;
        }

        .swb-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 10px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 700;
        }

        .swb-chip-red {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        .swb-chip-orange {
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #fed7aa;
        }

        .swb-chip-yellow {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        /* ── PAYMENT METHODS ── */
        .pay-methods {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .pay-btn {
            padding: 8px 18px;
            border-radius: 99px;
            border: 2px solid #f0d9a0;
            background: #fff;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            color: #9ca3af;
            transition: all .15s;
            font-family: 'Nunito', sans-serif;
        }

        .pay-btn:hover {
            border-color: #C0271A;
            color: #C0271A;
        }

        .pay-btn.active {
            background: #C0271A;
            border-color: #C0271A;
            color: #F5C518;
        }

        /* ── MENU GRID ── */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(155px, 1fr));
            gap: 10px;
        }

        .menu-card {
            border-radius: 14px;
            border: 2px solid #f0d9a0;
            background: #FFFDF5;
            padding: 14px;
            cursor: pointer;
            transition: all .18s;
            text-align: left;
            width: 100%;
        }

        .menu-card:hover {
            border-color: #C0271A;
            background: #fff0ee;
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(192, 39, 26, 0.15);
        }

        .menu-card h3 {
            font-size: 13px;
            font-weight: 800;
            color: #1f1f1f;
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
            font-weight: 800;
            color: #C0271A;
            font-family: 'Bebas Neue', sans-serif;
            letter-spacing: .04em;
        }

        /* ── STOCK GRID ── */
        .stock-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 8px;
        }

        .stk {
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 12px;
            border: 1.5px solid;
        }

        .stk h4 {
            font-weight: 800;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .stk-red {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #991b1b;
        }

        .stk-yellow {
            background: #fffbeb;
            border-color: #fde68a;
            color: #92400e;
        }

        .stk-orange {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #c2410c;
        }

        .stk-ok {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #166534;
        }

        /* ── RIGHT PANEL ── */
        .rp-header {
            padding: 14px 18px;
            border-bottom: 2px solid #f0d9a0;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 18px;
            letter-spacing: .1em;
            color: #C0271A;
            display: flex;
            align-items: center;
            gap: 8px;
            background: #FFF8E7;
        }

        .cart-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 14px 16px;
        }

        .cart-empty-box {
            border: 2px dashed #f0d9a0;
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
            border-bottom: 1px solid #fef3c7;
            gap: 8px;
        }

        .ci-name {
            font-size: 13px;
            font-weight: 800;
            color: #1f1f1f;
        }

        .ci-qty {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 2px;
        }

        .ci-price {
            font-size: 13px;
            font-weight: 800;
            color: #C0271A;
            text-align: right;
        }

        .ci-remove {
            font-size: 10px;
            font-weight: 700;
            color: #C0271A;
            cursor: pointer;
            margin-top: 4px;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .ci-remove:hover {
            color: #9B1E13;
        }

        .custom-badge {
            display: inline-block;
            background: #F5C518;
            color: #9B1E13;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
            padding: 1px 7px;
            border-radius: 99px;
            margin-left: 5px;
            vertical-align: middle;
        }

        /* ── CUSTOM SECTION ── */
        .custom-section {
            padding: 12px 16px;
            border-top: 2px solid #f0d9a0;
            background: #FFFDF5;
        }

        .custom-section .ct {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 14px;
            letter-spacing: .1em;
            color: #C0271A;
            margin-bottom: 8px;
        }

        .input-sm {
            width: 100%;
            border: 1.5px solid #f0d9a0;
            border-radius: 10px;
            padding: 7px 10px;
            font-size: 13px;
            font-family: 'Nunito', sans-serif;
            color: #1f1f1f;
            background: #fff;
            outline: none;
            transition: border-color .15s;
        }

        .input-sm:focus {
            border-color: #C0271A;
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
            background: #C0271A;
            color: #F5C518;
            border: none;
            border-radius: 10px;
            padding: 7px 14px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            white-space: nowrap;
            transition: background .15s;
            font-family: 'Nunito', sans-serif;
        }

        .btn-add-custom:hover {
            background: #9B1E13;
        }

        /* ── CART SUMMARY ── */
        .cart-summary {
            padding: 12px 16px;
            border-top: 2px solid #f0d9a0;
            background: #FFFDF5;
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
            font-weight: 700;
        }

        .sum-line {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            color: #6b7280;
        }

        .sum-line.big {
            font-size: 15px;
            font-weight: 800;
            color: #C0271A;
            padding-top: 8px;
            margin-top: 4px;
            border-top: 2px dashed #f0d9a0;
        }

        /* ── MONEY SECTION ── */
        .money-section {
            padding: 10px 16px;
            border-top: 2px solid #f0d9a0;
        }

        .money-section label {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 13px;
            letter-spacing: .1em;
            color: #C0271A;
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
            font-weight: 800;
            color: #059669;
        }

        /* ── CHECKOUT ── */
        .checkout-bar {
            padding: 12px 16px;
            border-top: 2px solid #f0d9a0;
            background: #fff;
        }

        .btn-checkout {
            width: 100%;
            background: linear-gradient(135deg, #C0271A 0%, #9B1E13 100%);
            color: #F5C518;
            border: none;
            border-radius: 12px;
            padding: 13px;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 18px;
            letter-spacing: .1em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all .15s;
            box-shadow: 0 4px 14px rgba(192, 39, 26, 0.3);
        }

        .btn-checkout:hover {
            background: linear-gradient(135deg, #9B1E13 0%, #7a1710 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(192, 39, 26, 0.4);
        }

        .btn-checkout:active {
            transform: scale(.98);
        }

        .btn-checkout:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }

        .btn-checkout svg {
            width: 18px;
            height: 18px;
        }

        /* ── OVERLAY & MODALS ── */
        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(155, 30, 19, 0.45);
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
            border: 2px solid #f0d9a0;
            box-shadow: 0 20px 60px rgba(192, 39, 26, 0.2);
        }

        .modal-wide {
            max-width: 620px;
        }

        .modal-sm {
            max-width: 400px;
        }

        .modal-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
        }

        .modal-head h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 22px;
            color: #C0271A;
            letter-spacing: .06em;
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
            background: #fef2f2;
            border: 1px solid #fecaca;
            cursor: pointer;
            font-size: 12px;
            color: #C0271A;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .15s;
        }

        .modal-close:hover {
            background: #fee2e2;
        }

        .modal-section {
            margin-bottom: 16px;
        }

        .modal-section-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 14px;
            letter-spacing: .1em;
            color: #C0271A;
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
            border: 1.5px solid;
            margin-bottom: 7px;
        }

        .mstk h5 {
            font-weight: 800;
            margin-bottom: 2px;
        }

        .mstk-ok {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #166534;
        }

        .mstk-yellow {
            background: #fffbeb;
            border-color: #fde68a;
            color: #92400e;
        }

        .mstk-orange {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #c2410c;
        }

        .mstk-red {
            background: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }

        /* ── ORDER BOX ── */
        .order-box {
            background: #FFF8E7;
            border-radius: 14px;
            padding: 16px;
            border: 2px solid #f0d9a0;
        }

        .price-display {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 28px;
            color: #C0271A;
            margin: 8px 0 14px;
            letter-spacing: .04em;
        }

        .qty-control {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .qty-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            border: 2px solid #f0d9a0;
            background: #fff;
            font-size: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #C0271A;
            font-weight: 800;
            transition: all .1s;
        }

        .qty-btn:hover {
            background: #C0271A;
            color: #F5C518;
            border-color: #C0271A;
        }

        .qty-val {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 22px;
            min-width: 32px;
            text-align: center;
            color: #1f1f1f;
        }

        .btn-add-cart {
            width: 100%;
            background: #C0271A;
            color: #F5C518;
            border: none;
            border-radius: 11px;
            padding: 12px;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 17px;
            letter-spacing: .08em;
            cursor: pointer;
            margin-top: 14px;
            transition: background .15s;
        }

        .btn-add-cart:hover {
            background: #9B1E13;
        }

        /* ── CONFIRM LIST ── */
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
            border-bottom: 1px solid #fef3c7;
            font-size: 13px;
        }

        .confirm-list li .cn {
            font-weight: 800;
            color: #1f1f1f;
        }

        .confirm-list li .cq {
            font-size: 11px;
            color: #9ca3af;
        }

        .confirm-list li .cs {
            font-weight: 800;
            color: #C0271A;
        }

        .confirm-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 20px;
            padding-top: 10px;
            margin-top: 6px;
            border-top: 3px solid #C0271A;
            color: #C0271A;
        }

        /* ── MODAL ACTIONS ── */
        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-modal-cancel {
            flex: 1;
            padding: 11px;
            border-radius: 11px;
            border: 2px solid #f0d9a0;
            background: #fff;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            color: #374151;
            font-family: 'Nunito', sans-serif;
            transition: background .15s;
        }

        .btn-modal-cancel:hover {
            background: #FFF8E7;
        }

        .btn-modal-confirm {
            flex: 2;
            padding: 11px;
            border-radius: 11px;
            border: none;
            background: #C0271A;
            color: #F5C518;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 17px;
            letter-spacing: .08em;
            cursor: pointer;
            transition: background .15s;
        }

        .btn-modal-confirm:hover {
            background: #9B1E13;
        }

        .btn-modal-confirm:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }

        /* ── STRUK ── */
        .receipt-thermal {
            background: #fff;
            border: 2px dashed #f0d9a0;
            border-radius: 8px;
            padding: 20px 18px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #1f1f1f;
        }

        .rc-brand {
            text-align: center;
            padding-bottom: 12px;
            margin-bottom: 12px;
            border-bottom: 2px dashed #f0d9a0;
        }

        .rc-brand h3 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 18px;
            letter-spacing: .1em;
            color: #C0271A;
            margin: 0 0 3px;
        }

        .rc-brand p {
            font-size: 11px;
            color: #6b7280;
            margin: 2px 0 0;
        }

        .rc-brand .rc-method-badge {
            display: inline-block;
            margin-top: 6px;
            padding: 2px 12px;
            background: #C0271A;
            color: #F5C518;
            border-radius: 99px;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 11px;
            letter-spacing: .1em;
        }

        .rc-meta {
            padding-bottom: 10px;
            margin-bottom: 10px;
            border-bottom: 1px dashed #f0d9a0;
        }

        .rc-meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            padding: 2px 0;
            color: #374151;
        }

        .rc-meta-row span:first-child {
            color: #9ca3af;
        }

        .rc-items-head {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #9ca3af;
            padding-bottom: 5px;
            border-bottom: 1px solid #f0d9a0;
            margin-bottom: 5px;
        }

        .rc-item-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: 5px 0;
            border-bottom: 1px dashed #f5e6c8;
        }

        .rc-item-name {
            font-size: 12px;
            font-weight: 700;
            color: #1f1f1f;
        }

        .rc-item-detail {
            font-size: 10px;
            color: #9ca3af;
            margin-top: 1px;
        }

        .rc-item-sub {
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
            color: #C0271A;
        }

        .rc-sum {
            padding: 10px 0 8px;
            border-bottom: 1px dashed #f0d9a0;
        }

        .rc-sum-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            padding: 2px 0;
            color: #6b7280;
        }

        .rc-sum-row.rc-total {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 16px;
            letter-spacing: .06em;
            color: #C0271A;
            margin-top: 6px;
            padding-top: 6px;
            border-top: 2px solid #C0271A;
        }

        .rc-pay {
            padding: 10px 0 0;
        }

        .rc-pay-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            padding: 2px 0;
            color: #374151;
        }

        .rc-kembalian-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
            padding: 7px 10px;
            border: 2px solid #F5C518;
            border-radius: 6px;
            background: #FFF8E7;
        }

        .rc-kembalian-box span {
            font-size: 11px;
            color: #9B1E13;
            font-weight: 700;
        }

        .rc-kembalian-box strong {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 16px;
            color: #C0271A;
            letter-spacing: .04em;
        }

        .rc-footer {
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px dashed #f0d9a0;
            text-align: center;
        }

        .rc-footer p {
            font-size: 11px;
            color: #9ca3af;
            margin: 2px 0;
        }

        .rc-footer .rc-wave {
            color: #f0d9a0;
            letter-spacing: .2em;
            font-size: 10px;
            margin-top: 6px;
        }

        /* ── RECEIPT BUTTONS ── */
        .receipt-btn-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 7px;
            margin-top: 12px;
        }

        .btn-rc {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 9px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Nunito', sans-serif;
            transition: background .15s;
            border: 2px solid;
        }

        .btn-rc-print {
            background: #fff;
            border-color: #f0d9a0;
            color: #374151;
        }

        .btn-rc-print:hover {
            background: #FFF8E7;
        }

        .btn-rc-sheets {
            background: #FFF8E7;
            border-color: #F5C518;
            color: #9B1E13;
        }

        .btn-rc-sheets:hover {
            background: #fef3c7;
        }

        .btn-rc-sheets:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-rc-new-trx {
            width: 100%;
            margin-top: 8px;
            padding: 11px;
            border-radius: 10px;
            border: none;
            background: #C0271A;
            color: #F5C518;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 17px;
            letter-spacing: .08em;
            cursor: pointer;
            transition: background .15s;
        }

        .btn-rc-new-trx:hover {
            background: #9B1E13;
        }

        /* ── LAPORAN MODAL ── */
        .laporan-modal-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 12px;
        }

        .lap-stat {
            background: #FFF8E7;
            border-radius: 12px;
            padding: 14px 16px;
            border: 2px solid #f0d9a0;
        }

        .lap-stat .ls-label {
            font-size: 11px;
            color: #9ca3af;
            font-weight: 700;
        }

        .lap-stat .ls-val {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 24px;
            margin-top: 4px;
            letter-spacing: .04em;
        }

        .btn-lap-action {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px;
            border-radius: 11px;
            border: 2px solid;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Nunito', sans-serif;
            transition: background .15s;
            margin-bottom: 8px;
        }

        .btn-lap-sheets {
            background: #FFF8E7;
            border-color: #F5C518;
            color: #9B1E13;
        }

        .btn-lap-sheets:hover {
            background: #fef3c7;
        }

        .btn-lap-sheets:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* ── SHEETS CONFIG ── */
        .sheets-config {
            background: #FFF8E7;
            border-radius: 12px;
            padding: 14px 16px;
            border: 2px solid #f0d9a0;
            margin-bottom: 12px;
        }

        .sheets-config label {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 13px;
            letter-spacing: .1em;
            color: #C0271A;
            display: block;
            margin-bottom: 6px;
        }

        .sheets-config .hint {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 4px;
        }

        .sheets-config .hint code {
            background: #f0d9a0;
            padding: 1px 5px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: #9B1E13;
        }

        /* ── SYNC STATUS ── */
        .sync-status {
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 12px;
            font-weight: 700;
            margin-top: 8px;
            display: none;
        }

        .sync-ok {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .sync-err {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .sync-wait {
            background: #FFF8E7;
            color: #9B1E13;
            border: 1px solid #F5C518;
        }

        .spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255, 255, 255, .4);
            border-top-color: currentColor;
            border-radius: 50%;
            animation: spin .6s linear infinite;
            vertical-align: middle;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── PRINT ── */
        @media print {

            .pos-wrap,
            .topbar,
            #modal-menu,
            #modal-checkout,
            #modal-laporan,
            #modal-sheets {
                display: none !important;
            }

            #modal-receipt {
                position: static !important;
                background: none !important;
                backdrop-filter: none !important;
                padding: 0 !important;
                animation: none !important;
                align-items: flex-start !important;
                justify-content: flex-start !important;
            }

            #modal-receipt .modal {
                box-shadow: none !important;
                border: none !important;
                max-height: none !important;
                max-width: 100% !important;
                width: 80mm !important;
                margin: 0 !important;
                padding: 0 !important;
                animation: none !important;
                border-radius: 0 !important;
            }

            .modal-close,
            .receipt-btn-group,
            .btn-rc-new-trx,
            #sync-status-receipt {
                display: none !important;
            }

            @page {
                size: 80mm auto;
                margin: 4mm;
            }
        }

        /* ── RESPONSIVE ── */
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

            .laporan-modal-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="pos-wrap">

        {{-- ══ LEFT PANEL ══ --}}
        <div class="pos-left">

            {{-- Topbar --}}
            <div class="topbar">
                <div>
                    <h1>POS KASIR</h1>
                    <p>Sistem penjualan harian · inventori stok · checkout</p>
                </div>

                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                    <button id="btn-toggle-shift-report" class="btn-laporan-topbar" onclick="toggleShiftReport()">
                        Laporan Shift
                    </button>

                    <button id="btn-shift" class="btn-laporan-topbar" style="background:#16a34a;color:white"
                        onclick="handleShiftButton()">
                        Awali Shift 1
                    </button>
                </div>

                <div class="shift-report" id="shift-report" style="display:none">
                    <div class="shift-report-title">Laporan Shift Realtime</div>
                    <div class="shift-report-line">
                        <span>Status Shift</span>
                        <strong id="shift-current">Belum dimulai</strong>
                    </div>
                    <div class="shift-report-line">
                        <span>Cash</span>
                        <strong id="shift-cash">Rp0</strong>
                    </div>
                    <div class="shift-report-line">
                        <span>QRIS</span>
                        <strong id="shift-qris">Rp0</strong>
                    </div>
                    <div class="shift-report-line">
                        <span>GoFood</span>
                        <strong id="shift-gofood">Rp0</strong>
                    </div>
                    <div class="shift-report-line">
                        <span>ShopeeFood</span>
                        <strong id="shift-shopeefood">Rp0</strong>
                    </div>
                    <div class="shift-report-line shift-total">
                        <span>Shift 1</span>
                        <strong id="shift1-total">Rp0</strong>
                    </div>
                    <div class="shift-report-line shift-total">
                        <span>Shift 2</span>
                        <strong id="shift2-total">Rp0</strong>
                    </div>
                    <button id="btn-reset-shift-report" class="btn-laporan-topbar" onclick="resetShiftReportData()"
                        style="margin-top:12px; font-size:12px; padding:8px 12px; background:#000; color:#fff; border-radius:8px;">
                        Reset Laporan Shift
                    </button>
                </div>

                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                    <button type="button" class="btn-laporan-topbar" onclick="openRiwayatModal()">
                        Riwayat Pesanan
                    </button>
                    <a href="{{ route('attendance.reset') }}"
                        style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:99px;
                        background:rgba(245,197,24,0.15);color:#F5C518;border:1px solid rgba(245,197,24,0.35);
                        font-size:12px;font-weight:700;text-decoration:none;transition:background .15s;font-family:'Nunito',sans-serif"
                        onmouseover="this.style.background='rgba(245,197,24,0.25)'"
                        onmouseout="this.style.background='rgba(245,197,24,0.15)'">
                        Absensi Karyawan
                    </a>
git 
<form method="POST" action="{{ route('logout') }}" style="display:inline;margin:0">
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

            {{-- Stock Warning Banner --}}
            @php $lowStocks = $stocks->filter(fn($s) => $s->jumlah_stok <= 20); @endphp
            @if ($lowStocks->count())
                <div class="stock-warning-banner">
                    <div class="swb-icon">⚠️</div>
                    <div>
                        <div class="swb-title">Peringatan Stok Menipis!</div>
                        <div style="font-size:12px">Beberapa bahan membutuhkan perhatian segera:</div>
                        <div class="swb-items">
                            @foreach ($lowStocks as $s)
                                @php $stok = $s->jumlah_stok; @endphp
                                <span
                                    class="swb-chip {{ $stok <= 5 ? 'swb-chip-red' : ($stok <= 10 ? 'swb-chip-yellow' : 'swb-chip-orange') }}">
                                    {{ $s->pcsTahu?->nama_pcs ?? '—' }}: {{ $stok }} pcs
                                    @if ($stok <= 5)
                                        🔴
                                    @elseif($stok <= 10)
                                        🟡
                                    @else
                                        🟠
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif


            {{-- Stats --}}
            <div class="stat-row">
                <div class="stat-card">
                    <div class="s-label">Total Penjualan Hari Ini</div>
                    <div class="s-val s-emerald" id="stat-sales">
                        Rp{{ number_format($todaySales ?? 0, 0, ',', '.') }}
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

            {{-- Metode Pembayaran --}}
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

            {{-- Modal Riwayat --}}
            <div class="overlay" id="modal-riwayat" style="display:none"
                onclick="closeModalOutside(event,'modal-riwayat')">
                <div class="modal modal-wide">
                    <div class="modal-head">
                        <div>
                            <h2>📋 Riwayat Transaksi</h2>
                            <p id="riwayat-tanggal">—</p>
                        </div>
                        <button class="modal-close" onclick="closeModal('modal-riwayat')">✕</button>
                    </div>
                    <div id="riwayat-list" style="max-height:60vh;overflow-y:auto"></div>
                </div>
            </div>

            {{-- Menu Cards --}}
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

            {{-- Stok Inventori --}}
            <div class="card">
                <div class="card-title">Stok Inventori</div>
                <div class="stock-grid" id="stock-grid">
                    @foreach ($stocks as $s)
                        @php $stok = $s->jumlah_stok; @endphp
                        <div
                            class="stk {{ $stok <= 5 ? 'stk-red' : ($stok <= 10 ? 'stk-yellow' : ($stok <= 20 ? 'stk-orange' : 'stk-ok')) }}">
                            <h4>{{ $s->pcsTahu?->nama_pcs ?? '—' }}</h4>
                            <span>{{ $stok }} pcs
                                @if ($stok <= 5)
                                    hampir habis
                                @elseif($stok <= 10)
                                    sisa sedikit
                                @elseif($stok <= 20)
                                    mulai menipis
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
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                    stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1" />
                    <circle cx="20" cy="21" r="1" />
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
                </svg>
                KERANJANG
            </div>

            <div class="cart-scroll" id="cart-scroll">
                <div class="cart-empty-box" id="cart-empty">
                    Keranjang masih kosong.<br>
                    <span style="font-size:11px;color:#d1d5db">Klik kartu menu untuk menambahkan.</span>
                </div>
                <div id="cart-items-container"></div>
            </div>

            <div class="custom-section" id="custom-section">
                <div class="ct">Menu Tambahan</div>
                <input id="custom-name" class="input-sm" type="text"
                    placeholder="Nama item (mis: Saus Extra, Ongkir)">
                <div class="custom-row" style="margin-top:6px">
                    <input id="custom-price" class="input-sm" type="number" min="0"
                        placeholder="Harga (Rp)">
                    <input id="custom-qty" class="input-sm" type="number" min="1" value="1"
                        style="max-width:64px">
                </div>
                <button class="btn-add-custom" style="width:100%;margin-top:10px" onclick="addCustomMenu()">
                    + Tambah ke Keranjang
                </button>
            </div>

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

            <div class="money-section" id="money-section" style="display:none">
                <label for="money-paid">Uang Dibayar</label>
                <input id="money-paid" class="input-sm" type="number" min="0" value="0"
                    oninput="calcChange()">
                <div class="change-row">
                    <span>Kembalian</span>
                    <span id="change-display" class="change-amt">Rp0</span>
                </div>
            </div>

            <div class="checkout-bar">
                <button class="btn-checkout" id="btn-checkout" onclick="openCheckoutModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 7.5M7 13l-4-8m4 8h10m0 0l1.5 7.5M17 13v0" />
                    </svg>
                    CHECKOUT
                </button>
            </div>
        </div>

    </div>{{-- /pos-wrap --}}

    {{-- ═══ MODAL 1 — PILIH MENU ═══ --}}
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
                        <div style="font-size:12px;color:#9ca3af;font-weight:700">Harga saat ini</div>
                        <div class="price-display" id="mm-price">Rp0</div>
                        <div style="font-size:12px;color:#9ca3af;margin-bottom:8px;font-weight:700">Jumlah pesanan
                        </div>
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

    {{-- ═══ MODAL 2 — KONFIRMASI CHECKOUT ═══ --}}
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
                    <span id="co-total"></span>
                </div>
            </div>
            <div
                style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:14px;padding:14px;background:#FFF8E7;border-radius:12px;border:2px solid #f0d9a0;font-size:13px">
                <div>
                    <div style="color:#9ca3af;font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:.1em">
                        Metode</div>
                    <div style="font-weight:800;margin-top:4px;color:#C0271A" id="co-method">—</div>
                </div>
                <div>
                    <div style="color:#9ca3af;font-family:'Bebas Neue',sans-serif;font-size:13px;letter-spacing:.1em">
                        Kembalian</div>
                    <div style="font-weight:800;color:#059669;margin-top:4px" id="co-change">Rp0</div>
                </div>
            </div>
            <div style="background:#FFF8E7;border-radius:12px;padding:14px;border:2px solid #f0d9a0;margin-top:14px">
                <div
                    style="font-family:'Bebas Neue',sans-serif;font-size:14px;letter-spacing:.1em;color:#C0271A;margin-bottom:10px">
                    Konfirmasi Metode Pembayaran
                </div>
                <div id="co-pay-methods" style="display:flex;gap:8px;flex-wrap:wrap"></div>
            </div>
            <div class="modal-actions">
                <button class="btn-modal-cancel" id="btn-cancel-checkout"
                    onclick="closeModal('modal-checkout')">Batal</button>
                <button class="btn-modal-confirm" id="btn-confirm-checkout" onclick="saveTransaction()">✓ Ya,
                    Simpan
                    Transaksi</button>
            </div>
        </div>
    </div>

    {{-- ═══ MODAL 3 — STRUK ═══ --}}
    <div class="overlay" id="modal-receipt" style="display:none">
        <div class="modal modal-sm">
            <div class="receipt-thermal" id="receipt-content">
                <div class="rc-brand">
                    <h3>Tahu Bakso Morojoyo</h3>
                    <p>Jl. Contoh No.1, Malang</p>
                    <p id="rc-datetime">—</p>
                    <div class="rc-method-badge" id="rc-method">—</div>
                </div>
                <div class="rc-meta">
                    <div class="rc-meta-row"><span>No. Transaksi</span><span id="rc-trxno">—</span></div>
                    <div class="rc-meta-row"><span>Kasir</span><span>{{ auth()->user()->name }}</span></div>
                </div>
                <div class="rc-items-head"><span>Item</span><span>Subtotal</span></div>
                <div id="rc-items"></div>
                <div class="rc-sum">
                    <div class="rc-sum-row"><span>Subtotal</span><span id="rc-sub">Rp0</span></div>
                    <div class="rc-sum-row"><span>Diskon</span><span id="rc-disc">Rp0</span></div>
                    <div class="rc-sum-row rc-total"><span>TOTAL</span><span id="rc-total">Rp0</span></div>
                </div>
                <div class="rc-pay">
                    <div class="rc-pay-row"><span>Tunai / Dibayar</span><span id="rc-paid">Rp0</span></div>
                    <div class="rc-kembalian-box">
                        <span>Kembalian</span>
                        <strong id="rc-change">Rp0</strong>
                    </div>
                </div>
                <div class="rc-footer">
                    <p>Terima kasih sudah berbelanja!</p>
                    <p>Sampai jumpa kembali.</p>
                    <div class="rc-wave">- - - - - - - - - - -</div>
                </div>
            </div>
            <div class="receipt-btn-group">
                <button class="btn-rc btn-rc-print" onclick="window.print()">🖨 Print Struk</button>
                <button class="btn-rc btn-rc-sheets" id="btn-rc-sheets" onclick="syncToSheets()">📊 Sync ke
                    Sheets</button>
            </div>
            <div id="sync-status-receipt" class="sync-status"></div>
            <button class="btn-rc-new-trx" onclick="resetAndClose()">✓ Selesai & Transaksi Baru</button>
        </div>
    </div>

    {{-- ═══ MODAL 5 — KONFIGURASI GOOGLE SHEETS ═══ --}}
    <div class="overlay" id="modal-sheets" style="display:none" onclick="closeModalOutside(event,'modal-sheets')">
        <div class="modal modal-sm">
            <div class="modal-head">
                <div>
                    <h2>📊 Sync ke Google Sheets</h2>
                    <p>Kirim data penjualan ke spreadsheet owner.</p>
                </div>
                <button class="modal-close" onclick="closeModal('modal-sheets')">✕</button>
            </div>

            @if (auth()->user()->role === 'owner')
                <div class="sheets-config">
                    <label>Spreadsheet ID</label>
                    <input id="sheets-id-input" class="input-sm" type="text"
                        placeholder="1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgVE2upms" oninput="saveSheetsId()">
                    <div class="hint">Ambil dari URL: <code>spreadsheets/d/<strong>[ID]</strong>/edit</code>
                    </div>
                </div>
                <div class="sheets-config" style="margin-top:0">
                    <label>Periode Laporan</label>
                    <div style="display:flex;gap:8px;flex-wrap:wrap">
                        <button class="pay-btn active" id="periode-harian" onclick="setPeriode(this,'harian')">📅
                            Harian</button>
                        <button class="pay-btn" id="periode-mingguan" onclick="setPeriode(this,'mingguan')">📆
                            Mingguan</button>
                        <button class="pay-btn" id="periode-bulanan" onclick="setPeriode(this,'bulanan')">🗓
                            Bulanan</button>
                    </div>
                </div>
                <div id="sync-status-modal" class="sync-status"></div>
                <div class="modal-actions" style="margin-top:14px">
                    <button class="btn-modal-cancel" onclick="closeModal('modal-sheets')">Tutup</button>
                    <button class="btn-modal-confirm" id="btn-do-sync" onclick="doSync()">📊 Sync
                        Sekarang</button>
                </div>
            @else
                <div
                    style="background:#fef2f2;border:2px solid #fecaca;border-radius:12px;padding:16px;text-align:center;color:#991b1b">
                    <div style="font-size:24px;margin-bottom:8px">🔒</div>
                    <div style="font-weight:800;margin-bottom:4px">Akses Terbatas</div>
                    <div style="font-size:12px">Fitur sync Google Sheets hanya tersedia untuk Owner.</div>
                </div>
                <div class="modal-actions" style="margin-top:14px">
                    <button class="btn-modal-cancel" style="flex:1"
                        onclick="closeModal('modal-sheets')">Tutup</button>
                </div>
            @endif
        </div>
    </div>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        @php
            $menuData = $menus
                ->map(function ($menu) use ($stocks) {
                    return [
                        'id' => $menu->idMenu,
                        'name' => $menu->namaMenu,
                        'desc' => $menu->deskripsi,
                        'prices' => [
                            'normal' => (float) ($menu->hargas->first()?->harga_normal ?? 0),
                            'gofood' => (float) ($menu->hargas->first()?->harga_gofood ?? 0),
                            'shopeefood' => (float) ($menu->hargas->first()?->harga_shopeefood ?? 0),
                        ],
                        'details' => $menu->menuDetails
                            ->map(function ($d) use ($stocks) {
                                $stok = $stocks->get($d->id_pcs);
                                return [
                                    'pcs_id' => $d->id_pcs,
                                    'pcs_name' => $d->pcsTahu?->nama_pcs ?? 'Bahan tidak dikenal',
                                    'qty' => (int) $d->jumlah_pcs,
                                    'stock' => (int) ($stok?->jumlah_stok ?? 0),
                                ];
                            })
                            ->values()
                            ->toArray(),
                    ];
                })
                ->values()
                ->toArray();

            $stockList = $stocks
                ->map(function ($s) {
                    return [
                        'pcs_id' => $s->pcsTahu?->id_pcs ?? ($s->id_pcs ?? null),
                        'pcs_name' => $s->pcsTahu?->nama_pcs ?? '—',
                        'stock' => (int) $s->jumlah_stok,
                    ];
                })
                ->values()
                ->toArray();

            $todayTrxFull = \App\Models\Transaction::with('items')
                ->whereDate('created_at', today())
                ->when(auth()->user()?->cabang_id, fn($q, $idCabang) => $q->where('id_cabang', $idCabang))
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

            $stockSnapshot = $stocks
                ->map(function ($s) {
                    return [
                        'pcs_id' => $s->pcsTahu?->id_pcs ?? null,
                        'pcs_name' => $s->pcsTahu?->nama_pcs ?? '—',
                        'stok_saat_ini' => (int) $s->jumlah_stok,
                    ];
                })
                ->values()
                ->toArray();
        @endphp

        const MENU_DATA = @json($menuData);
        const STOCK_LIST = @json($stockList);
        const STOCK_SNAPSHOT = @json($stockSnapshot);
        const KASIR_NAME = @json($selectedShift?->user->name ?? (auth()->user()->name ?? '—'));
        const TANGGAL_HARI = @json(now()->translatedFormat('d F Y'));
        const CHECKOUT_URL = "{{ route('cashier.pos.checkout') }}";
        const SYNC_SHEETS_URL = "{{ url('/cashier/sync-sheets') }}";
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

        let todayTrxList = @json($todayTrxFull);
        let cart = [];
        let activeMenu = null;
        let modalQty = 1;
        let currentPay = 'normal';
        let currentPaymentMethod = 'cash';
        let lastTrxSnapshot = null;
        let stockMutasiMap = {};

        STOCK_SNAPSHOT.forEach(s => {
            stockMutasiMap[s.pcs_id] = {
                pcs_name: s.pcs_name,
                stok_awal: s.stok_saat_ini,
                total_dikurangi: 0,
                stok_akhir: s.stok_saat_ini,
            };
        });

        const BRAND = 'Warung Tahu Bakso';
        const ADDRESS = 'Jl. Contoh No.1, Malang';
        const METHOD_LABELS = {
            cash: 'Tunai',
            qris: 'QRIS',
            gofood: 'GoFood',
            shopeefood: 'ShopeeFood'
        };
        const KASIR_ID = @json(auth()->user()->id ?? null);

        function openRiwayatModal() {
            document.getElementById('riwayat-tanggal').textContent = TANGGAL_HARI;
            const list = document.getElementById('riwayat-list');
            if (!todayTrxList.length) {
                list.innerHTML = '<p style="color:#9ca3af;font-size:13px;padding:16px">Belum ada transaksi hari ini.</p>';
                openModal('modal-riwayat');
                return;
            }
            list.innerHTML = [...todayTrxList].reverse().map(t => `
                <div style="border:2px solid #f0d9a0;border-radius:12px;padding:14px;margin-bottom:10px;background:#FFFDF5">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                        <div>
                            <span style="font-weight:800;font-size:13px;color:#C0271A">${trxNo(t.id)}</span>
                            <span style="font-size:11px;color:#9ca3af;margin-left:8px">${t.created_at}</span>
                        </div>
                        <div style="display:flex;gap:8px;align-items:center">
                            <span style="font-size:12px;background:#C0271A;color:#F5C518;padding:2px 10px;border-radius:99px;font-weight:700">
                                ${METHOD_LABELS[t.payment_method] ?? t.payment_method}
                            </span>
                            <span style="font-weight:800;color:#C0271A">${fmt(t.total)}</span>
                            <button onclick="cetakUlang(${t.id})"
                                style="padding:5px 12px;border-radius:8px;border:2px solid #f0d9a0;background:#fff;font-size:11px;font-weight:700;cursor:pointer;color:#374151">
                                🖨 Cetak
                            </button>
                        </div>
                    </div>
                    <div style="font-size:12px;color:#6b7280">
                        ${(t.items || []).map(i => `${i.nama_item} ×${i.qty} = ${fmt(i.subtotal)}`).join(' &nbsp;·&nbsp; ')}
                    </div>
                    ${t.discount > 0 ? `<div style="font-size:11px;color:#d97706;margin-top:4px">Diskon: ${fmt(t.discount)}</div>` : ''}
                </div>
            `).join('');
            openModal('modal-riwayat');
        }

        function cetakUlang(trxId) {
            const t = todayTrxList.find(x => x.id === trxId);
            if (!t) return;
            document.getElementById('rc-datetime').textContent = t.created_at;
            document.getElementById('rc-trxno').textContent = trxNo(t.id);
            document.getElementById('rc-method').textContent = METHOD_LABELS[t.payment_method] ?? t.payment_method;
            document.getElementById('rc-items').innerHTML = (t.items || []).map(item => `
                <div class="rc-item-row">
                    <div>
                        <div class="rc-item-name">${item.nama_item}</div>
                        <div class="rc-item-detail">${item.qty} x ${fmt(item.unit_price)}</div>
                    </div>
                    <div class="rc-item-sub">${fmt(item.subtotal)}</div>
                </div>`).join('');
            document.getElementById('rc-sub').textContent = fmt(t.sub_total);
            document.getElementById('rc-disc').textContent = fmt(t.discount);
            document.getElementById('rc-total').textContent = fmt(t.total);
            document.getElementById('rc-paid').textContent = fmt(t.money_paid ?? t.total);
            document.getElementById('rc-change').textContent = fmt(Math.max(0, (t.money_paid ?? t.total) - t.total));
            closeModal('modal-riwayat');
            openModal('modal-receipt');
        }

        function setCheckoutPayment(btn, method) {
            document.querySelectorAll('#co-pay-methods .pay-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            setPaymentMethod(method);
            const disc = Math.max(0, parseFloat(document.getElementById('discount').value) || 0);
            const sub = cart.reduce((s, i) => s + i.subtotal, 0);
            const total = Math.max(0, sub - disc);
            const paid = parseFloat(document.getElementById('money-paid').value) || 0;
            document.getElementById('co-total').textContent = fmt(total);
            document.getElementById('co-change').textContent = fmt(Math.max(0, paid - total));
            document.getElementById('co-method').textContent = METHOD_LABELS[method] ?? method;
        }

        function setPaymentMethod(method) {
            currentPaymentMethod = method;
            currentPay = (method === 'cash' || method === 'qris') ? 'normal' : method;
            document.querySelectorAll('#pay-methods .pay-btn').forEach(b => {
                b.classList.toggle('active', b.dataset.method === method);
            });
            document.querySelectorAll('#co-pay-methods .pay-btn').forEach(b => {
                b.classList.toggle('active', b.dataset.method === method);
            });
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

        function updateStats() {
            const totalSales = todayTrxList.reduce((s, t) => s + (t.total || 0), 0);
            const totalTrx = todayTrxList.length;
            const totalItems = todayTrxList.reduce((s, t) =>
                s + (t.items || []).reduce((si, i) => si + (i.qty || 0), 0), 0);
            document.getElementById('stat-sales').textContent = fmt(totalSales);
            document.getElementById('stat-trx').textContent = totalTrx;
            document.getElementById('stat-items').textContent = totalItems;
        }

        function fmt(n) {
            return 'Rp' + Number(n || 0).toLocaleString('id-ID', {
                maximumFractionDigits: 0
            });
        }

        function mStkClass(s) {
            return s <= 5 ? 'mstk mstk-red' : s <= 10 ? 'mstk mstk-yellow' : s <= 20 ? 'mstk mstk-orange' : 'mstk mstk-ok';
        }

        function getPrice(menu) {
            const key = (currentPay === 'cash' || currentPay === 'qris') ? 'normal' : currentPay;
            return Number(menu.prices[key]) || Number(Object.values(menu.prices)[0]) || 0;
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

        function setPayment(btn, method) {
            document.querySelectorAll('.pay-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentPaymentMethod = method;
            currentPay = (method === 'cash' || method === 'qris') ? 'normal' : method;
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

        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        function closeModalOutside(event, id) {
            if (event.target.id === id) closeModal(id);
        }

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
                    stocksEl.innerHTML +=
                        `<div class="${mStkClass(d.stock)}"><h5>${d.pcs_name}</h5><span>Per porsi: ${d.qty} pcs &nbsp;·&nbsp; Sisa stok: <strong>${d.stock} pcs</strong>${d.stock <= 20 ? ' ⚠' : ''}</span></div>`;
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
                    details: activeMenu.details
                });
            }
            closeModal('modal-menu');
            renderCart();
        }

        function addCustomMenu() {
            const name = document.getElementById('custom-name').value.trim();
            const price = parseFloat(document.getElementById('custom-price').value) || 0;
            const qty = Math.max(1, parseInt(document.getElementById('custom-qty').value) || 1);
            if (!name) {
                alert('Masukkan nama item tambahan.');
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
                const bahanInfo = item.custom && item.details?.length ?
                    `<div style="font-size:10px;color:#6b7280;margin-top:2px">Bahan: ${item.details.map(d => `${d.pcs_name} ×${d.qty}`).join(', ')}</div>` :
                    '';
                div.innerHTML = `
                    <div>
                        <div class="ci-name">${item.name}${item.custom ? '<span class="custom-badge">custom</span>' : ''}</div>
                        <div class="ci-qty">x${item.qty} &times; ${fmt(item.unitPrice)}</div>
                        ${bahanInfo}
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
            cart.forEach(item => {
                (item.details || []).forEach(d => {
                    if (!d.pcs_id && !d.pcs_name) return;
                    const key = d.pcs_name;
                    if (!stockImpact[key]) stockImpact[key] = {
                        stock: d.stock,
                        used: 0
                    };
                    stockImpact[key].used += d.qty * item.qty;
                });
            });
            const coStocks = document.getElementById('co-stocks');
            coStocks.innerHTML = '';
            if (!Object.keys(stockImpact).length) {
                coStocks.innerHTML =
                    '<p style="color:#9ca3af;font-size:12px">Tidak ada bahan inventori yang terpengaruh.</p>';
            } else {
                Object.entries(stockImpact).forEach(([name, v]) => {
                    const sisa = v.stock - v.used;
                    coStocks.innerHTML +=
                        `<div class="${mStkClass(sisa)}" style="margin-bottom:6px"><h5>${name}</h5><span>Sisa: <strong>${v.stock} pcs</strong> − ${v.used} pcs = <strong>${sisa} pcs</strong>${sisa <= 20 ? ' ⚠' : ''}</span></div>`;
                });
            }
            document.getElementById('co-items').innerHTML = cart.map(item =>
                `
                <li><div><div class="cn">${item.name}</div><div class="cq">x${item.qty}</div></div><div class="cs">${fmt(item.subtotal)}</div></li>`
            ).join('');
            if (!['cash', 'qris', 'gofood', 'shopeefood'].includes(currentPaymentMethod)) {
                currentPaymentMethod = 'cash';
                currentPay = 'normal';
            }
            document.getElementById('co-total').textContent = fmt(total);
            document.getElementById('co-change').textContent = fmt(change);
            document.getElementById('co-method').textContent = METHOD_LABELS[currentPaymentMethod] ?? currentPaymentMethod;
            const coPayMethods = document.getElementById('co-pay-methods');
            if (coPayMethods) {
                const methods = ['cash', 'qris', 'gofood', 'shopeefood'];
                const labels = {
                    cash: 'Cash',
                    qris: 'QRIS',
                    gofood: 'GoFood',
                    shopeefood: 'ShopeeFood'
                };
                coPayMethods.innerHTML = methods.map(m =>
                    `<button class="pay-btn ${currentPaymentMethod === m ? 'active' : ''}" onclick="setCheckoutPayment(this, '${m}')" data-method="${m}">${labels[m]}</button>`
                ).join('');
            }
            openModal('modal-checkout');
        }

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
                formData.append('payment_method', currentPaymentMethod);
                formData.append('discount', disc);
                formData.append('cart', JSON.stringify(cart));
                const response = await fetch(CHECKOUT_URL, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                const result = await response.json();
                if (!response.ok || result.success === false) throw new Error(result.message || 'Transaksi gagal.');
                const newId = result.id || Date.now();
                lastTrxSnapshot = {
                    id: newId,
                    created_at: nowStr(),
                    payment_method: currentPaymentMethod,
                    sub_total: sub,
                    discount: disc,
                    total,
                    money_paid: paid,
                    kasir: KASIR_NAME,
                    items: cart.map(item => ({
                        nama_item: item.name,
                        qty: item.qty,
                        unit_price: item.unitPrice,
                        subtotal: item.subtotal,
                        is_custom: item.custom,
                        bahan_dikurangi: (item.details || []).map(d => ({
                            pcs_id: d.pcs_id,
                            pcs_name: d.pcs_name,
                            qty_per_porsi: d.qty,
                            total_dikurangi: d.qty * item.qty,
                        })),
                    })),
                };
                cart.forEach(item => {
                    (item.details || []).forEach(d => {
                        if (!d.pcs_id) return;
                        if (!stockMutasiMap[d.pcs_id]) {
                            stockMutasiMap[d.pcs_id] = {
                                pcs_name: d.pcs_name,
                                stok_awal: d.stock,
                                total_dikurangi: 0,
                                stok_akhir: d.stock
                            };
                        }
                        const reduced = d.qty * item.qty;
                        stockMutasiMap[d.pcs_id].total_dikurangi += reduced;
                        stockMutasiMap[d.pcs_id].stok_akhir = stockMutasiMap[d.pcs_id].stok_awal -
                            stockMutasiMap[d.pcs_id].total_dikurangi;
                    });
                });
                todayTrxList = [...todayTrxList, lastTrxSnapshot];
                updateStats();
                closeModal('modal-checkout');
                showReceipt(sub, disc, total, paid, change, newId);
                await refreshStocks();
                await autoSyncToSheets();
                document.getElementById('ajax-error').style.display = 'none';
            } catch (err) {
                document.getElementById('ajax-error').textContent = err.message || 'Terjadi kesalahan. Coba lagi';
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
                if (newStockGrid) document.querySelector('#stock-grid').innerHTML = newStockGrid.innerHTML;
            } catch (err) {
                console.error('Gagal refresh stok:', err);
            }
        }

        function showReceipt(sub, disc, total, paid, change, id) {
            document.getElementById('rc-datetime').textContent = nowStr();
            document.getElementById('rc-trxno').textContent = trxNo(id);
            document.getElementById('rc-method').textContent = METHOD_LABELS[currentPaymentMethod] ?? currentPaymentMethod;
            document.getElementById('rc-items').innerHTML = cart.map(item => `
                <div class="rc-item-row">
                    <div>
                        <div class="rc-item-name">${item.name}</div>
                        <div class="rc-item-detail">${item.qty} x ${fmt(item.unitPrice)}</div>
                        ${item.custom && item.details?.length ? `<div style="font-size:10px;color:#9ca3af">Bahan: ${item.details.map(d=>`${d.pcs_name}×${d.qty}`).join(', ')}</div>` : ''}
                    </div>
                    <div class="rc-item-sub">${fmt(item.subtotal)}</div>
                </div>`).join('');
            document.getElementById('rc-sub').textContent = fmt(sub);
            document.getElementById('rc-disc').textContent = fmt(disc);
            document.getElementById('rc-total').textContent = fmt(total);
            document.getElementById('rc-paid').textContent = fmt(paid);
            document.getElementById('rc-change').textContent = fmt(change);
            const st = document.getElementById('sync-status-receipt');
            st.style.display = 'none';
            st.className = 'sync-status';
            openModal('modal-receipt');
        }

        function resetAndClose() {
            cart = [];
            document.getElementById('discount').value = 0;
            document.getElementById('money-paid').value = 0;
            renderCart();
            closeModal('modal-receipt');
            const lastSyncDate = localStorage.getItem('synced_date');
            if (lastSyncDate !== TANGGAL_HARI) {
                localStorage.removeItem('synced_trx_ids');
                localStorage.setItem('synced_date', TANGGAL_HARI);
            }
        }

        function openLaporanModal() {
            const totalSales = todayTrxList.reduce((s, t) => s + (t.total || 0), 0);
            const totalDisc = todayTrxList.reduce((s, t) => s + (t.discount || 0), 0);
            const totalTrx = todayTrxList.length;
            const totalItems = todayTrxList.reduce((s, t) => s + (t.items || []).reduce((si, i) => si + (i.qty || 0), 0),
                0);
            document.getElementById('lap-tanggal').textContent = TANGGAL_HARI;
            document.getElementById('lap-sales').textContent = fmt(totalSales);
            document.getElementById('lap-trx').textContent = totalTrx;
            document.getElementById('lap-items').textContent = totalItems;
            document.getElementById('lap-disc').textContent = fmt(totalDisc);
            const st = document.getElementById('sync-status-laporan');
            st.style.display = 'none';
            st.className = 'sync-status';
            openModal('modal-laporan');
        }

        let selectedPeriode = 'harian';

        function setPeriode(btn, periode) {
            document.querySelectorAll('#modal-sheets .pay-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            selectedPeriode = periode;
        }

        /**
         * Menghitung range tanggal berdasarkan periode yang dipilih
         */
        function getDateRangeByPeriode(periode) {
            const today = new Date();
            let startDate, endDate;

            if (periode === 'harian') {
                startDate = new Date(today);
                startDate.setHours(0, 0, 0, 0);
                endDate = new Date(today);
                endDate.setHours(23, 59, 59, 999);
                return {
                    startDate,
                    endDate,
                    label: today.toLocaleDateString('id-ID', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    })
                };
            }

            if (periode === 'mingguan') {
                const dayOfWeek = today.getDay();
                startDate = new Date(today);
                startDate.setDate(today.getDate() - (dayOfWeek === 0 ? 6 : dayOfWeek - 1));
                startDate.setHours(0, 0, 0, 0);
                endDate = new Date(startDate);
                endDate.setDate(startDate.getDate() + 6);
                endDate.setHours(23, 59, 59, 999);
                return {
                    startDate,
                    endDate,
                    label: `Minggu ${startDate.getDate()} - ${endDate.getDate()} ${endDate.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' })}`
                };
            }

            if (periode === 'bulanan') {
                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                startDate.setHours(0, 0, 0, 0);
                endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                endDate.setHours(23, 59, 59, 999);
                return {
                    startDate,
                    endDate,
                    label: today.toLocaleDateString('id-ID', {
                        month: 'long',
                        year: 'numeric'
                    })
                };
            }
        }

        /**
         * Parse tanggal transaksi dari format string ke Date object
         */
        function parseTransactionDate(dateStr) {
            const months = {
                'Jan': 0,
                'Feb': 1,
                'Mar': 2,
                'Apr': 3,
                'Mei': 4,
                'Mei.': 4,
                'Juni': 5,
                'Jun': 5,
                'Jul': 6,
                'Agu': 7,
                'Agus': 7,
                'Sep': 8,
                'Okt': 9,
                'Nov': 10,
                'Des': 11,
                'Desember': 11,
                'Januari': 0,
                'Februari': 1,
                'Maret': 2,
                'April': 3,
                'Juli': 6,
                'September': 8,
                'Oktober': 9
            };

            const trimmed = String(dateStr || '').trim();
            if (!trimmed) return new Date(NaN);

            const isoDate = new Date(trimmed);
            if (!isNaN(isoDate.getTime())) return isoDate;

            const parts = trimmed.split(' ');
            if (parts.length >= 4) {
                const day = parseInt(parts[0], 10);
                const monthStr = parts[1];
                const year = parseInt(parts[2], 10);
                const time = parts[3].split(':');
                const hour = parseInt(time[0], 10);
                const minute = parseInt(time[1] || '0', 10);
                const month = months[monthStr] !== undefined ? months[monthStr] : 0;
                return new Date(year, month, day, hour, minute, 0);
            }

            if (trimmed.includes('-')) {
                const [datePart, timePart] = trimmed.split(' ');
                const [year, month, day] = datePart.split('-').map(x => parseInt(x, 10));
                const [hour, minute] = (timePart || '00:00').split(':').map(x => parseInt(x, 10));
                return new Date(year, month - 1, day, hour, minute, 0);
            }

            return new Date(trimmed);
        }

        /**
         * Build payload untuk dikirim ke Google Sheets dengan filter periode
         */
        function buildSheetsPayload(periode = 'harian') {
            const {
                startDate,
                endDate,
                label
            } = getDateRangeByPeriode(periode);

            // Filter transaksi berdasarkan periode
            const filteredTrx = todayTrxList.filter(t => {
                const trxDate = parseTransactionDate(t.created_at);
                return trxDate >= startDate && trxDate <= endDate;
            });

            // RINGKASAN
            const ringkasan = {
                periode: periode.charAt(0).toUpperCase() + periode.slice(1),
                tanggal: label,
                kasir: KASIR_NAME,
                jumlah_transaksi: filteredTrx.length,
                item_terjual: filteredTrx.reduce((s, t) => s + (t.items || []).reduce((si, i) => si + (i.qty || 0), 0),
                    0),
                total_diskon: filteredTrx.reduce((s, t) => s + (t.discount || 0), 0),
                total_penjualan: filteredTrx.reduce((s, t) => s + (t.total || 0), 0),
            };

            // DETAIL TRANSAKSI
            const detailRows = [];
            filteredTrx.forEach(t => {
                (t.items || []).forEach(item => {
                    const bahanStr = (item.bahan_dikurangi || []).map(b =>
                        `${b.pcs_name} -${b.total_dikurangi}pcs`).join(' | ') || (item.is_custom ?
                        'Custom' : '—');

                    detailRows.push({
                        periode: periode.charAt(0).toUpperCase() + periode.slice(1),
                        tanggal_laporan: label,
                        tanggal_transaksi: t.created_at,
                        no_transaksi: trxNo(t.id),
                        kasir: t.kasir || KASIR_NAME,
                        metode_bayar: METHOD_LABELS[t.payment_method] ?? t.payment_method,
                        nama_item: item.nama_item,
                        is_custom: item.is_custom ? 'Ya' : 'Tidak',
                        qty: item.qty,
                        harga_satuan: item.unit_price,
                        subtotal_item: item.subtotal,
                        diskon_transaksi: t.discount,
                        total_transaksi: t.total,
                        bahan_dikurangi: bahanStr,
                    });
                });
            });

            // MUTASI STOK
            const mutasiMap = {};
            filteredTrx.forEach(t => {
                (t.items || []).forEach(item => {
                    (item.bahan_dikurangi || []).forEach(b => {
                        if (!b.pcs_id) return;
                        if (!mutasiMap[b.pcs_id]) {
                            const snap = STOCK_SNAPSHOT.find(s => s.pcs_id == b.pcs_id);
                            mutasiMap[b.pcs_id] = {
                                pcs_name: b.pcs_name,
                                stok_awal: snap?.stok_saat_ini ?? 0,
                                total_dikurangi: 0
                            };
                        }
                        mutasiMap[b.pcs_id].total_dikurangi += b.total_dikurangi;
                    });
                });
            });

            const mutasiRows = Object.entries(mutasiMap).map(([pcsId, v]) => ({
                periode: periode.charAt(0).toUpperCase() + periode.slice(1),
                tanggal_laporan: label,
                pcs_id: pcsId,
                nama_bahan: v.pcs_name,
                stok_awal: v.stok_awal,
                total_dikurangi: v.total_dikurangi,
                stok_akhir: v.stok_awal - v.total_dikurangi,
            }));

            return {
                periode,
                ringkasan,
                detail_transaksi: detailRows,
                mutasi_stok: mutasiRows
            };
        }

        function loadSheetsConfig() {
            const id = localStorage.getItem('sheets_id') || '';
            const inputId = document.getElementById('sheets-id-input');
            if (inputId) inputId.value = id;
        }

        function saveSheetsId() {
            localStorage.setItem('sheets_id', document.getElementById('sheets-id-input').value.trim());
        }

        function openSheetsModal() {
            loadSheetsConfig();
            const st = document.getElementById('sync-status-modal');
            st.style.display = 'none';
            st.className = 'sync-status';
            openModal('modal-sheets');
        }

        function showSyncStatus(elId, type, msg) {
            const el = document.getElementById(elId);
            el.className = 'sync-status ' + (type === 'ok' ? 'sync-ok' : type === 'err' ? 'sync-err' : 'sync-wait');
            el.textContent = msg;
            el.style.display = '';
        }

        function syncToSheets() {
            loadSheetsConfig();
            closeModal('modal-receipt');
            closeModal('modal-laporan');
            const st = document.getElementById('sync-status-modal');
            st.style.display = 'none';
            st.className = 'sync-status';
            openModal('modal-sheets');
        }

        async function doSync() {
            const spreadsheetId = (localStorage.getItem('sheets_id') || '').trim();
            if (!spreadsheetId) {
                showSyncStatus('sync-status-modal', 'err', '❌ Masukkan Spreadsheet ID terlebih dahulu.');
                return;
            }
            const btn = document.getElementById('btn-do-sync');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner"></span> Mengirim data...';
            showSyncStatus('sync-status-modal', 'wait', '⏳ Menghubungkan ke Google Sheets...');
            const payload = {
                spreadsheet_id: spreadsheetId,
                ...buildSheetsPayload(selectedPeriode) // ✅ BARU: dengan parameter periode
            };
            try {
                const res = await fetch(SYNC_SHEETS_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload),
                });

                if (res.status === 404) {
                    showSyncStatus('sync-status-modal', 'err', '⚠️ Endpoint /cashier/sync-sheets belum ada.');
                    return;
                }

                const data = await res.json();
                if (!res.ok || data.success === false) throw new Error(data.message || 'Sync gagal.');

                const periodeLabel = selectedPeriode.charAt(0).toUpperCase() + selectedPeriode.slice(1);
                showSyncStatus('sync-status-modal', 'ok',
                    `✅ Laporan ${periodeLabel} berhasil dikirim ke Google Sheets!`);
            } catch (err) {
                showSyncStatus('sync-status-modal', 'err', '❌ ' + (err.message || 'Terjadi kesalahan saat sync.'));
            } finally {
                btn.disabled = false;
                btn.innerHTML = '📊 Sync Sekarang';
            }
        }

        async function autoSyncToSheets() {
            const spreadsheetId = (localStorage.getItem('sheets_id') || '').trim();
            if (!spreadsheetId) return;
            try {
                const payload = {
                    spreadsheet_id: spreadsheetId,
                    ...buildSheetsPayload('harian') // ✅ BARU: selalu kirim laporan harian
                };

                await fetch(SYNC_SHEETS_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload),
                });
            } catch (err) {
                console.warn('Auto-sync Sheets gagal (silent):', err.message);
            }
        }

        // ── SHIFT STATE ──
        const SHIFT_KEY = 'shift_state_' + TANGGAL_HARI + '_' + (KASIR_ID ?? 'guest');
        const SHIFT_TIMES_KEY = 'shift_times_' + TANGGAL_HARI + '_' + (KASIR_ID ?? 'guest');
        let shiftState = localStorage.getItem(SHIFT_KEY) || 'belum';
        let shiftTimes = loadShiftTimes();

        function normalizeShiftTimes(data) {
            return {
                shift1: {
                    start: data?.shift1?.start || null,
                    end: data?.shift1?.end || null,
                },
                shift2: {
                    start: data?.shift2?.start || null,
                    end: data?.shift2?.end || null,
                },
            };
        }

        function loadShiftTimes() {
            const saved = localStorage.getItem(SHIFT_TIMES_KEY);
            if (!saved) return normalizeShiftTimes({});
            try {
                return normalizeShiftTimes(JSON.parse(saved));
            } catch {
                return normalizeShiftTimes({});
            }
        }

        function saveShiftTimes() {
            localStorage.setItem(SHIFT_TIMES_KEY, JSON.stringify(shiftTimes));
        }

        function formatTimeLabel(isoString) {
            if (!isoString) return '—';
            const d = new Date(isoString);
            return d.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function filterTransactionsForShift(transactions, shiftKey) {
            const shift = shiftTimes[shiftKey];
            if (!shift || !shift.start) return [];
            const start = new Date(shift.start);
            const end = shift.end ? new Date(shift.end) : new Date();
            return transactions.filter(trx => {
                const trxDate = parseTransactionDate(trx.created_at);
                return trxDate >= start && trxDate <= end;
            });
        }

        function normalizePaymentMethod(method) {
            const m = String(method || '').trim().toLowerCase();
            if (m === 'tunai') return 'cash';
            if (m === 'go food' || m === 'gofood') return 'gofood';
            if (m === 'shopee food' || m === 'shopeefood') return 'shopeefood';
            if (m === 'qris') return 'qris';
            if (m === 'cash') return 'cash';
            return m;
        }

        function computeMethodTotals(transactions) {
            return ['cash', 'qris', 'gofood', 'shopeefood'].reduce((acc, method) => {
                acc[method] = transactions.reduce((sum, trx) => {
                    const trxMethod = normalizePaymentMethod(trx.payment_method);
                    return sum + ((trxMethod === method) ? (trx.total || 0) : 0);
                }, 0);
                return acc;
            }, {
                cash: 0,
                qris: 0,
                gofood: 0,
                shopeefood: 0
            });
        }

        function computeCurrentShiftMethodTotals() {
            if (shiftState === 'shift1_aktif') {
                return computeMethodTotals(filterTransactionsForShift(todayTrxList, 'shift1'));
            }
            if (shiftState === 'shift2_aktif') {
                return computeMethodTotals(filterTransactionsForShift(todayTrxList, 'shift2'));
            }
            return {
                cash: 0,
                qris: 0,
                gofood: 0,
                shopeefood: 0
            };
        }

        function computeShiftTotal(shiftKey) {
            const shift = shiftTimes[shiftKey];
            if (!shift || !shift.start) return 0;
            const start = new Date(shift.start);
            const end = shift.end ? new Date(shift.end) : new Date();
            return todayTrxList.reduce((sum, trx) => {
                const trxDate = parseTransactionDate(trx.created_at);
                if (trxDate >= start && trxDate <= end) {
                    return sum + (trx.total || 0);
                }
                return sum;
            }, 0);
        }

        function buildShiftLabel() {
            if (shiftState === 'belum') {
                return 'Shift belum dimulai';
            }
            if (shiftState === 'shift1_aktif') {
                return `Shift 1 aktif sejak ${formatTimeLabel(shiftTimes.shift1.start)}`;
            }
            if (shiftState === 'shift1_selesai') {
                return `Shift 1 selesai ${formatTimeLabel(shiftTimes.shift1.end)} · Siap mulai Shift 2`;
            }
            if (shiftState === 'shift2_aktif') {
                return `Shift 2 aktif sejak ${formatTimeLabel(shiftTimes.shift2.start)}`;
            }
            if (shiftState === 'shift2_selesai') {
                return `Shift 2 selesai ${formatTimeLabel(shiftTimes.shift2.end)}`;
            }
            return 'Status shift tidak diketahui';
        }

        const SHIFT_CONFIG = {
            belum: {
                label: 'Awali Shift 1',
                bg: '#16a34a',
                next: 'shift1_aktif'
            },
            shift1_aktif: {
                label: 'Akhiri Shift 1',
                bg: '#dc2626',
                next: 'shift1_selesai'
            },
            shift1_selesai: {
                label: 'Mulai Shift 2',
                bg: '#16a34a',
                next: 'shift2_aktif'
            },
            shift2_aktif: {
                label: 'Akhiri Shift 2',
                bg: '#dc2626',
                next: 'shift2_selesai'
            },
            shift2_selesai: {
                label: 'Shift Selesai',
                bg: '#6b7280',
                next: null
            },
        };

        function renderShiftButton() {
            const btn = document.getElementById('btn-shift');
            if (!btn) return;
            const cfg = SHIFT_CONFIG[shiftState] || SHIFT_CONFIG['belum'];
            btn.textContent = cfg.label;
            btn.style.background = cfg.bg;
            btn.disabled = cfg.next === null;
            btn.style.opacity = cfg.next === null ? '0.6' : '1';
            btn.style.cursor = cfg.next === null ? 'not-allowed' : 'pointer';
            renderShiftReport();
        }

        function handleShiftButton() {
            const cfg = SHIFT_CONFIG[shiftState];
            if (!cfg || !cfg.next) return;

            const confirmMsg = {
                belum: 'Mulai Shift 1 sekarang?',
                shift1_aktif: 'Akhiri Shift 1 sekarang?',
                shift1_selesai: 'Mulai Shift 2 sekarang?',
                shift2_aktif: 'Akhiri Shift 2 sekarang?',
            };

            if (!confirm(confirmMsg[shiftState] || 'Lanjutkan?')) return;

            const now = new Date().toISOString();
            if (shiftState === 'belum') {
                shiftTimes.shift1.start = now;
                shiftState = 'shift1_aktif';
            } else if (shiftState === 'shift1_aktif') {
                shiftTimes.shift1.end = now;
                shiftState = 'shift1_selesai';
            } else if (shiftState === 'shift1_selesai') {
                shiftTimes.shift2.start = now;
                shiftState = 'shift2_aktif';
            } else if (shiftState === 'shift2_aktif') {
                shiftTimes.shift2.end = now;
                shiftState = 'shift2_selesai';
            }

            localStorage.setItem(SHIFT_KEY, shiftState);
            saveShiftTimes();
            renderShiftButton();
        }

        function resetShiftReportData() {
            if (!confirm('Reset ulang laporan shift untuk demo?')) return;
            shiftState = 'belum';
            shiftTimes = normalizeShiftTimes({});
            localStorage.removeItem(SHIFT_KEY);
            localStorage.removeItem(SHIFT_TIMES_KEY);
            renderShiftButton();
            renderShiftReport();
        }

        function renderShiftReport() {
            const totals = computeCurrentShiftMethodTotals();
            document.getElementById('shift-cash').textContent = fmt(totals.cash);
            document.getElementById('shift-qris').textContent = fmt(totals.qris);
            document.getElementById('shift-gofood').textContent = fmt(totals.gofood);
            document.getElementById('shift-shopeefood').textContent = fmt(totals.shopeefood);
            document.getElementById('shift1-total').textContent = fmt(computeShiftTotal('shift1'));
            document.getElementById('shift2-total').textContent = fmt(computeShiftTotal('shift2'));
            document.getElementById('shift-current').textContent = buildShiftLabel();
        }

        function toggleShiftReport() {
            const el = document.getElementById('shift-report');
            if (!el) return;
            const isOpen = el.style.display !== 'none';
            el.style.display = isOpen ? 'none' : 'grid';
            if (!isOpen) renderShiftReport();
        }

        function updateStats() {
            const totalSales = todayTrxList.reduce((s, t) => s + (t.total || 0), 0);
            const totalTrx = todayTrxList.length;
            const totalItems = todayTrxList.reduce((s, t) =>
                s + (t.items || []).reduce((si, i) => si + (i.qty || 0), 0), 0);
            document.getElementById('stat-sales').textContent = fmt(totalSales);
            document.getElementById('stat-trx').textContent = totalTrx;
            document.getElementById('stat-items').textContent = totalItems;
            renderShiftReport();
        }

        // Init shift button saat halaman dimuat
        renderShiftButton();

        // Init
        renderMenuPrices();
        renderCart();
        loadSheetsConfig();

        (function initSyncedIds() {
            const today = TANGGAL_HARI;
            const lastSyncDate = localStorage.getItem('synced_date');
            if (lastSyncDate !== today) {
                localStorage.removeItem('synced_trx_ids');
                localStorage.setItem('synced_date', today);
            }
            const existingIds = todayTrxList.map(t => t.id);
            const alreadySynced = JSON.parse(localStorage.getItem('synced_trx_ids') || '[]');
            const merged = [...new Set([...alreadySynced, ...existingIds])];
            localStorage.setItem('synced_trx_ids', JSON.stringify(merged));
        })();

        const CREATE_SHEET_URL = "{{ route('cashier.create-spreadsheet') }}";

        async function createNewSpreadsheet() {
            const btn = document.getElementById('btn-create-sheet');
            btn.disabled = true;
            btn.textContent = 'Membuat spreadsheet...';
            showSyncStatus('sync-status-modal', 'wait', '⏳ Membuat spreadsheet baru...');
            try {
                const res = await fetch(CREATE_SHEET_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);
                document.getElementById('sheets-id-input').value = data.spreadsheet_id;
                saveSheetsId();
                document.getElementById('sync-status-modal').innerHTML =
                    `✅ ${data.message} &nbsp;<a href="${data.url}" target="_blank" style="color:#C0271A;font-weight:700">→ Buka Spreadsheet</a>`;
                document.getElementById('sync-status-modal').style.display = '';
            } catch (err) {
                showSyncStatus('sync-status-modal', 'err', '❌ ' + err.message);
            } finally {
                btn.disabled = false;
                btn.textContent = '+ Buat Spreadsheet Baru (Auto Share ke Akun Saya)';
            }
        }
    </script>

</x-layouts.app>
