@props(['title' => 'POS Kasir'])

<x-layouts.app :title="$title">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&display=swap');

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

        .btn-laporan-topbar {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 99px;
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
            font-family: inherit;
        }

        .btn-laporan-topbar:hover {
            background: #dcfce7;
        }

        .btn-laporan-topbar svg {
            width: 14px;
            height: 14px;
        }

        /* Google Sheets sync button */
        .btn-sheets-topbar {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 99px;
            background: #f0f9ff;
            color: #0369a1;
            border: 1px solid #bae6fd;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
            font-family: inherit;
        }

        .btn-sheets-topbar:hover {
            background: #e0f2fe;
        }

        .btn-sheets-topbar:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-sheets-topbar svg {
            width: 14px;
            height: 14px;
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
            font-weight: 700;
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
            font-weight: 600;
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

        /* ── 3-LEVEL STOCK COLORS ── */
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

        /* ≤5 → merah */
        .stk-red {
            background: #fef2f2;
            border-color: #fca5a5;
            color: #991b1b;
        }

        /* ≤10 → kuning */
        .stk-yellow {
            background: #fffbeb;
            border-color: #fde68a;
            color: #92400e;
        }

        /* ≤20 → oranye */
        .stk-orange {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #c2410c;
        }

        /* >20 → hijau */
        .stk-ok {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #166534;
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

        /* ── STRUK ── */
        .receipt-thermal {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 20px 18px;
            font-family: 'IBM Plex Mono', 'Courier New', monospace;
            font-size: 12px;
            color: #111827;
        }

        .rc-brand {
            text-align: center;
            padding-bottom: 12px;
            margin-bottom: 12px;
            border-bottom: 1px dashed #9ca3af;
        }

        .rc-brand h3 {
            font-size: 15px;
            font-weight: 600;
            margin: 0 0 3px;
            font-family: 'IBM Plex Mono', monospace;
        }

        .rc-brand p {
            font-size: 11px;
            color: #6b7280;
            margin: 2px 0 0;
        }

        .rc-brand .rc-method-badge {
            display: inline-block;
            margin-top: 6px;
            padding: 2px 10px;
            border: 1px solid #374151;
            border-radius: 2px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #111827;
        }

        .rc-meta {
            padding-bottom: 10px;
            margin-bottom: 10px;
            border-bottom: 1px dashed #9ca3af;
        }

        .rc-meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            padding: 2px 0;
            color: #374151;
        }

        .rc-meta-row span:first-child {
            color: #6b7280;
        }

        .rc-items-head {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #6b7280;
            padding-bottom: 5px;
            border-bottom: 1px solid #d1d5db;
            margin-bottom: 5px;
        }

        .rc-item-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: 5px 0;
            border-bottom: 1px dashed #e5e7eb;
        }

        .rc-item-name {
            font-size: 12px;
            font-weight: 500;
            color: #111827;
        }

        .rc-item-detail {
            font-size: 10px;
            color: #9ca3af;
            margin-top: 1px;
        }

        .rc-item-sub {
            font-size: 12px;
            font-weight: 500;
            white-space: nowrap;
        }

        .rc-sum {
            padding: 10px 0 8px;
            border-bottom: 1px dashed #9ca3af;
        }

        .rc-sum-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            padding: 2px 0;
            color: #6b7280;
        }

        .rc-sum-row.rc-total {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px solid #374151;
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
            border: 1px solid #059669;
            border-radius: 3px;
            background: #f0fdf4;
        }

        .rc-kembalian-box span {
            font-size: 11px;
            color: #166534;
            font-weight: 500;
        }

        .rc-kembalian-box strong {
            font-size: 13px;
            font-weight: 600;
            color: #059669;
        }

        .rc-footer {
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px dashed #9ca3af;
            text-align: center;
        }

        .rc-footer p {
            font-size: 11px;
            color: #6b7280;
            margin: 2px 0;
        }

        .rc-footer .rc-wave {
            color: #9ca3af;
            letter-spacing: .2em;
            font-size: 10px;
            margin-top: 6px;
        }

        /* ── STRUK ACTIONS (tanpa PDF/Excel) ── */
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
            font-weight: 600;
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background .15s;
            border: 1.5px solid;
            text-align: center;
        }

        .btn-rc-print {
            background: #fff;
            border-color: #e5e7eb;
            color: #374151;
        }

        .btn-rc-print:hover {
            background: #f3f4f6;
        }

        .btn-rc-sheets {
            background: #f0f9ff;
            border-color: #bae6fd;
            color: #0369a1;
        }

        .btn-rc-sheets:hover {
            background: #e0f2fe;
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
            border: 1.5px solid #bbf7d0;
            background: #ecfdf5;
            color: #047857;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background .15s;
        }

        .btn-rc-new-trx:hover {
            background: #d1fae5;
        }

        /* ── MODAL LAPORAN ── */
        .laporan-modal-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 12px;
        }

        .lap-stat {
            background: #f8fafc;
            border-radius: 12px;
            padding: 14px 16px;
            border: 1px solid #e9eaec;
        }

        .lap-stat .ls-label {
            font-size: 11px;
            color: #9ca3af;
            font-weight: 500;
        }

        .lap-stat .ls-val {
            font-size: 20px;
            font-weight: 700;
            margin-top: 4px;
        }

        .btn-lap-action {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px;
            border-radius: 11px;
            border: 1.5px solid;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background .15s;
            margin-bottom: 8px;
        }

        .btn-lap-sheets {
            background: #f0f9ff;
            border-color: #bae6fd;
            color: #0369a1;
        }

        .btn-lap-sheets:hover {
            background: #e0f2fe;
        }

        .btn-lap-sheets:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* ── GOOGLE SHEETS MODAL ── */
        .sheets-config {
            background: #f8fafc;
            border-radius: 12px;
            padding: 14px 16px;
            border: 1px solid #e9eaec;
            margin-bottom: 12px;
        }

        .sheets-config label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #9ca3af;
            display: block;
            margin-bottom: 6px;
        }

        .sheets-config .hint {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 4px;
        }

        .sheets-config .hint code {
            background: #e9eaec;
            padding: 1px 5px;
            border-radius: 4px;
            font-family: 'IBM Plex Mono', monospace;
        }

        .sync-status {
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 12px;
            font-weight: 500;
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
            background: #f0f9ff;
            color: #0369a1;
            border: 1px solid #bae6fd;
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

        @media print {

            /* Sembunyikan panel utama */
            .pos-wrap,
            .topbar,
            #modal-menu,
            #modal-checkout,
            #modal-laporan,
            #modal-sheets {
                display: none !important;
            }

            /* Overlay struk — jadikan static, hilangkan backdrop */
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

            /* Sembunyikan tombol di struk */
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
                    <h1>POS Kasir</h1>
                    <p>Sistem penjualan harian, inventori stok, dan checkout.</p>
                </div>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                    <span class="badge">{{ $selectedShift?->user->name ?? auth()->user()->name }}</span>
                    <span class="badge badge-green">{{ $selectedShift ? 'Kasir shift' : auth()->user()->role }}</span>

                    <button type="button" class="btn-laporan-topbar" onclick="openLaporanModal()">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Laporan Hari Ini
                    </button>

                    {{-- Tombol sync Google Sheets --}}
                    <button type="button" class="btn-sheets-topbar" id="btn-sheets-topbar" onclick="openSheetsModal()">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                        </svg>
                        Sync Sheets
                    </button>
                    <button type="button" class="btn-laporan-topbar" onclick="openRiwayatModal()"
                        style="background:#f5f3ff;color:#6d28d9;border-color:#ddd6fe">
                        📋 Riwayat
                    </button>

                    {{-- Tambahkan di dalam div topbar, sebelum tombol Keluar --}}
                    <a href="{{ route('attendance.index') }}"
                        style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:99px;
          background:#fef3c7;color:#92400e;border:1px solid #fde68a;font-size:12px;
          font-weight:600;text-decoration:none;transition:background .15s"
                        onmouseover="this.style.background='#fde68a'" onmouseout="this.style.background='#fef3c7'">
                        👤 Absensi
                    </a>

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

            {{-- ⚠ STOCK WARNING BANNER — muncul jika ada stok ≤ 20 --}}
            @php
                $lowStocks = $stocks->filter(fn($s) => $s->jumlah_stok <= 20);
            @endphp
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
                                    {{ $s->pcsTahu?->nama_pcs ?? '—' }}:
                                    {{ $stok }} pcs
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
            {{-- ═══ MODAL 6 — RIWAYAT TRANSAKSI ═══ --}}
            <div class="overlay" id="modal-riwayat" style="display:none"
                onclick="closeModalOutside(event,'modal-riwayat')">
                <div class="modal modal-wide">
                    <div class="modal-head">
                        <div>
                            <h2>📋 Riwayat Transaksi Hari Ini</h2>
                            <p id="riwayat-tanggal">—</p>
                        </div>
                        <button class="modal-close" onclick="closeModal('modal-riwayat')">✕</button>
                    </div>
                    <div id="riwayat-list" style="max-height:60vh;overflow-y:auto"></div>
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

            {{-- Stok inventori (3-level color) --}}
            <div class="card">
                <div class="card-title">Stok Inventori</div>
                <div class="stock-grid" id="stock-grid">
                    @foreach ($stocks as $s)
                        @php $stok = $s->jumlah_stok; @endphp
                        <div
                            class="stk {{ $stok <= 5 ? 'stk-red' : ($stok <= 10 ? 'stk-yellow' : ($stok <= 20 ? 'stk-orange' : 'stk-ok')) }}">
                            <h4>{{ $s->pcsTahu?->nama_pcs ?? '—' }}</h4>
                            <span>
                                {{ $stok }} pcs
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
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
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
                    Checkout
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
            <div style="background:#f8fafc;border-radius:12px;padding:14px;border:1px solid #e9eaec;margin-top:14px">
                <div
                    style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#9ca3af;margin-bottom:10px">
                    Konfirmasi Metode Pembayaran
                </div>
                <div id="co-pay-methods" style="display:flex;gap:8px;flex-wrap:wrap"></div>
            </div>
            <div class="modal-actions">
                <button class="btn-modal-cancel" id="btn-cancel-checkout"
                    onclick="closeModal('modal-checkout')">Batal</button>
                <button class="btn-modal-confirm" id="btn-confirm-checkout" onclick="saveTransaction()">✓ Ya, Simpan
                    Transaksi</button>
            </div>
        </div>
    </div>

    {{-- ═══ MODAL 3 — STRUK (tanpa PDF/Excel, ada tombol Sync Sheets) ═══ --}}
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

            {{-- Hanya Print + Sync Sheets --}}
            <div class="receipt-btn-group">
                <button class="btn-rc btn-rc-print" onclick="window.print()">🖨 Print Struk</button>
                <button class="btn-rc btn-rc-sheets" id="btn-rc-sheets" onclick="syncToSheets()">
                    📊 Sync ke Sheets
                </button>
            </div>
            <div id="sync-status-receipt" class="sync-status"></div>

            <button class="btn-rc-new-trx" onclick="resetAndClose()">✓ Selesai & Transaksi Baru</button>
        </div>
    </div>

    {{-- ═══ MODAL 4 — LAPORAN ═══ --}}
    <div class="overlay" id="modal-laporan" style="display:none" onclick="closeModalOutside(event,'modal-laporan')">
        <div class="modal modal-sm">
            <div class="modal-head">
                <div>
                    <h2>Laporan Hari Ini</h2>
                    <p id="lap-tanggal">—</p>
                </div>
                <button class="modal-close" onclick="closeModal('modal-laporan')">✕</button>
            </div>
            <div class="laporan-modal-grid">
                <div class="lap-stat">
                    <div class="ls-label">Total Penjualan</div>
                    <div class="ls-val s-emerald" id="lap-sales">Rp0</div>
                </div>
                <div class="lap-stat">
                    <div class="ls-label">Jumlah Transaksi</div>
                    <div class="ls-val s-indigo" id="lap-trx">0</div>
                </div>
                <div class="lap-stat">
                    <div class="ls-label">Item Terjual</div>
                    <div class="ls-val s-amber" id="lap-items">0</div>
                </div>
                <div class="lap-stat">
                    <div class="ls-label">Total Diskon</div>
                    <div class="ls-val" style="color:#6b7280" id="lap-disc">Rp0</div>
                </div>
            </div>
            {{-- Hanya Sync Sheets, tanpa PDF/Excel --}}
            <button class="btn-lap-action btn-lap-sheets" id="btn-lap-sheets" onclick="syncToSheets()">
                📊 Sync Laporan ke Google Sheets
            </button>
            <div id="sync-status-laporan" class="sync-status"></div>
        </div>
    </div>

    {{-- ═══ MODAL 5 — KONFIGURASI GOOGLE SHEETS ═══ --}}
    <div class="overlay" id="modal-sheets" style="display:none" onclick="closeModalOutside(event,'modal-sheets')">
        <div class="modal modal-sm">
            <div class="modal-head">
                <div>
                    <h2>📊 Sync ke Google Sheets</h2>
                    <p>Kirim data penjualan hari ini ke spreadsheet.</p>
                </div>
                <button class="modal-close" onclick="closeModal('modal-sheets')">✕</button>
            </div>

            <div class="sheets-config">
                <label>Spreadsheet ID</label>
                <input id="sheets-id-input" class="input-sm" type="text"
                    placeholder="Contoh: 1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgVE2upms" oninput="saveSheetsId()">
                <div class="hint">
                    Ambil dari URL spreadsheet kamu:<br>
                    <code>docs.google.com/spreadsheets/d/<strong>[ID DI SINI]</strong>/edit</code>
                </div>
            </div>

            {{-- <div class="sheets-config" style="margin-top:0">
                <label>Nama Sheet (Tab)</label>
                <input id="sheets-tab-input" class="input-sm" type="text" placeholder="Contoh: Laporan"
                    value="Laporan" oninput="saveSheetsTab()">
                <div class="hint">
                    Nama tab/sheet di dalam spreadsheet. Pastikan tab sudah ada.
                </div>
            </div> --}}

            <div id="sync-status-modal" class="sync-status"></div>

            <div class="modal-actions" style="margin-top:14px">
                <button class="btn-modal-cancel" onclick="closeModal('modal-sheets')">Tutup</button>
                <button class="btn-modal-confirm" id="btn-do-sync" onclick="doSync()">
                    📊 Sync Sekarang
                </button>
            </div>

            <div
                style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px 14px;margin-top:12px;font-size:11px;color:#92400e;line-height:1.6">
                <strong>Setup yang diperlukan:</strong><br>
                1. Buat Google Sheet & catat ID-nya<br>
                2. Di Laravel, install <code
                    style="background:#fde68a;padding:1px 4px;border-radius:3px">google/apiclient</code><br>
                3. Tambahkan route <code style="background:#fde68a;padding:1px 4px;border-radius:3px">POST
                    /cashier/sync-sheets</code><br>
                4. Lihat komentar di JS untuk payload yang dikirim
            </div>
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
                            'normal' => (float) ($menu->hargas->whereIn('metode_payment', ['take_away_cash', 'take_away_qris'])->first()?->harga ?? 0),
                            'gofood' => (float) ($menu->hargas->where('metode_payment', 'gofood')->first()?->harga ?? 0),
                            'shopeefood' => (float) ($menu->hargas->where('metode_payment', 'shopeefood')->first()?->harga ?? 0),
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

            // Stok list untuk custom menu (flat array)
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

            // Snapshot stok saat ini untuk Mutasi Stok sheet
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
        const STOCK_LIST = @json($stockList); // ← baru, untuk custom menu bahan selector
        const STOCK_SNAPSHOT = @json($stockSnapshot); // ← baru, untuk Mutasi Stok sheet
        const KASIR_NAME = @json($selectedShift?->user->name ?? (auth()->user()->name ?? '—'));
        const TANGGAL_HARI = @json(now()->translatedFormat('d F Y'));
        const CHECKOUT_URL = "{{ route('cashier.pos.checkout') }}";
        const SYNC_SHEETS_URL = "{{ url('/cashier/sync-sheets') }}";
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

        let todayTrxList = @json($todayTrxFull);
        let cart = [];
        let activeMenu = null;
        let modalQty = 1;
        let currentPay = '{{ strtolower(str_replace(' ', '', $paymentMethods[0] ?? 'normal')) }}';
        let lastTrxSnapshot = null;

        // Akumulasi mutasi stok hari ini (dikumpulkan dari setiap transaksi yang terjadi di sesi ini)
        // Format: { pcs_id: { pcs_name, stok_awal, total_dikurangi } }
        let stockMutasiMap = {};

        // Inisialisasi stockMutasiMap dari snapshot awal
        STOCK_SNAPSHOT.forEach(s => {
            stockMutasiMap[s.pcs_id] = {
                pcs_name: s.pcs_name,
                stok_awal: s.stok_saat_ini, // stok saat halaman dimuat
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
            shopeefood: 'ShopeeFood',
        };

        // ── RIWAYAT TRANSAKSI ──
        function openRiwayatModal() {
            document.getElementById('riwayat-tanggal').textContent = TANGGAL_HARI;
            const list = document.getElementById('riwayat-list');

            if (!todayTrxList.length) {
                list.innerHTML = '<p style="color:#9ca3af;font-size:13px;padding:16px">Belum ada transaksi hari ini.</p>';
                openModal('modal-riwayat');
                return;
            }

            list.innerHTML = [...todayTrxList].reverse().map(t => `
        <div style="border:1px solid #e9eaec;border-radius:12px;padding:14px;margin-bottom:10px">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                <div>
                    <span style="font-weight:700;font-size:13px">${trxNo(t.id)}</span>
                    <span style="font-size:11px;color:#9ca3af;margin-left:8px">${t.created_at}</span>
                </div>
                <div style="display:flex;gap:8px;align-items:center">
                    <span style="font-size:12px;background:#eef2ff;color:#4338ca;padding:2px 10px;border-radius:99px">
                        ${METHOD_LABELS[t.payment_method] ?? t.payment_method}
                    </span>
                    <span style="font-weight:700;color:#059669">${fmt(t.total)}</span>
                    <button onclick="cetakUlang(${t.id})"
                        style="padding:5px 12px;border-radius:8px;border:1px solid #e5e7eb;background:#fff;font-size:11px;font-weight:600;cursor:pointer;color:#374151">
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

            // Isi ulang struk dengan data transaksi lama
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
            // Update currentPay dan hitung ulang total
            setPaymentMethod(method);
            // Update tampilan total di modal checkout
            const disc = Math.max(0, parseFloat(document.getElementById('discount').value) || 0);
            const sub = cart.reduce((s, i) => s + i.subtotal, 0);
            const total = Math.max(0, sub - disc);
            const paid = parseFloat(document.getElementById('money-paid').value) || 0;
            document.getElementById('co-total').textContent = fmt(total);
            document.getElementById('co-change').textContent = fmt(Math.max(0, paid - total));
            document.getElementById('co-method').textContent = METHOD_LABELS[method] ?? method;
        }

        function setPaymentMethod(method) {
            currentPay = method;
            // Update tombol di panel utama juga
            document.querySelectorAll('#pay-methods .pay-btn').forEach(b => {
                b.classList.toggle('active', b.dataset.method === method);
            });
            // Recalculate harga cart
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

        // ── FIX updateStats agar sinkron dengan DB ──
        // Ganti fungsi updateStats yang ada:
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
            return s <= 5 ? 'mstk mstk-red' :
                s <= 10 ? 'mstk mstk-yellow' :
                s <= 20 ? 'mstk mstk-orange' :
                'mstk mstk-ok';
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

        function updateStats() {
            const totalSales = todayTrxList.reduce((s, t) => s + (t.total || 0), 0);
            const totalTrx = todayTrxList.length;
            const totalItems = todayTrxList.reduce((s, t) =>
                s + (t.items || []).reduce((si, i) => si + (i.qty || 0), 0), 0);
            document.getElementById('stat-sales').textContent = fmt(totalSales);
            document.getElementById('stat-trx').textContent = totalTrx;
            document.getElementById('stat-items').textContent = totalItems;
        }

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

        function openModal(id) {
            document.getElementById(id).style.display = 'flex';
        }

        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }

        function closeModalOutside(event, id) {
            if (event.target.id === id) closeModal(id);
        }

        /* ── MODAL 1 — PILIH MENU ── */
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
                        <span>Per porsi: ${d.qty} pcs &nbsp;·&nbsp; Sisa stok: <strong>${d.stock} pcs</strong>${d.stock <= 20 ? ' ⚠' : ''}</span>
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
                details: [], // tidak ada bahan stok
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

        /* ── RENDER CART ── */
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
                // Tampilkan bahan untuk custom menu
                const bahanInfo = item.custom && item.details?.length ?
                    `<div style="font-size:10px;color:#6b7280;margin-top:2px">
                       Bahan: ${item.details.map(d => `${d.pcs_name} ×${d.qty}`).join(', ')}
                   </div>` :
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

        /* ── MODAL 2 — KONFIRMASI CHECKOUT ── */
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

            // Stok impact
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
                    coStocks.innerHTML += `
                <div class="${mStkClass(sisa)}" style="margin-bottom:6px">
                    <h5>${name}</h5>
                    <span>Sisa: <strong>${v.stock} pcs</strong> − ${v.used} pcs = <strong>${sisa} pcs</strong>${sisa <= 20 ? ' ⚠' : ''}</span>
                </div>`;
                });
            }

            // Items list
            document.getElementById('co-items').innerHTML = cart.map(item => `
        <li>
            <div><div class="cn">${item.name}</div><div class="cq">x${item.qty}</div></div>
            <div class="cs">${fmt(item.subtotal)}</div>
        </li>`).join('');

            document.getElementById('co-total').textContent = fmt(total);
            document.getElementById('co-change').textContent = fmt(change);
            document.getElementById('co-method').textContent = METHOD_LABELS[currentPay] ?? currentPay;

            // Render tombol konfirmasi metode
            const coPayMethods = document.getElementById('co-pay-methods');
            if (coPayMethods) {
                const methods = ['cash', 'qris', 'gofood', 'shopeefood'];
                const labels = {
                    cash: 'Cash',
                    qris: 'QRIS',
                    gofood: 'GoFood',
                    shopeefood: 'ShopeeFood'
                };
                coPayMethods.innerHTML = methods.map(m => `
            <button class="pay-btn ${currentPay === m ? 'active' : ''}"
                onclick="setCheckoutPayment(this, '${m}')"
                data-method="${m}">${labels[m]}</button>
        `).join('');
            }

            openModal('modal-checkout');
        }

        /* ── SIMPAN TRANSAKSI ── */
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
                if (!response.ok || result.success === false) throw new Error(result.message || 'Transaksi gagal.');

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
                        is_custom: item.custom,
                        // ← DATA BARU: bahan yang dikurangi per item di transaksi ini
                        bahan_dikurangi: (item.details || []).map(d => ({
                            pcs_id: d.pcs_id,
                            pcs_name: d.pcs_name,
                            qty_per_porsi: d.qty,
                            total_dikurangi: d.qty * item.qty,
                        })),
                    })),
                };

                // Update mutasi stok lokal
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

        /* ── MODAL 3 — STRUK ── */
        function showReceipt(sub, disc, total, paid, change, id) {
            document.getElementById('rc-datetime').textContent = nowStr();
            document.getElementById('rc-trxno').textContent = trxNo(id);
            document.getElementById('rc-method').textContent = METHOD_LABELS[currentPay] ?? currentPay;
            document.getElementById('rc-items').innerHTML = cart.map(item => `
            <div class="rc-item-row">
                <div>
                    <div class="rc-item-name">${item.name}</div>
                    <div class="rc-item-detail">${item.qty} x ${fmt(item.unitPrice)}</div>
                    ${item.custom && item.details?.length
                        ? `<div style="font-size:10px;color:#9ca3af">Bahan: ${item.details.map(d=>`${d.pcs_name}×${d.qty}`).join(', ')}</div>`
                        : ''}
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
            // Bersihkan synced IDs kalau sudah ganti hari
            const lastSyncDate = localStorage.getItem('synced_date');
            if (lastSyncDate !== TANGGAL_HARI) {
                localStorage.removeItem('synced_trx_ids');
                localStorage.setItem('synced_date', TANGGAL_HARI);
            }
        }

        /* ── MODAL 4 — LAPORAN ── */
        function openLaporanModal() {
            const totalSales = todayTrxList.reduce((s, t) => s + (t.total || 0), 0);
            const totalDisc = todayTrxList.reduce((s, t) => s + (t.discount || 0), 0);
            const totalTrx = todayTrxList.length;
            const totalItems = todayTrxList.reduce((s, t) =>
                s + (t.items || []).reduce((si, i) => si + (i.qty || 0), 0), 0);
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

        /* ══════════════════════════════════════════════════════
           GOOGLE SHEETS SYNC — 3 TAB PAYLOAD
           ══════════════════════════════════════════════════════
           Tab 1: "Ringkasan Harian"
                  Kolom: Tanggal | Kasir | Jumlah Transaksi | Item Terjual |
                         Total Diskon | Total Penjualan

           Tab 2: "Detail Transaksi"
                  Kolom: Tanggal | No.Transaksi | Kasir | Metode Bayar |
                         Nama Item | Qty | Harga Satuan | Subtotal |
                         Diskon | Total Transaksi | Bahan Dikurangi (detail)

           Tab 3: "Mutasi Stok"
                  Kolom: Tanggal | Nama Bahan | Stok Awal (saat halaman dimuat) |
                         Total Dikurangi Hari Ini | Stok Akhir
        ══════════════════════════════════════════════════════ */

        function buildSheetsPayload() {
            // Ambil ID yang sudah pernah di-sync
            const syncedIds = JSON.parse(localStorage.getItem('synced_trx_ids') || '[]');

            // Filter hanya transaksi BARU yang belum di-sync
            const newTrx = todayTrxList.filter(t => !syncedIds.includes(t.id));

            // Ringkasan tetap dari semua transaksi hari ini (bukan hanya yang baru)
            const ringkasan = {
                tanggal: TANGGAL_HARI,
                kasir: KASIR_NAME,
                jumlah_transaksi: todayTrxList.length,
                item_terjual: todayTrxList.reduce((s, t) =>
                    s + (t.items || []).reduce((si, i) => si + (i.qty || 0), 0), 0),
                total_diskon: todayTrxList.reduce((s, t) => s + (t.discount || 0), 0),
                total_penjualan: todayTrxList.reduce((s, t) => s + (t.total || 0), 0),
            };

            // Detail hanya dari transaksi BARU
            const detailRows = [];
            newTrx.forEach(t => {
                (t.items || []).forEach(item => {
                    const bahanStr = (item.bahan_dikurangi || [])
                        .map(b => `${b.pcs_name} -${b.total_dikurangi}pcs`)
                        .join(' | ') || (item.is_custom ? 'Custom' : '—');
                    detailRows.push({
                        tanggal: t.created_at,
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

            // Mutasi stok hanya dari transaksi BARU
            const mutasiMap = {};
            newTrx.forEach(t => {
                (t.items || []).forEach(item => {
                    (item.bahan_dikurangi || []).forEach(b => {
                        if (!b.pcs_id) return;
                        if (!mutasiMap[b.pcs_id]) {
                            const snap = STOCK_SNAPSHOT.find(s => s.pcs_id == b.pcs_id);
                            mutasiMap[b.pcs_id] = {
                                pcs_name: b.pcs_name,
                                stok_awal: snap?.stok_saat_ini ?? 0,
                                total_dikurangi: 0,
                            };
                        }
                        mutasiMap[b.pcs_id].total_dikurangi += b.total_dikurangi;
                    });
                });
            });
            const mutasiRows = Object.entries(mutasiMap).map(([pcsId, v]) => ({
                tanggal: TANGGAL_HARI,
                pcs_id: pcsId,
                nama_bahan: v.pcs_name,
                stok_awal: v.stok_awal,
                total_dikurangi: v.total_dikurangi,
                stok_akhir: v.stok_awal - v.total_dikurangi,
            }));

            // Tandai semua transaksi ini sebagai sudah di-sync
            const allSyncedIds = [...new Set([...syncedIds, ...newTrx.map(t => t.id)])];
            localStorage.setItem('synced_trx_ids', JSON.stringify(allSyncedIds));

            return {
                ringkasan,
                detail_transaksi: detailRows,
                mutasi_stok: mutasiRows
            };
        }

        function loadSheetsConfig() {
            const id = localStorage.getItem('sheets_id') || '';
            const tab = localStorage.getItem('sheets_tab') || 'Laporan';
            const inputId = document.getElementById('sheets-id-input');
            const inputTab = document.getElementById('sheets-tab-input');
            if (inputId) inputId.value = id;
            if (inputTab) inputTab.value = tab;
        }

        function saveSheetsId() {
            localStorage.setItem('sheets_id', document.getElementById('sheets-id-input').value.trim());
        }

        function saveSheetsTab() {
            localStorage.setItem('sheets_tab', document.getElementById('sheets-tab-input').value.trim());
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

            /*
             * PAYLOAD BARU — 3 tab:
             * {
             *   spreadsheet_id: "...",
             *   ringkasan:        { tanggal, kasir, jumlah_transaksi, item_terjual, total_diskon, total_penjualan },
             *   detail_transaksi: [{ tanggal, no_transaksi, kasir, metode_bayar, nama_item, is_custom,
             *                         qty, harga_satuan, subtotal_item, diskon_transaksi, total_transaksi,
             *                         bahan_dikurangi }],
             *   mutasi_stok:      [{ tanggal, pcs_id, nama_bahan, stok_awal, total_dikurangi, stok_akhir }]
             * }
             *
             * Di Laravel (SyncSheetsController), baca 3 key ini dan tulis ke masing-masing tab:
             *   - payload['ringkasan']         → append ke tab "Ringkasan Harian"
             *   - payload['detail_transaksi']  → append ke tab "Detail Transaksi"
             *   - payload['mutasi_stok']       → append/update ke tab "Mutasi Stok"
             */
            const payload = {
                spreadsheet_id: spreadsheetId,
                ...buildSheetsPayload(),
            };

            try {
                const res = await fetch(SYNC_SHEETS_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                if (res.status === 404) {
                    showSyncStatus('sync-status-modal', 'err',
                        '⚠️ Endpoint /cashier/sync-sheets belum ada. Tambahkan route & controller di Laravel dulu.');
                    return;
                }
                const data = await res.json();
                if (!res.ok || data.success === false) throw new Error(data.message || 'Sync gagal.');
                showSyncStatus('sync-status-modal', 'ok', '✅ Data berhasil dikirim ke 3 tab Google Sheets!');
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
                    ...buildSheetsPayload()
                };
                await fetch(SYNC_SHEETS_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
            } catch (err) {
                console.warn('Auto-sync Sheets gagal (silent):', err.message);
            }
        }

        // Init
        renderMenuPrices();
        renderCart();
        loadSheetsConfig();

        // Tandai semua transaksi yang sudah ada di DB saat halaman dimuat
        // supaya tidak ikut di-sync ulang di sesi ini
        (function initSyncedIds() {
            const today = TANGGAL_HARI;
            const lastSyncDate = localStorage.getItem('synced_date');

            // Kalau ganti hari, reset dulu
            if (lastSyncDate !== today) {
                localStorage.removeItem('synced_trx_ids');
                localStorage.setItem('synced_date', today);
            }

            // Tandai semua ID yang sudah ada saat halaman dimuat sebagai "sudah di-sync"
            // (transaksi baru yang dibuat di sesi ini akan ditambahkan setelah checkout)
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
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({})
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message);

                // Auto-isi spreadsheet ID
                document.getElementById('sheets-id-input').value = data.spreadsheet_id;
                saveSheetsId();

                showSyncStatus('sync-status-modal', 'ok',
                    `✅ ${data.message} — <a href="${data.url}" target="_blank" style="color:#0369a1">Buka Spreadsheet</a>`
                );
                // Render HTML di status (karena ada tag <a>)
                document.getElementById('sync-status-modal').innerHTML =
                    `✅ ${data.message} &nbsp;<a href="${data.url}" target="_blank" style="color:#0369a1;font-weight:600">→ Buka Spreadsheet</a>`;
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
