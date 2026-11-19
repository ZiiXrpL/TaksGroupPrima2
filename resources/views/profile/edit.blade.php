<x-layouts.app title="Profil Saya">
    <div class="mx-auto max-w-2xl">
        <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm sm:p-8">

            <div class="mb-8 flex flex-col items-center text-center">
                @if ($user->profile_photo_path)
                    <img data-profile-avatar src="{{ route('profile.photo', ['v' => sha1($user->profile_photo_path)]) }}" alt="Foto profil {{ $user->name }}" class="h-20 w-20 rounded-full object-cover shadow-md shadow-indigo-200">
                @else
                    <img data-profile-avatar alt="Pratinjau foto profil" class="hidden h-20 w-20 rounded-full object-cover shadow-md shadow-indigo-200">
                    <div data-profile-initials class="flex h-20 w-20 items-center justify-center rounded-full bg-indigo-600 text-2xl font-bold text-white shadow-md shadow-indigo-200">
                        {{ collect(explode(' ', $user->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('') }}
                    </div>
                @endif
                <h3 class="mt-4 text-xl font-bold text-slate-900">{{ $user->name }}</h3>
                <p class="text-sm capitalize text-slate-400">{{ $user->role }}</p>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <x-label for="profile_photo" value="Foto Profil" />
                    <input id="profile_photo" name="profile_photo" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        class="block w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700 shadow-sm file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100">
                    <p id="profile-photo-status" class="mt-1 text-xs text-slate-500">Pilih foto untuk mengatur potongan dan pembesaran. Format JPG, PNG, atau WebP; hasil maksimal 2 MB.</p>
                    <x-input-error :messages="$errors->get('profile_photo')" />
                </div>

                <div>
                    <x-label for="name" value="Nama Lengkap" />
                    <x-input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus />
                    <x-input-error :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-label for="email" value="Email" />
                    <x-input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required />
                    <x-input-error :messages="$errors->get('email')" />
                </div>

                @if ($user->needsClassProfile())
                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        Lengkapi kelas dan nomor absen Anda untuk melanjutkan ke dashboard siswa.
                    </div>
                @endif

                @if ($user->isSiswa())
                    <div>
                        <x-label for="class_id" value="Kelas dan Jurusan" />
                        @if ($user->class_id)
                            <x-input id="class_id" type="text" value="{{ $user->schoolClass?->name }} · {{ $user->schoolClass?->major ?? 'Tanpa jurusan' }}" disabled />
                        @else
                            <x-select id="class_id" name="class_id" required>
                                <option value="">Pilih kelas dan jurusan</option>
                                @foreach ($classes as $schoolClass)
                                    <option value="{{ $schoolClass->id }}" @selected((string) old('class_id') === (string) $schoolClass->id)>
                                        {{ $schoolClass->name }} · {{ $schoolClass->major ?? 'Tanpa jurusan' }} · {{ $schoolClass->academic_year }}
                                    </option>
                                @endforeach
                            </x-select>
                        @endif
                        <x-input-error :messages="$errors->get('class_id')" />
                    </div>

                    <div>
                        <x-label for="attendance_number" value="Nomor Absen" />
                        @if ($user->attendance_number !== null)
                            <x-input id="attendance_number" type="text" value="{{ $user->attendanceLabel() }}" disabled />
                            <p class="mt-1 text-xs text-slate-500">Nomor absen hanya dapat diubah oleh admin.</p>
                        @else
                            <x-input id="attendance_number" name="attendance_number" type="number" min="1" max="99" inputmode="numeric" value="{{ old('attendance_number') }}" placeholder="Contoh: 03" required />
                            <p class="mt-1 text-xs text-slate-500">Angka positif, unik di dalam kelas Anda. Contoh: 03.</p>
                        @endif
                        <x-input-error :messages="$errors->get('attendance_number')" />
                    </div>
                @endif

                <div class="border-t border-slate-100 pt-5">
                    <p class="mb-4 text-sm font-bold text-slate-900">Ubah Kata Sandi</p>

                    <div class="space-y-4">
                        <div>
                            <x-label for="current_password" value="Kata Sandi Lama" />
                            <x-input id="current_password" name="current_password" type="password" placeholder="Masukkan kata sandi lama Anda" autocomplete="current-password" />
                            <x-input-error :messages="$errors->get('current_password')" />
                        </div>

                        <div>
                            <x-label for="password" value="Kata Sandi Baru" />
                            <x-input id="password" name="password" type="password" placeholder="Masukkan kata sandi baru" autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" />
                        </div>

                        <div>
                            <x-label for="password_confirmation" value="Konfirmasi Kata Sandi Baru" />
                            <x-input id="password_confirmation" name="password_confirmation" type="password" placeholder="Ulangi kata sandi baru" autocomplete="new-password" />
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <x-primary-button>{{ $user->needsClassProfile() ? 'Simpan dan Lanjutkan' : 'Simpan Perubahan' }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>

    <div id="profile-crop-dialog" class="fixed inset-0 z-[100] hidden items-center justify-center overflow-y-auto bg-slate-950/70 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="profile-crop-title">
        <div class="my-auto max-h-[calc(100dvh-2rem)] w-full max-w-md overflow-y-auto rounded-2xl bg-white p-5 shadow-2xl sm:p-6">
            <div class="mb-5 flex items-start justify-between gap-4">
                <div>
                    <h2 id="profile-crop-title" class="text-lg font-bold text-slate-900">Atur foto profil</h2>
                    <p class="mt-1 text-sm text-slate-500">Geser foto dan atur pembesaran di dalam lingkaran.</p>
                </div>
                <button id="profile-crop-close" type="button" class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900" aria-label="Tutup editor foto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"/></svg>
                </button>
            </div>

            <div class="mx-auto aspect-square w-full max-w-[min(320px,45dvh)] overflow-hidden rounded-full bg-slate-950 ring-4 ring-indigo-100">
                <canvas id="profile-crop-canvas" class="block h-full w-full touch-none cursor-grab active:cursor-grabbing" width="512" height="512" aria-label="Geser foto untuk memilih potongan"></canvas>
            </div>

            <div class="mt-5">
                <div class="mb-2 flex items-center justify-between text-xs font-medium text-slate-500">
                    <label for="profile-crop-zoom">Perbesar</label>
                    <span id="profile-crop-zoom-value">100%</span>
                </div>
                <input id="profile-crop-zoom" type="range" min="1" max="3" step="0.01" value="1" class="w-full accent-indigo-600">
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button id="profile-crop-cancel" type="button" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Batal</button>
                <button id="profile-crop-apply" type="button" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus-visible:outline-indigo-600">Gunakan foto</button>
            </div>
        </div>
    </div>
</x-layouts.app>
