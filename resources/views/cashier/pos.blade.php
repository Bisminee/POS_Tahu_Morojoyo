{{-- resources/views/kasir/pos.blade.php --}}
@props(['title' => 'POS Kasir'])

<x-layouts.app :title="$title">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
    theme: {
        extend: {
            colors: {
                brand: {
                    red:         '#C0392B',
                    'red-dark':  '#96281B',
                    'red-light': '#E74C3C',
                    cream:       '#FAF6EF',
                    'cream-dark':'#F0E9DC',
                    warm:        '#7B3F2B',
                }
            },
            fontFamily: {
                sans: ['Plus Jakarta Sans', 'system-ui', 'sans-serif'],
                mono: ['IBM Plex Mono', 'Courier New', 'monospace'],
            }
        }
    }
}
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
@import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&display=swap');

* { box-sizing: border-box; }
body { font-family: 'Plus Jakarta Sans', sans-serif; background: #FAF6EF; }

/* ── POS Layout ── */
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
    background: #FAF6EF;
}
.pos-right {
    border-left: 1px solid #F0E9DC;
    background: #fff;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* ── Card base ── */
.card {
    background: #fff;
    border-radius: 16px;
    padding: 18px 20px;
    border: 1px solid #F0E9DC;
}
.card-title {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: #C0392B;
    opacity: .6;
    margin-bottom: 14px;
}

/* ── Topbar ── */
.topbar {
    background: #fff;
    border-radius: 16px;
    padding: 14px 20px;
    border: 1px solid #F0E9DC;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}
.topbar h1 {
    font-size: 18px;
    font-weight: 800;
    color: #C0392B;
    letter-spacing: .02em;
}
.topbar p { font-size: 12px; color: #9ca3af; margin-top: 2px; }

/* ── Badge ── */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    border-radius: 99px;
    font-size: 12px;
    font-weight: 600;
    background: #F0E9DC;
    color: #7B3F2B;
    border: 1px solid #e8ddd0;
}
.badge-red {
    background: #fef2f2;
    color: #C0392B;
    border-color: #fca5a5;
}

/* ── Topbar buttons ── */
.btn-topbar-base {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border-radius: 99px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: background .15s;
    font-family: inherit;
    border: 1px solid;
}
.btn-laporan  { background: #FAF6EF; color: #7B3F2B; border-color: #e8ddd0; }
.btn-laporan:hover  { background: #F0E9DC; }
.btn-sheets   { background: #f0f9ff; color: #0369a1; border-color: #bae6fd; }
.btn-sheets:hover   { background: #e0f2fe; }
.btn-riwayat  { background: #f5f3ff; color: #6d28d9; border-color: #ddd6fe; }
.btn-riwayat:hover  { background: #ede9fe; }
.btn-absensi  { background: #fffbeb; color: #92400e; border-color: #fde68a; }
.btn-absensi:hover  { background: #fef3c7; }
.btn-logout   { background: #fef2f2; color: #C0392B; border-color: #fca5a5; border-radius: 99px; font-size: 12px; font-weight: 700; cursor: pointer; padding: 7px 16px; transition: background .15s; font-family: inherit; border-width: 1px; border-style: solid; }
.btn-logout:hover   { background: #fee2e2; }

/* ── Stats ── */
.stat-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
.stat-card {
    background: #fff;
    border-radius: 16px;
    padding: 16px 18px;
    border: 1px solid #F0E9DC;
}
.stat-card .s-label { font-size: 11px; color: #9ca3af; font-weight: 500; }
.stat-card .s-val   { font-size: 22px; font-weight: 800; margin-top: 6px; }
.s-red    { color: #C0392B; }
.s-warm   { color: #7B3F2B; }
.s-amber  { color: #d97706; }

/* ── Stock warning banner ── */
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

/* ── Payment method buttons ── */
.pay-btn {
    padding: 8px 18px;
    border-radius: 99px;
    border: 1.5px solid #F0E9DC;
    background: #fff;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    color: #9ca3af;
    transition: all .15s;
    font-family: inherit;
}
.pay-btn:hover  { border-color: #C0392B; color: #C0392B; }
.pay-btn.active { background: #fef2f2; border-color: #C0392B; color: #96281B; font-weight: 700; }

/* ── Menu grid ── */
.menu-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(155px, 1fr)); gap: 10px; }
.menu-card {
    border-radius: 14px;
    border: 1.5px solid #F0E9DC;
    background: #FAF6EF;
    padding: 14px;
    cursor: pointer;
    transition: all .18s;
    text-align: left;
    width: 100%;
}
.menu-card:hover {
    border-color: #C0392B;
    background: #fff8f7;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(192,57,43,.1);
}
.menu-card h3 { font-size: 13px; font-weight: 700; color: #111827; line-height: 1.4; }
.menu-card p  { font-size: 11px; color: #9ca3af; margin-top: 5px; line-height: 1.5; }
.menu-price-tag {
    display: inline-block;
    margin-top: 10px;
    font-size: 14px;
    font-weight: 800;
    color: #C0392B;
}

/* ── Stock grid (3-level color) ── */
.stock-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 8px; }
.stk { border-radius: 12px; padding: 10px 12px; font-size: 12px; border: 1px solid; }
.stk h4 { font-weight: 700; font-size: 12px; margin-bottom: 4px; }
.stk-red    { background: #fef2f2; border-color: #fca5a5; color: #991b1b; }
.stk-yellow { background: #fffbeb; border-color: #fde68a; color: #92400e; }
.stk-orange { background: #fff7ed; border-color: #fed7aa; color: #c2410c; }
.stk-ok     { background: #FAF6EF; border-color: #e8ddd0; color: #7B3F2B; }

/* ── Right panel cart ── */
.rp-header {
    padding: 14px 18px;
    border-bottom: 1px solid #FAF6EF;
    font-size: 14px;
    font-weight: 800;
    color: #C0392B;
    display: flex;
    align-items: center;
    gap: 8px;
    letter-spacing: .02em;
}

.cart-scroll { flex: 1; overflow-y: auto; padding: 14px 16px; }
.cart-empty-box {
    border: 1.5px dashed #F0E9DC;
    border-radius: 14px;
    padding: 32px 16px;
    text-align: center;
    color: #c4b9ae;
    font-size: 13px;
}

.cart-item-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 9px 0;
    border-bottom: 1px solid #FAF6EF;
    gap: 8px;
}
.ci-name  { font-size: 13px; font-weight: 700; color: #111827; }
.ci-qty   { font-size: 11px; color: #9ca3af; margin-top: 2px; }
.ci-price { font-size: 13px; font-weight: 700; color: #111827; text-align: right; }
.ci-remove { font-size: 10px; font-weight: 700; color: #C0392B; cursor: pointer; margin-top: 4px; letter-spacing: .05em; text-transform: uppercase; }
.ci-remove:hover { color: #96281B; }

.custom-badge {
    display: inline-block;
    background: #fef2f2;
    color: #C0392B;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    padding: 1px 7px;
    border-radius: 99px;
    margin-left: 5px;
    vertical-align: middle;
    border: 1px solid #fca5a5;
}

/* ── Cart summary / money ── */
.custom-section {
    padding: 12px 16px;
    border-top: 1px solid #FAF6EF;
    background: #FAF6EF;
}
.custom-section .ct {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: #C0392B;
    opacity: .6;
    margin-bottom: 8px;
}

.input-sm {
    width: 100%;
    border: 1px solid #F0E9DC;
    border-radius: 10px;
    padding: 7px 10px;
    font-size: 13px;
    font-family: inherit;
    color: #111827;
    background: #fff;
    outline: none;
    transition: border-color .15s;
}
.input-sm:focus        { border-color: #C0392B; }
.input-sm::placeholder { color: #d1d5db; }

.btn-add-custom {
    background: #C0392B;
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 7px 14px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    white-space: nowrap;
    transition: background .15s;
    font-family: inherit;
}
.btn-add-custom:hover { background: #96281B; }

.cart-summary {
    padding: 12px 16px;
    border-top: 1px solid #FAF6EF;
    background: #FAF6EF;
    font-size: 13px;
}
.disc-row { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
.disc-row label { font-size: 12px; color: #9ca3af; white-space: nowrap; }
.sum-line { display: flex; justify-content: space-between; padding: 3px 0; color: #6b7280; }
.sum-line.big {
    font-size: 15px;
    font-weight: 800;
    color: #111827;
    padding-top: 8px;
    margin-top: 4px;
    border-top: 1px solid #F0E9DC;
}

.money-section { padding: 10px 16px; border-top: 1px solid #FAF6EF; }
.money-section label { font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #9ca3af; display: block; margin-bottom: 5px; }
.change-row { display: flex; justify-content: space-between; margin-top: 6px; font-size: 13px; color: #6b7280; }
.change-amt { font-weight: 800; color: #C0392B; }

/* ── Checkout button ── */
.checkout-bar { padding: 12px 16px; border-top: 1px solid #FAF6EF; background: #fff; }
.btn-checkout {
    width: 100%;
    background: linear-gradient(135deg, #C0392B, #E74C3C);
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 13px;
    font-size: 14px;
    font-weight: 800;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-family: inherit;
    letter-spacing: .05em;
    transition: all .15s;
    box-shadow: 0 4px 14px rgba(192,57,43,.3);
}
.btn-checkout:hover    { background: linear-gradient(135deg, #96281B, #C0392B); box-shadow: 0 6px 18px rgba(192,57,43,.35); }
.btn-checkout:active   { transform: scale(.98); }
.btn-checkout:disabled { background: #9ca3af; box-shadow: none; cursor: not-allowed; }
.btn-checkout svg { width: 18px; height: 18px; }

/* ── Alerts ── */
.alert { border-radius: 14px; padding: 12px 16px; font-size: 13px; }
.alert-danger  { background: #fef2f2; color: #991b1b; border: 1px solid #fca5a5; }
.alert-success { background: #FAF6EF; color: #7B3F2B; border: 1px solid #e8ddd0; }
.alert-warning { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }

/* ── Modals ── */
.overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.5);
    z-index: 200;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    backdrop-filter: blur(3px);
    animation: fadeIn .15s ease;
}
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUp { from { transform: translateY(18px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

.modal {
    background: #fff;
    border-radius: 20px;
    padding: 24px;
    width: 100%;
    max-width: 520px;
    max-height: 90vh;
    overflow-y: auto;
    animation: slideUp .2s ease;
    border: 1px solid #F0E9DC;
}
.modal-wide { max-width: 620px; }
.modal-sm   { max-width: 400px; }

.modal-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 18px; }
.modal-head h2 { font-size: 17px; font-weight: 800; color: #111827; }
.modal-head p  { font-size: 12px; color: #9ca3af; margin-top: 3px; }

.modal-close {
    flex-shrink: 0;
    width: 28px;
    height: 28px;
    border-radius: 99px;
    background: #FAF6EF;
    border: 1px solid #F0E9DC;
    cursor: pointer;
    font-size: 14px;
    color: #6b7280;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .15s;
}
.modal-close:hover { background: #F0E9DC; }
.modal-section       { margin-bottom: 16px; }
.modal-section-title { font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: #C0392B; opacity: .6; margin-bottom: 10px; }
.modal-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

.mstk { border-radius: 10px; padding: 10px 12px; font-size: 12px; border: 1px solid; margin-bottom: 7px; }
.mstk h5 { font-weight: 700; margin-bottom: 2px; }
.mstk-ok     { background: #FAF6EF; border-color: #e8ddd0; color: #7B3F2B; }
.mstk-yellow { background: #fffbeb; border-color: #fde68a; color: #92400e; }
.mstk-orange { background: #fff7ed; border-color: #fed7aa; color: #c2410c; }
.mstk-red    { background: #fef2f2; border-color: #fecaca; color: #991b1b; }

.order-box { background: #FAF6EF; border-radius: 14px; padding: 16px; border: 1px solid #F0E9DC; }
.price-display { font-size: 22px; font-weight: 800; color: #C0392B; margin: 8px 0 14px; }

.qty-control { display: flex; align-items: center; gap: 12px; }
.qty-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1px solid #F0E9DC;
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
.qty-btn:hover { background: #FAF6EF; }
.qty-val { font-size: 18px; font-weight: 800; min-width: 32px; text-align: center; }

.btn-add-cart {
    width: 100%;
    background: #C0392B;
    color: #fff;
    border: none;
    border-radius: 11px;
    padding: 12px;
    font-size: 14px;
    font-weight: 800;
    cursor: pointer;
    margin-top: 14px;
    font-family: inherit;
    transition: background .15s;
}
.btn-add-cart:hover { background: #96281B; }

/* Confirm list */
.confirm-list { list-style: none; padding: 0; margin: 0; }
.confirm-list li { display: flex; justify-content: space-between; align-items: center; padding: 7px 0; border-bottom: 1px solid #FAF6EF; font-size: 13px; }
.confirm-list li .cn { font-weight: 700; color: #111827; }
.confirm-list li .cq { font-size: 11px; color: #9ca3af; }
.confirm-list li .cs { font-weight: 700; }
.confirm-total-row { display: flex; justify-content: space-between; align-items: center; font-size: 16px; font-weight: 800; padding-top: 10px; margin-top: 6px; border-top: 2px solid #111827; color: #111827; }

.modal-actions { display: flex; gap: 10px; margin-top: 20px; }
.btn-modal-cancel {
    flex: 1; padding: 11px; border-radius: 11px; border: 1.5px solid #F0E9DC; background: #fff;
    font-size: 13px; font-weight: 700; cursor: pointer; color: #374151; font-family: inherit; transition: background .15s;
}
.btn-modal-cancel:hover { background: #FAF6EF; }
.btn-modal-confirm {
    flex: 2; padding: 11px; border-radius: 11px; border: none; background: #C0392B; color: #fff;
    font-size: 13px; font-weight: 800; cursor: pointer; font-family: inherit; transition: background .15s;
}
.btn-modal-confirm:hover    { background: #96281B; }
.btn-modal-confirm:disabled { background: #9ca3af; cursor: not-allowed; }

/* ── Thermal receipt ── */
.receipt-thermal {
    background: #fff;
    border: 1px solid #F0E9DC;
    border-radius: 4px;
    padding: 20px 18px;
    font-family: 'IBM Plex Mono', 'Courier New', monospace;
    font-size: 12px;
    color: #111827;
}
.rc-brand { text-align: center; padding-bottom: 12px; margin-bottom: 12px; border-bottom: 1px dashed #c4b9ae; }
.rc-brand h3 { font-size: 15px; font-weight: 600; margin: 0 0 3px; font-family: 'IBM Plex Mono', monospace; }
.rc-brand p  { font-size: 11px; color: #6b7280; margin: 2px 0 0; }
.rc-brand .rc-method-badge {
    display: inline-block;
    margin-top: 6px;
    padding: 2px 10px;
    border: 1px solid #C0392B;
    border-radius: 2px;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #C0392B;
}
.rc-meta { padding-bottom: 10px; margin-bottom: 10px; border-bottom: 1px dashed #c4b9ae; }
.rc-meta-row { display: flex; justify-content: space-between; font-size: 11px; padding: 2px 0; color: #374151; }
.rc-meta-row span:first-child { color: #6b7280; }
.rc-items-head {
    display: flex; justify-content: space-between;
    font-size: 10px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase;
    color: #6b7280; padding-bottom: 5px; border-bottom: 1px solid #F0E9DC; margin-bottom: 5px;
}
.rc-item-row   { display: flex; justify-content: space-between; align-items: baseline; padding: 5px 0; border-bottom: 1px dashed #F0E9DC; }
.rc-item-name  { font-size: 12px; font-weight: 500; color: #111827; }
.rc-item-detail{ font-size: 10px; color: #9ca3af; margin-top: 1px; }
.rc-item-sub   { font-size: 12px; font-weight: 500; white-space: nowrap; }
.rc-sum        { padding: 10px 0 8px; border-bottom: 1px dashed #c4b9ae; }
.rc-sum-row    { display: flex; justify-content: space-between; font-size: 11px; padding: 2px 0; color: #6b7280; }
.rc-sum-row.rc-total { font-size: 14px; font-weight: 700; color: #111827; margin-top: 6px; padding-top: 6px; border-top: 1px solid #374151; }
.rc-pay        { padding: 10px 0 0; }
.rc-pay-row    { display: flex; justify-content: space-between; font-size: 11px; padding: 2px 0; color: #374151; }
.rc-kembalian-box {
    display: flex; justify-content: space-between; align-items: center;
    margin-top: 8px; padding: 7px 10px;
    border: 1px solid #C0392B; border-radius: 3px; background: #fef2f2;
}
.rc-kembalian-box span { font-size: 11px; color: #96281B; font-weight: 500; }
.rc-kembalian-box strong { font-size: 13px; font-weight: 700; color: #C0392B; }
.rc-footer { margin-top: 14px; padding-top: 12px; border-top: 1px dashed #c4b9ae; text-align: center; }
.rc-footer p   { font-size: 11px; color: #6b7280; margin: 2px 0; }
.rc-footer .rc-wave { color: #c4b9ae; letter-spacing: .2em; font-size: 10px; margin-top: 6px; }

/* ── Receipt action buttons ── */
.receipt-btn-group { display: grid; grid-template-columns: 1fr 1fr; gap: 7px; margin-top: 12px; }
.btn-rc { display: flex; align-items: center; justify-content: center; gap: 5px; padding: 9px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; transition: background .15s; border: 1.5px solid; text-align: center; }
.btn-rc-print  { background: #fff;    border-color: #F0E9DC; color: #374151; }
.btn-rc-print:hover { background: #FAF6EF; }
.btn-rc-sheets { background: #f0f9ff; border-color: #bae6fd; color: #0369a1; }
.btn-rc-sheets:hover { background: #e0f2fe; }
.btn-rc-sheets:disabled { opacity: .6; cursor: not-allowed; }
.btn-rc-new-trx {
    width: 100%; margin-top: 8px; padding: 11px; border-radius: 10px;
    border: 1.5px solid #fca5a5; background: #fef2f2; color: #96281B;
    font-size: 13px; font-weight: 800; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif;
    transition: background .15s;
}
.btn-rc-new-trx:hover { background: #fee2e2; }

/* ── Laporan modal ── */
.laporan-modal-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 12px; }
.lap-stat { background: #FAF6EF; border-radius: 12px; padding: 14px 16px; border: 1px solid #F0E9DC; }
.lap-stat .ls-label { font-size: 11px; color: #9ca3af; font-weight: 500; }
.lap-stat .ls-val   { font-size: 20px; font-weight: 800; margin-top: 4px; }

.btn-lap-action { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 12px; border-radius: 11px; border: 1.5px solid; font-size: 13px; font-weight: 700; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; transition: background .15s; margin-bottom: 8px; }
.btn-lap-sheets { background: #f0f9ff; border-color: #bae6fd; color: #0369a1; }
.btn-lap-sheets:hover    { background: #e0f2fe; }
.btn-lap-sheets:disabled { opacity: .6; cursor: not-allowed; }

/* ── Sheets config ── */
.sheets-config { background: #FAF6EF; border-radius: 12px; padding: 14px 16px; border: 1px solid #F0E9DC; margin-bottom: 12px; }
.sheets-config label { font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: #9ca3af; display: block; margin-bottom: 6px; }
.sheets-config .hint { font-size: 11px; color: #9ca3af; margin-top: 4px; }
.sheets-config .hint code { background: #F0E9DC; padding: 1px 5px; border-radius: 4px; font-family: 'IBM Plex Mono', monospace; }

.sync-status { border-radius: 10px; padding: 10px 14px; font-size: 12px; font-weight: 500; margin-top: 8px; display: none; }
.sync-ok   { background: #FAF6EF; color: #7B3F2B; border: 1px solid #e8ddd0; }
.sync-err  { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
.sync-wait { background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; }

/* ── SWB chips ── */
.swb-chip { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 99px; font-size: 11px; font-weight: 700; }
.swb-chip-red    { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
.swb-chip-orange { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
.swb-chip-yellow { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }

/* ── Spinner ── */
.spinner { display: inline-block; width: 14px; height: 14px; border: 2px solid rgba(255,255,255,.4); border-top-color: currentColor; border-radius: 50%; animation: spin .6s linear infinite; vertical-align: middle; }
@keyframes spin { to { transform: rotate(360deg); } }

/* ── Print ── */
@media print {
    .pos-wrap, .topbar, #modal-menu, #modal-checkout, #modal-laporan, #modal-sheets { display: none !important; }
    #modal-receipt { position: static !important; background: none !important; backdrop-filter: none !important; padding: 0 !important; animation: none !important; align-items: flex-start !important; justify-content: flex-start !important; }
    #modal-receipt .modal { box-shadow: none !important; border: none !important; max-height: none !important; max-width: 100% !important; width: 80mm !important; margin: 0 !important; padding: 0 !important; animation: none !important; border-radius: 0 !important; }
    .modal-close, .receipt-btn-group, .btn-rc-new-trx, #sync-status-receipt { display: none !important; }
    @page { size: 80mm auto; margin: 4mm; }
}

/* ── Responsive ── */
@media (max-width: 768px) {
    .pos-wrap { grid-template-columns: 1fr; grid-template-rows: 1fr auto; max-height: none; height: auto; }
    .stat-row { grid-template-columns: 1fr 1fr; }
    .modal-2col { grid-template-columns: 1fr; }
    .laporan-modal-grid { grid-template-columns: 1fr; }
}
</style>

<div class="pos-wrap">

    {{-- ══ LEFT PANEL ══ --}}
    <div class="pos-left">

        {{-- Topbar --}}
        <div class="topbar">
            <div>
                <h1>POS Kasir</h1>
                <p>Sistem penjualan harian — Tahu Bakso Morojoyo</p>
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                <span class="badge">{{ $selectedShift?->user->name ?? auth()->user()->name }}</span>
                <span class="badge badge-red">{{ $selectedShift ? 'Kasir shift' : auth()->user()->role }}</span>

                <button type="button" class="btn-topbar-base btn-laporan" onclick="openLaporanModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Laporan Hari Ini
                </button>

                <button type="button" class="btn-topbar-base btn-sheets" id="btn-sheets-topbar" onclick="openSheetsModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                    </svg>
                    Sync Sheets
                </button>

                <button type="button" class="btn-topbar-base btn-riwayat" onclick="openRiwayatModal()">
                    📋 Riwayat
                </button>

                <a href="{{ route('attendance.index') }}" class="btn-topbar-base btn-absensi" style="text-decoration:none">
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

        {{-- Stock Warning Banner --}}
        @php $lowStocks = $stocks->filter(fn($s) => $s->jumlah_stok <= 20); @endphp
        @if ($lowStocks->count())
            <div class="stock-warning-banner">
                <div style="font-size:18px;flex-shrink:0;line-height:1">⚠️</div>
                <div>
                    <div style="font-weight:700;font-size:13px;margin-bottom:4px">Peringatan Stok Menipis!</div>
                    <div style="font-size:12px">Beberapa bahan membutuhkan perhatian segera:</div>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:6px">
                        @foreach ($lowStocks as $s)
                            @php $stok = $s->jumlah_stok; @endphp
                            <span class="swb-chip {{ $stok <= 5 ? 'swb-chip-red' : ($stok <= 10 ? 'swb-chip-yellow' : 'swb-chip-orange') }}">
                                {{ $s->pcsTahu?->nama_pcs ?? '—' }}: {{ $stok }} pcs
                                @if ($stok <= 5) 🔴 @elseif($stok <= 10) 🟡 @else 🟠 @endif
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
                <div class="s-val s-red" id="stat-sales">Rp{{ number_format($todaySales ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="stat-card">
                <div class="s-label">Jumlah Transaksi</div>
                <div class="s-val s-warm" id="stat-trx">{{ $todayTransactions ?? 0 }}</div>
            </div>
            <div class="stat-card">
                <div class="s-label">Menu Terjual</div>
                <div class="s-val s-amber" id="stat-items">{{ $todayItems ?? 0 }}</div>
            </div>
        </div>

        {{-- Payment methods --}}
        <div class="card">
            <div class="card-title">Metode Pembayaran</div>
            <div class="pay-methods" id="pay-methods" style="display:flex;gap:8px;flex-wrap:wrap">
                @foreach ($paymentMethods as $index => $method)
                    <button class="pay-btn {{ $index === 0 ? 'active' : '' }}"
                        onclick="setPayment(this, '{{ strtolower(str_replace(' ', '', $method)) }}')"
                        data-method="{{ strtolower(str_replace(' ', '', $method)) }}">{{ $method }}</button>
                @endforeach
            </div>
        </div>

        {{-- Riwayat modal --}}
        <div class="overlay" id="modal-riwayat" style="display:none" onclick="closeModalOutside(event,'modal-riwayat')">
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

        {{-- Stock inventory --}}
        <div class="card">
            <div class="card-title">Stok Inventori</div>
            <div class="stock-grid" id="stock-grid">
                @foreach ($stocks as $s)
                    @php $stok = $s->jumlah_stok; @endphp
                    <div class="stk {{ $stok <= 5 ? 'stk-red' : ($stok <= 10 ? 'stk-yellow' : ($stok <= 20 ? 'stk-orange' : 'stk-ok')) }}">
                        <h4>{{ $s->pcsTahu?->nama_pcs ?? '—' }}</h4>
                        <span>
                            {{ $stok }} pcs
                            @if ($stok <= 5) hampir habis
                            @elseif($stok <= 10) sisa sedikit
                            @elseif($stok <= 20) mulai menipis
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
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
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
            <input id="custom-name"  class="input-sm" type="text"   placeholder="Nama item (mis: Saus Extra, Ongkir)">
            <div style="display:flex;gap:6px;margin-top:6px">
                <input id="custom-price" class="input-sm" type="number" min="0" placeholder="Harga (Rp)">
                <input id="custom-qty"   class="input-sm" type="number" min="1" value="1" style="max-width:64px">
            </div>
            <button class="btn-add-custom" style="width:100%;margin-top:10px" onclick="addCustomMenu()">
                + Tambah ke Keranjang
            </button>
        </div>

        <div class="cart-summary" id="cart-summary" style="display:none">
            <div class="disc-row">
                <label for="discount">Diskon (Rp)</label>
                <input id="discount" class="input-sm" type="number" min="0" value="0" oninput="renderCart()">
            </div>
            <div class="sum-line"><span>Total item</span><span id="sum-items">0</span></div>
            <div class="sum-line"><span>Subtotal</span><span id="sum-sub">Rp0</span></div>
            <div class="sum-line"><span>Diskon</span><span id="sum-disc">Rp0</span></div>
            <div class="sum-line big"><span>Total</span><span id="sum-total">Rp0</span></div>
        </div>

        <div class="money-section" id="money-section" style="display:none">
            <label for="money-paid">Uang Dibayar</label>
            <input id="money-paid" class="input-sm" type="number" min="0" value="0" oninput="calcChange()">
            <div class="change-row">
                <span>Kembalian</span>
                <span id="change-display" class="change-amt">Rp0</span>
            </div>
        </div>

        <div class="checkout-bar">
            <button class="btn-checkout" id="btn-checkout" onclick="openCheckoutModal()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 7.5M7 13l-4-8m4 8h10m0 0l1.5 7.5M17 13v0"/>
                </svg>
                Checkout
            </button>
        </div>
    </div>

</div>{{-- /pos-wrap --}}

{{-- ═══ MODALS (sama seperti sebelumnya, sudah pakai class yg redefined di atas) ═══ --}}

{{-- Modal 1 — Pilih Menu --}}
<div class="overlay" id="modal-menu" style="display:none" onclick="closeModalOutside(event,'modal-menu')">
    <div class="modal modal-wide">
        <div class="modal-head">
            <div><h2 id="mm-title">Nama Menu</h2><p id="mm-desc">Deskripsi menu</p></div>
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

{{-- Modal 2 — Konfirmasi Checkout --}}
<div class="overlay" id="modal-checkout" style="display:none" onclick="closeModalOutside(event,'modal-checkout')">
    <div class="modal modal-wide">
        <div class="modal-head">
            <div><h2>Konfirmasi Checkout</h2><p>Periksa pesanan & stok sebelum menyimpan transaksi.</p></div>
            <button class="modal-close" onclick="closeModal('modal-checkout')">✕</button>
        </div>
        <div class="modal-section">
            <div class="modal-section-title">Stok inventori yang akan dikurangi</div>
            <div id="co-stocks"></div>
        </div>
        <div class="modal-section">
            <div class="modal-section-title">Pesanan</div>
            <ul class="confirm-list" id="co-items"></ul>
            <div class="confirm-total-row"><span>Total</span><span id="co-total" style="color:#C0392B"></span></div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:14px;padding:14px;background:#FAF6EF;border-radius:12px;border:1px solid #F0E9DC;font-size:13px">
            <div>
                <div style="color:#9ca3af;font-size:11px;text-transform:uppercase;letter-spacing:.08em">Metode</div>
                <div style="font-weight:800;margin-top:4px" id="co-method">—</div>
            </div>
            <div>
                <div style="color:#9ca3af;font-size:11px;text-transform:uppercase;letter-spacing:.08em">Kembalian</div>
                <div style="font-weight:800;color:#C0392B;margin-top:4px" id="co-change">Rp0</div>
            </div>
        </div>
        <div style="background:#FAF6EF;border-radius:12px;padding:14px;border:1px solid #F0E9DC;margin-top:14px">
            <div style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#C0392B;opacity:.6;margin-bottom:10px">Konfirmasi Metode Pembayaran</div>
            <div id="co-pay-methods" style="display:flex;gap:8px;flex-wrap:wrap"></div>
        </div>
        <div class="modal-actions">
            <button class="btn-modal-cancel" id="btn-cancel-checkout" onclick="closeModal('modal-checkout')">Batal</button>
            <button class="btn-modal-confirm" id="btn-confirm-checkout" onclick="saveTransaction()">✓ Ya, Simpan Transaksi</button>
        </div>
    </div>
</div>

{{-- Modal 3 — Struk --}}
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
            <button class="btn-rc btn-rc-sheets" id="btn-rc-sheets" onclick="syncToSheets()">📊 Sync ke Sheets</button>
        </div>
        <div id="sync-status-receipt" class="sync-status"></div>
        <button class="btn-rc-new-trx" onclick="resetAndClose()">✓ Selesai & Transaksi Baru</button>
    </div>
</div>

{{-- Modal 4 — Laporan --}}
<div class="overlay" id="modal-laporan" style="display:none" onclick="closeModalOutside(event,'modal-laporan')">
    <div class="modal modal-sm">
        <div class="modal-head">
            <div><h2>Laporan Hari Ini</h2><p id="lap-tanggal">—</p></div>
            <button class="modal-close" onclick="closeModal('modal-laporan')">✕</button>
        </div>
        <div class="laporan-modal-grid">
            <div class="lap-stat"><div class="ls-label">Total Penjualan</div><div class="ls-val s-red" id="lap-sales">Rp0</div></div>
            <div class="lap-stat"><div class="ls-label">Jumlah Transaksi</div><div class="ls-val s-warm" id="lap-trx">0</div></div>
            <div class="lap-stat"><div class="ls-label">Item Terjual</div><div class="ls-val s-amber" id="lap-items">0</div></div>
            <div class="lap-stat"><div class="ls-label">Total Diskon</div><div class="ls-val" style="color:#6b7280" id="lap-disc">Rp0</div></div>
        </div>
        <button class="btn-lap-action btn-lap-sheets" id="btn-lap-sheets" onclick="syncToSheets()">
            📊 Sync Laporan ke Google Sheets
        </button>
        <div id="sync-status-laporan" class="sync-status"></div>
    </div>
</div>

{{-- Modal 5 — Konfigurasi Google Sheets --}}
<div class="overlay" id="modal-sheets" style="display:none" onclick="closeModalOutside(event,'modal-sheets')">
    <div class="modal modal-sm">
        <div class="modal-head">
            <div><h2>📊 Sync ke Google Sheets</h2><p>Kirim data penjualan hari ini ke spreadsheet.</p></div>
            <button class="modal-close" onclick="closeModal('modal-sheets')">✕</button>
        </div>
        <div class="sheets-config">
            <label>Spreadsheet ID</label>
            <input id="sheets-id-input" class="input-sm" type="text" placeholder="Contoh: 1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgVE2upms" oninput="saveSheetsId()">
            <div class="hint">
                Ambil dari URL spreadsheet kamu:<br>
                <code>docs.google.com/spreadsheets/d/<strong>[ID DI SINI]</strong>/edit</code>
            </div>
        </div>
        <div id="sync-status-modal" class="sync-status"></div>
        <div class="modal-actions" style="margin-top:14px">
            <button class="btn-modal-cancel" onclick="closeModal('modal-sheets')">Tutup</button>
            <button class="btn-modal-confirm" id="btn-do-sync" onclick="doSync()">📊 Sync Sekarang</button>
        </div>
        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px 14px;margin-top:12px;font-size:11px;color:#92400e;line-height:1.6">
            <strong>Setup yang diperlukan:</strong><br>
            1. Buat Google Sheet & catat ID-nya<br>
            2. Di Laravel, install <code style="background:#fde68a;padding:1px 4px;border-radius:3px">google/apiclient</code><br>
            3. Tambahkan route <code style="background:#fde68a;padding:1px 4px;border-radius:3px">POST /cashier/sync-sheets</code><br>
            4. Lihat komentar di JS untuk payload yang dikirim
        </div>
    </div>
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- JavaScript sama persis — tidak ada perubahan logika --}}
<script>
    @php
        $menuData = $menus->map(function ($menu) use ($stocks) {
            return [
                'id'      => $menu->idMenu,
                'name'    => $menu->namaMenu,
                'desc'    => $menu->deskripsi,
                'prices'  => [
                    'normal'     => (float)($menu->hargas->whereIn('metode_payment',['take_away_cash','take_away_qris'])->first()?->harga ?? 0),
                    'gofood'     => (float)($menu->hargas->where('metode_payment','gofood')->first()?->harga ?? 0),
                    'shopeefood' => (float)($menu->hargas->where('metode_payment','shopeefood')->first()?->harga ?? 0),
                ],
                'details' => $menu->menuDetails->map(function ($d) use ($stocks) {
                    $stok = $stocks->get($d->id_pcs);
                    return ['pcs_id'=>$d->id_pcs,'pcs_name'=>$d->pcsTahu?->nama_pcs??'Bahan tidak dikenal','qty'=>(int)$d->jumlah_pcs,'stock'=>(int)($stok?->jumlah_stok??0)];
                })->values()->toArray(),
            ];
        })->values()->toArray();

        $stockList = $stocks->map(function ($s) {
            return ['pcs_id'=>$s->pcsTahu?->id_pcs??($s->id_pcs??null),'pcs_name'=>$s->pcsTahu?->nama_pcs??'—','stock'=>(int)$s->jumlah_stok];
        })->values()->toArray();

        $stockSnapshot = $stocks->map(function ($s) {
            return ['pcs_id'=>$s->pcsTahu?->id_pcs??null,'pcs_name'=>$s->pcsTahu?->nama_pcs??'—','stok_saat_ini'=>(int)$s->jumlah_stok];
        })->values()->toArray();

        $todayTrxFull = \App\Models\Transaction::with('items')->whereDate('created_at',today())->get()->map(fn($t)=>[
            'id'=>$t->id,'created_at'=>$t->created_at->format('d M Y H:i'),'payment_method'=>$t->payment_method,
            'kasir'=>optional(auth()->user())->name??'—','sub_total'=>(int)$t->sub_total,'discount'=>(int)$t->discount,
            'total'=>(int)$t->total,'money_paid'=>(int)($t->money_paid??$t->total),
            'items'=>$t->items->map(fn($i)=>['nama_item'=>$i->nama_item,'qty'=>(int)$i->qty,'unit_price'=>(int)$i->unit_price,'subtotal'=>(int)$i->subtotal])->toArray(),
        ])->values()->toArray();
    @endphp

    const MENU_DATA      = @json($menuData);
    const STOCK_LIST     = @json($stockList);
    const STOCK_SNAPSHOT = @json($stockSnapshot);
    const KASIR_NAME     = @json($selectedShift?->user->name ?? (auth()->user()->name ?? '—'));
    const TANGGAL_HARI   = @json(now()->translatedFormat('d F Y'));
    const CHECKOUT_URL   = "{{ route('cashier.pos.checkout') }}";
    const SYNC_SHEETS_URL= "{{ url('/cashier/sync-sheets') }}";
    const CSRF_TOKEN     = document.querySelector('meta[name="csrf-token"]').content;

    let todayTrxList     = @json($todayTrxFull);
    let cart             = [];
    let activeMenu       = null;
    let modalQty         = 1;
    let currentPay       = '{{ strtolower(str_replace(' ', '', $paymentMethods[0] ?? 'normal')) }}';
    let lastTrxSnapshot  = null;
    let stockMutasiMap   = {};

    STOCK_SNAPSHOT.forEach(s => {
        stockMutasiMap[s.pcs_id] = { pcs_name: s.pcs_name, stok_awal: s.stok_saat_ini, total_dikurangi: 0, stok_akhir: s.stok_saat_ini };
    });

    const BRAND = 'Warung Tahu Bakso';
    const METHOD_LABELS = { cash:'Tunai', qris:'QRIS', gofood:'GoFood', shopeefood:'ShopeeFood' };

    function fmt(n) { return 'Rp' + Number(n||0).toLocaleString('id-ID',{maximumFractionDigits:0}); }
    function mStkClass(s) { return s<=5?'mstk mstk-red':s<=10?'mstk mstk-yellow':s<=20?'mstk mstk-orange':'mstk mstk-ok'; }
    function getPrice(menu) { return Number(menu.prices[currentPay])||Number(Object.values(menu.prices)[0])||0; }
    function nowStr() { const d=new Date(); return d.toLocaleDateString('id-ID',{day:'numeric',month:'long',year:'numeric'})+' '+d.toLocaleTimeString('id-ID',{hour:'2-digit',minute:'2-digit'}); }
    function trxNo(id) { return 'TRX-'+String(id||0).padStart(5,'0'); }
    function openModal(id) { document.getElementById(id).style.display='flex'; }
    function closeModal(id) { document.getElementById(id).style.display='none'; }
    function closeModalOutside(e,id) { if(e.target.id===id)closeModal(id); }

    function updateStats() {
        const s=todayTrxList.reduce((a,t)=>a+(t.total||0),0);
        document.getElementById('stat-sales').textContent=fmt(s);
        document.getElementById('stat-trx').textContent=todayTrxList.length;
        document.getElementById('stat-items').textContent=todayTrxList.reduce((a,t)=>a+(t.items||[]).reduce((b,i)=>b+(i.qty||0),0),0);
    }

    function openRiwayatModal(){
        document.getElementById('riwayat-tanggal').textContent=TANGGAL_HARI;
        const list=document.getElementById('riwayat-list');
        if(!todayTrxList.length){list.innerHTML='<p style="color:#9ca3af;font-size:13px;padding:16px">Belum ada transaksi hari ini.</p>';openModal('modal-riwayat');return;}
        list.innerHTML=[...todayTrxList].reverse().map(t=>`
            <div style="border:1px solid #F0E9DC;border-radius:12px;padding:14px;margin-bottom:10px">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                    <div><span style="font-weight:800;font-size:13px">${trxNo(t.id)}</span><span style="font-size:11px;color:#9ca3af;margin-left:8px">${t.created_at}</span></div>
                    <div style="display:flex;gap:8px;align-items:center">
                        <span style="font-size:12px;background:#fef2f2;color:#C0392B;padding:2px 10px;border-radius:99px;border:1px solid #fca5a5">${METHOD_LABELS[t.payment_method]??t.payment_method}</span>
                        <span style="font-weight:800;color:#C0392B">${fmt(t.total)}</span>
                        <button onclick="cetakUlang(${t.id})" style="padding:5px 12px;border-radius:8px;border:1px solid #F0E9DC;background:#fff;font-size:11px;font-weight:700;cursor:pointer;color:#374151">🖨 Cetak</button>
                    </div>
                </div>
                <div style="font-size:12px;color:#6b7280">${(t.items||[]).map(i=>`${i.nama_item} ×${i.qty} = ${fmt(i.subtotal)}`).join(' · ')}</div>
                ${t.discount>0?`<div style="font-size:11px;color:#d97706;margin-top:4px">Diskon: ${fmt(t.discount)}</div>`:''}
            </div>`).join('');
        openModal('modal-riwayat');
    }

    function cetakUlang(trxId){
        const t=todayTrxList.find(x=>x.id===trxId);if(!t)return;
        document.getElementById('rc-datetime').textContent=t.created_at;
        document.getElementById('rc-trxno').textContent=trxNo(t.id);
        document.getElementById('rc-method').textContent=METHOD_LABELS[t.payment_method]??t.payment_method;
        document.getElementById('rc-items').innerHTML=(t.items||[]).map(item=>`<div class="rc-item-row"><div><div class="rc-item-name">${item.nama_item}</div><div class="rc-item-detail">${item.qty} x ${fmt(item.unit_price)}</div></div><div class="rc-item-sub">${fmt(item.subtotal)}</div></div>`).join('');
        document.getElementById('rc-sub').textContent=fmt(t.sub_total);
        document.getElementById('rc-disc').textContent=fmt(t.discount);
        document.getElementById('rc-total').textContent=fmt(t.total);
        document.getElementById('rc-paid').textContent=fmt(t.money_paid??t.total);
        document.getElementById('rc-change').textContent=fmt(Math.max(0,(t.money_paid??t.total)-t.total));
        closeModal('modal-riwayat');openModal('modal-receipt');
    }

    function setCheckoutPayment(btn,method){
        document.querySelectorAll('#co-pay-methods .pay-btn').forEach(b=>b.classList.remove('active'));
        btn.classList.add('active');
        setPaymentMethod(method);
        const disc=Math.max(0,parseFloat(document.getElementById('discount').value)||0);
        const sub=cart.reduce((s,i)=>s+i.subtotal,0);
        const total=Math.max(0,sub-disc);
        const paid=parseFloat(document.getElementById('money-paid').value)||0;
        document.getElementById('co-total').textContent=fmt(total);
        document.getElementById('co-change').textContent=fmt(Math.max(0,paid-total));
        document.getElementById('co-method').textContent=METHOD_LABELS[method]??method;
    }

    function setPaymentMethod(method){
        currentPay=method;
        document.querySelectorAll('#pay-methods .pay-btn').forEach(b=>b.classList.toggle('active',b.dataset.method===method));
        cart=cart.map(item=>{if(item.custom)return item;const m=MENU_DATA.find(x=>x.id===item.menuId);if(!m)return item;const up=getPrice(m);return{...item,unitPrice:up,subtotal:up*item.qty};});
        renderMenuPrices();renderCart();
    }

    function setPayment(btn,method){
        document.querySelectorAll('.pay-btn').forEach(b=>b.classList.remove('active'));
        btn.classList.add('active');currentPay=method;
        cart=cart.map(item=>{if(item.custom)return item;const m=MENU_DATA.find(x=>x.id===item.menuId);if(!m)return item;const up=getPrice(m);return{...item,unitPrice:up,subtotal:up*item.qty};});
        renderMenuPrices();renderCart();
    }

    function renderMenuPrices(){
        document.querySelectorAll('.menu-price-tag').forEach(el=>{
            const id=Number(el.dataset.menuId);const m=MENU_DATA.find(x=>x.id===id);
            el.textContent=m?fmt(getPrice(m)):'—';
        });
    }

    function openMenuModal(menuId){
        activeMenu=MENU_DATA.find(m=>m.id===menuId);if(!activeMenu)return;
        modalQty=1;
        document.getElementById('mm-title').textContent=activeMenu.name;
        document.getElementById('mm-desc').textContent=activeMenu.desc||'Tidak ada deskripsi.';
        document.getElementById('mm-price').textContent=fmt(getPrice(activeMenu));
        document.getElementById('mm-qty').textContent='1';
        const stocksEl=document.getElementById('mm-stocks');
        stocksEl.innerHTML='';
        if(!activeMenu.details.length){stocksEl.innerHTML='<p style="color:#9ca3af;font-size:12px">Tidak ada data bahan terdaftar.</p>';}
        else{activeMenu.details.forEach(d=>{stocksEl.innerHTML+=`<div class="${mStkClass(d.stock)}"><h5>${d.pcs_name}</h5><span>Per porsi: ${d.qty} pcs · Sisa stok: <strong>${d.stock} pcs</strong>${d.stock<=20?' ⚠':''}</span></div>`;});}
        openModal('modal-menu');
    }

    function adjModalQty(delta){modalQty=Math.max(1,modalQty+delta);document.getElementById('mm-qty').textContent=modalQty;}

    function addToCartFromModal(){
        if(!activeMenu)return;
        const up=getPrice(activeMenu);
        const ex=cart.find(i=>i.menuId===activeMenu.id&&!i.custom);
        if(ex){ex.qty+=modalQty;ex.subtotal=ex.unitPrice*ex.qty;}
        else{cart.push({menuId:activeMenu.id,name:activeMenu.name,qty:modalQty,unitPrice:up,subtotal:up*modalQty,custom:false,details:activeMenu.details});}
        closeModal('modal-menu');renderCart();
    }

    function addCustomMenu(){
        const name=document.getElementById('custom-name').value.trim();
        const price=parseFloat(document.getElementById('custom-price').value)||0;
        const qty=Math.max(1,parseInt(document.getElementById('custom-qty').value)||1);
        if(!name){alert('Masukkan nama item tambahan.');return;}
        if(price<=0){alert('Masukkan harga yang valid.');return;}
        cart.push({menuId:null,name,qty,unitPrice:price,subtotal:price*qty,custom:true,details:[]});
        document.getElementById('custom-name').value='';
        document.getElementById('custom-price').value='';
        document.getElementById('custom-qty').value=1;
        renderCart();
    }

    function removeCartItem(index){cart.splice(index,1);renderCart();}

    function renderCart(){
        const container=document.getElementById('cart-items-container');
        const empty=document.getElementById('cart-empty');
        const summary=document.getElementById('cart-summary');
        const moneyEl=document.getElementById('money-section');
        container.innerHTML='';
        if(!cart.length){empty.style.display='';summary.style.display='none';moneyEl.style.display='none';return;}
        empty.style.display='none';summary.style.display='';moneyEl.style.display='';
        cart.forEach((item,i)=>{
            const div=document.createElement('div');
            div.className='cart-item-row';
            div.innerHTML=`<div><div class="ci-name">${item.name}${item.custom?'<span class="custom-badge">custom</span>':''}</div><div class="ci-qty">x${item.qty} × ${fmt(item.unitPrice)}</div></div><div style="text-align:right"><div class="ci-price">${fmt(item.subtotal)}</div><div class="ci-remove" onclick="removeCartItem(${i})">✕ hapus</div></div>`;
            container.appendChild(div);
        });
        const disc=Math.max(0,parseFloat(document.getElementById('discount').value)||0);
        const sub=cart.reduce((s,i)=>s+i.subtotal,0);
        const total=Math.max(0,sub-disc);
        const items=cart.reduce((s,i)=>s+i.qty,0);
        document.getElementById('sum-items').textContent=items;
        document.getElementById('sum-sub').textContent=fmt(sub);
        document.getElementById('sum-disc').textContent=fmt(disc);
        document.getElementById('sum-total').textContent=fmt(total);
        calcChange();
    }

    function calcChange(){
        const disc=Math.max(0,parseFloat(document.getElementById('discount').value)||0);
        const sub=cart.reduce((s,i)=>s+i.subtotal,0);
        const total=Math.max(0,sub-disc);
        const paid=parseFloat(document.getElementById('money-paid').value)||0;
        document.getElementById('change-display').textContent=fmt(Math.max(0,paid-total));
    }

    function openCheckoutModal(){
        if(!cart.length){alert('Keranjang masih kosong. Tambahkan menu terlebih dahulu.');return;}
        const disc=Math.max(0,parseFloat(document.getElementById('discount').value)||0);
        const sub=cart.reduce((s,i)=>s+i.subtotal,0);
        const total=Math.max(0,sub-disc);
        const paid=parseFloat(document.getElementById('money-paid').value)||0;
        const change=Math.max(0,paid-total);
        const stockImpact={};
        cart.forEach(item=>{(item.details||[]).forEach(d=>{if(!d.pcs_id&&!d.pcs_name)return;const key=d.pcs_name;if(!stockImpact[key])stockImpact[key]={stock:d.stock,used:0};stockImpact[key].used+=d.qty*item.qty;});});
        const coStocks=document.getElementById('co-stocks');
        coStocks.innerHTML='';
        if(!Object.keys(stockImpact).length){coStocks.innerHTML='<p style="color:#9ca3af;font-size:12px">Tidak ada bahan inventori yang terpengaruh.</p>';}
        else{Object.entries(stockImpact).forEach(([name,v])=>{const sisa=v.stock-v.used;coStocks.innerHTML+=`<div class="${mStkClass(sisa)}" style="margin-bottom:6px"><h5>${name}</h5><span>Sisa: <strong>${v.stock} pcs</strong> − ${v.used} pcs = <strong>${sisa} pcs</strong>${sisa<=20?' ⚠':''}</span></div>`;});}
        document.getElementById('co-items').innerHTML=cart.map(item=>`<li><div><div class="cn">${item.name}</div><div class="cq">x${item.qty}</div></div><div class="cs">${fmt(item.subtotal)}</div></li>`).join('');
        document.getElementById('co-total').textContent=fmt(total);
        document.getElementById('co-change').textContent=fmt(change);
        document.getElementById('co-method').textContent=METHOD_LABELS[currentPay]??currentPay;
        const coPayMethods = document.getElementById('co-pay-methods');
            if (coPayMethods) {
                const payLabels = {'cash':'Cash','qris':'QRIS','gofood':'GoFood','shopeefood':'ShopeeFood'};
                coPayMethods.innerHTML = ['cash','qris','gofood','shopeefood'].map(m =>
                    `<button class="pay-btn ${currentPay === m ? 'active' : ''}"
                        onclick="setCheckoutPayment(this,'${m}')"
                        data-method="${m}">${payLabels[m]}</button>`
                ).join('');
            }
        openModal('modal-checkout');
    }

    async function saveTransaction(){
        const btnConfirm=document.getElementById('btn-confirm-checkout');
        const btnCancel=document.getElementById('btn-cancel-checkout');
        btnConfirm.disabled=true;btnConfirm.innerHTML='<span class="spinner"></span> Menyimpan...';btnCancel.disabled=true;
        const disc=Math.max(0,parseFloat(document.getElementById('discount').value)||0);
        const sub=cart.reduce((s,i)=>s+i.subtotal,0);
        const total=Math.max(0,sub-disc);
        const paid=parseFloat(document.getElementById('money-paid').value)||0;
        const change=Math.max(0,paid-total);
        try{
            const formData=new FormData();
            formData.append('_token',CSRF_TOKEN);formData.append('payment_method',currentPay);
            formData.append('discount',disc);formData.append('cart',JSON.stringify(cart));
            const response=await fetch(CHECKOUT_URL,{method:'POST',body:formData,headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}});
            const result=await response.json();
            if(!response.ok||result.success===false)throw new Error(result.message||'Transaksi gagal.');
            const newId=result.id||Date.now();
            lastTrxSnapshot={id:newId,created_at:nowStr(),payment_method:currentPay,sub_total:sub,discount:disc,total,money_paid:paid,kasir:KASIR_NAME,
                items:cart.map(item=>({nama_item:item.name,qty:item.qty,unit_price:item.unitPrice,subtotal:item.subtotal,is_custom:item.custom,
                    bahan_dikurangi:(item.details||[]).map(d=>({pcs_id:d.pcs_id,pcs_name:d.pcs_name,qty_per_porsi:d.qty,total_dikurangi:d.qty*item.qty}))}))};
            cart.forEach(item=>{(item.details||[]).forEach(d=>{if(!d.pcs_id)return;if(!stockMutasiMap[d.pcs_id]){stockMutasiMap[d.pcs_id]={pcs_name:d.pcs_name,stok_awal:d.stock,total_dikurangi:0,stok_akhir:d.stock};}const reduced=d.qty*item.qty;stockMutasiMap[d.pcs_id].total_dikurangi+=reduced;stockMutasiMap[d.pcs_id].stok_akhir=stockMutasiMap[d.pcs_id].stok_awal-stockMutasiMap[d.pcs_id].total_dikurangi;});});
            todayTrxList=[...todayTrxList,lastTrxSnapshot];updateStats();closeModal('modal-checkout');
            showReceipt(sub,disc,total,paid,change,newId);await refreshStocks();await autoSyncToSheets();
            document.getElementById('ajax-error').style.display='none';
        }catch(err){
            document.getElementById('ajax-error').textContent=err.message||'Terjadi kesalahan. Coba lagi';
            document.getElementById('ajax-error').style.display='';closeModal('modal-checkout');
        }finally{btnConfirm.disabled=false;btnConfirm.innerHTML='✓ Ya, Simpan Transaksi';btnCancel.disabled=false;}
    }

    async function refreshStocks(){
        try{const r=await fetch(window.location.href,{headers:{'X-Requested-With':'XMLHttpRequest'}});const html=await r.text();const doc=new DOMParser().parseFromString(html,'text/html');const ng=doc.querySelector('#stock-grid');if(ng)document.querySelector('#stock-grid').innerHTML=ng.innerHTML;}
        catch(err){console.error('Gagal refresh stok:',err);}
    }

    function showReceipt(sub,disc,total,paid,change,id){
        document.getElementById('rc-datetime').textContent=nowStr();
        document.getElementById('rc-trxno').textContent=trxNo(id);
        document.getElementById('rc-method').textContent=METHOD_LABELS[currentPay]??currentPay;
        document.getElementById('rc-items').innerHTML=cart.map(item=>`<div class="rc-item-row"><div><div class="rc-item-name">${item.name}</div><div class="rc-item-detail">${item.qty} x ${fmt(item.unitPrice)}</div></div><div class="rc-item-sub">${fmt(item.subtotal)}</div></div>`).join('');
        document.getElementById('rc-sub').textContent=fmt(sub);document.getElementById('rc-disc').textContent=fmt(disc);
        document.getElementById('rc-total').textContent=fmt(total);document.getElementById('rc-paid').textContent=fmt(paid);
        document.getElementById('rc-change').textContent=fmt(change);
        const st=document.getElementById('sync-status-receipt');st.style.display='none';st.className='sync-status';
        openModal('modal-receipt');
    }

    function resetAndClose(){
        cart=[];document.getElementById('discount').value=0;document.getElementById('money-paid').value=0;renderCart();closeModal('modal-receipt');
        const lastSyncDate=localStorage.getItem('synced_date');
        if(lastSyncDate!==TANGGAL_HARI){localStorage.removeItem('synced_trx_ids');localStorage.setItem('synced_date',TANGGAL_HARI);}
    }

    function openLaporanModal(){
        const totalSales=todayTrxList.reduce((s,t)=>s+(t.total||0),0);
        const totalDisc=todayTrxList.reduce((s,t)=>s+(t.discount||0),0);
        const totalTrx=todayTrxList.length;
        const totalItems=todayTrxList.reduce((s,t)=>s+(t.items||[]).reduce((si,i)=>si+(i.qty||0),0),0);
        document.getElementById('lap-tanggal').textContent=TANGGAL_HARI;
        document.getElementById('lap-sales').textContent=fmt(totalSales);
        document.getElementById('lap-trx').textContent=totalTrx;
        document.getElementById('lap-items').textContent=totalItems;
        document.getElementById('lap-disc').textContent=fmt(totalDisc);
        const st=document.getElementById('sync-status-laporan');st.style.display='none';st.className='sync-status';
        openModal('modal-laporan');
    }

    function buildSheetsPayload(){
        const syncedIds=JSON.parse(localStorage.getItem('synced_trx_ids')||'[]');
        const newTrx=todayTrxList.filter(t=>!syncedIds.includes(t.id));
        const ringkasan={tanggal:TANGGAL_HARI,kasir:KASIR_NAME,jumlah_transaksi:todayTrxList.length,item_terjual:todayTrxList.reduce((s,t)=>s+(t.items||[]).reduce((si,i)=>si+(i.qty||0),0),0),total_diskon:todayTrxList.reduce((s,t)=>s+(t.discount||0),0),total_penjualan:todayTrxList.reduce((s,t)=>s+(t.total||0),0)};
        const detailRows=[];
        newTrx.forEach(t=>{(t.items||[]).forEach(item=>{const bahanStr=(item.bahan_dikurangi||[]).map(b=>`${b.pcs_name} -${b.total_dikurangi}pcs`).join(' | ')||(item.is_custom?'Custom':'—');detailRows.push({tanggal:t.created_at,no_transaksi:trxNo(t.id),kasir:t.kasir||KASIR_NAME,metode_bayar:METHOD_LABELS[t.payment_method]??t.payment_method,nama_item:item.nama_item,is_custom:item.is_custom?'Ya':'Tidak',qty:item.qty,harga_satuan:item.unit_price,subtotal_item:item.subtotal,diskon_transaksi:t.discount,total_transaksi:t.total,bahan_dikurangi:bahanStr});});});
        const mutasiMap={};
        newTrx.forEach(t=>{(t.items||[]).forEach(item=>{(item.bahan_dikurangi||[]).forEach(b=>{if(!b.pcs_id)return;if(!mutasiMap[b.pcs_id]){const snap=STOCK_SNAPSHOT.find(s=>s.pcs_id==b.pcs_id);mutasiMap[b.pcs_id]={pcs_name:b.pcs_name,stok_awal:snap?.stok_saat_ini??0,total_dikurangi:0};}mutasiMap[b.pcs_id].total_dikurangi+=b.total_dikurangi;});});});
        const mutasiRows=Object.entries(mutasiMap).map(([pcsId,v])=>({tanggal:TANGGAL_HARI,pcs_id:pcsId,nama_bahan:v.pcs_name,stok_awal:v.stok_awal,total_dikurangi:v.total_dikurangi,stok_akhir:v.stok_awal-v.total_dikurangi}));
        const allSyncedIds=[...new Set([...syncedIds,...newTrx.map(t=>t.id)])];
        localStorage.setItem('synced_trx_ids',JSON.stringify(allSyncedIds));
        return{ringkasan,detail_transaksi:detailRows,mutasi_stok:mutasiRows};
    }

    function loadSheetsConfig(){const id=localStorage.getItem('sheets_id')||'';const inputId=document.getElementById('sheets-id-input');if(inputId)inputId.value=id;}
    function saveSheetsId(){localStorage.setItem('sheets_id',document.getElementById('sheets-id-input').value.trim());}
    function openSheetsModal(){loadSheetsConfig();const st=document.getElementById('sync-status-modal');st.style.display='none';st.className='sync-status';openModal('modal-sheets');}
    function showSyncStatus(elId,type,msg){const el=document.getElementById(elId);el.className='sync-status '+(type==='ok'?'sync-ok':type==='err'?'sync-err':'sync-wait');el.textContent=msg;el.style.display='';}
    function syncToSheets(){loadSheetsConfig();closeModal('modal-receipt');closeModal('modal-laporan');const st=document.getElementById('sync-status-modal');st.style.display='none';st.className='sync-status';openModal('modal-sheets');}

    async function doSync(){
        const spreadsheetId=(localStorage.getItem('sheets_id')||'').trim();
        if(!spreadsheetId){showSyncStatus('sync-status-modal','err','❌ Masukkan Spreadsheet ID terlebih dahulu.');return;}
        const btn=document.getElementById('btn-do-sync');
        btn.disabled=true;btn.innerHTML='<span class="spinner"></span> Mengirim data...';
        showSyncStatus('sync-status-modal','wait','⏳ Menghubungkan ke Google Sheets...');
        const payload={spreadsheet_id:spreadsheetId,...buildSheetsPayload()};
        try{
            const res=await fetch(SYNC_SHEETS_URL,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},body:JSON.stringify(payload)});
            if(res.status===404){showSyncStatus('sync-status-modal','err','⚠️ Endpoint /cashier/sync-sheets belum ada.');return;}
            const data=await res.json();
            if(!res.ok||data.success===false)throw new Error(data.message||'Sync gagal.');
            showSyncStatus('sync-status-modal','ok','✅ Data berhasil dikirim ke 3 tab Google Sheets!');
        }catch(err){showSyncStatus('sync-status-modal','err','❌ '+(err.message||'Terjadi kesalahan saat sync.'));}
        finally{btn.disabled=false;btn.innerHTML='📊 Sync Sekarang';}
    }

    async function autoSyncToSheets(){
        const spreadsheetId=(localStorage.getItem('sheets_id')||'').trim();if(!spreadsheetId)return;
        try{const payload={spreadsheet_id:spreadsheetId,...buildSheetsPayload()};await fetch(SYNC_SHEETS_URL,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF_TOKEN,'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},body:JSON.stringify(payload)});}
        catch(err){console.warn('Auto-sync Sheets gagal (silent):',err.message);}
    }

    renderMenuPrices();renderCart();loadSheetsConfig();

    (function initSyncedIds(){
        const today=TANGGAL_HARI;const lastSyncDate=localStorage.getItem('synced_date');
        if(lastSyncDate!==today){localStorage.removeItem('synced_trx_ids');localStorage.setItem('synced_date',today);}
        const existingIds=todayTrxList.map(t=>t.id);
        const alreadySynced=JSON.parse(localStorage.getItem('synced_trx_ids')||'[]');
        localStorage.setItem('synced_trx_ids',JSON.stringify([...new Set([...alreadySynced,...existingIds])]));
    })();

    const CREATE_SHEET_URL="{{ route('cashier.create-spreadsheet') }}";
</script>

</x-layouts.app>