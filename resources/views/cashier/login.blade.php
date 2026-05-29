@props(['title' => 'Login Kasir'])

<x-layouts.app :title="$title">
    <div class="mx-auto max-w-xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <h1 class="text-3xl font-semibold text-slate-900">Login Kasir</h1>
            <p class="mt-2 text-slate-600">Gunakan akun kasir untuk masuk ke sistem POS.</p>

            @if ($errors->any())
                <div class="mt-6 rounded-2xl bg-rose-50 p-4 text-rose-700 ring-1 ring-rose-200">
                    <p class="font-semibold">Terjadi kesalahan:</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('cashier.login.submit') }}" method="POST" class="mt-8 space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-slate-700">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="mt-2 w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700">Password</label>
                    <input type="password" name="password" required class="mt-2 w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" />
                </div>

                <button type="submit" class="w-full rounded-2xl bg-indigo-600 px-4 py-3 text-white transition hover:bg-indigo-700">Masuk sebagai Kasir</button>
            </form>
        </div>
    </div>
</x-layouts.app>
