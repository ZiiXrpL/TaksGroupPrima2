<x-layouts.guest title="Registrasi Siswa">
    <div class="mx-auto grid w-full max-w-4xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl shadow-slate-950/20 lg:grid-cols-[0.85fr_1.15fr]">
        <section class="relative hidden overflow-hidden bg-gradient-to-br from-blue-700 via-indigo-700 to-violet-800 p-10 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full border-[30px] border-white/10"></div>
            <div class="absolute -bottom-24 -left-24 h-72 w-72 rounded-full border-[40px] border-white/10"></div>
            <div class="relative">
                <div class="mb-10 flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/15 text-sm font-extrabold ring-1 ring-white/20">TG</div>
                    <span class="text-lg font-bold">TaskGroup</span>
                </div>
                <p class="mb-4 text-sm font-semibold uppercase text-blue-200">Akun Siswa</p>
                <h1 class="max-w-sm text-4xl font-bold leading-tight">Mulai belajar dan berkolaborasi</h1>
                <p class="mt-5 max-w-sm text-sm leading-6 text-blue-100">Buat akun untuk melihat tugas, bekerja bersama kelompok, dan mengikuti progres belajar.</p>
            </div>
            <p class="relative text-sm text-blue-100">Untuk tim sekolah yang aktif</p>
        </section>

        <section class="bg-white p-6 sm:p-10 lg:p-12">
            <div class="mb-6 lg:hidden">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 text-sm font-extrabold text-white shadow-lg shadow-blue-200">TG</div>
                <p class="text-sm font-semibold uppercase text-blue-600">TaskGroup</p>
            </div>

            <div class="mb-6">
                <h2 class="text-3xl font-bold text-slate-900">Buat akun siswa</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">Isi data berikut untuk memulai.</p>
            </div>

            <form method="POST" action="{{ route('register.store') }}" class="space-y-3.5">
                @csrf

                <div>
                    <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Nama lengkap</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Nama sesuai data sekolah"
                        class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 hover:border-blue-300 hover:bg-white focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100">
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="nama@sekolah.id"
                        class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 hover:border-blue-300 hover:bg-white focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100">
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">Kata sandi</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Buat kata sandi"
                        class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 hover:border-blue-300 hover:bg-white focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100">
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>

                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-slate-700">Ulangi kata sandi</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Masukkan ulang kata sandi"
                        class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 hover:border-blue-300 hover:bg-white focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100">
                </div>

                <button type="submit" class="group mt-1 flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-4 font-semibold text-white shadow-lg shadow-blue-200 transition hover:-translate-y-0.5 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-blue-200">
                    <span>Daftar sebagai siswa</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-500">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-semibold text-blue-700 hover:text-blue-800 hover:underline">Masuk</a>
            </p>
        </section>
    </div>
</x-layouts.guest>