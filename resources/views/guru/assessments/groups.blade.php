<x-layouts.app title="Penilaian Kelompok" role="guru">
    <div class="space-y-4">
        <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm sm:p-6">
            <h3 class="text-xl font-bold text-slate-900">Penilaian Kelompok</h3>
            <p class="mt-1 text-sm text-slate-500">
                Klik nama kelompok untuk melihat anggota dan memberikan nilai kepada setiap siswa secara terpisah.
            </p>
        </div>

        <form method="GET" action="{{ route('guru.assessments.groups') }}" class="flex flex-wrap gap-2">
            <div class="w-full max-w-xs">
                <x-input type="text" name="search" value="{{ $search }}" placeholder="Cari judul tugas atau kelompok..." />
            </div>

            <div class="w-full sm:w-52">
                <x-select name="status">
                    <option value="">Menunggu Review &amp; Sudah Dinilai</option>
                    <option value="submitted" @selected($status === 'submitted')>Menunggu Review Saja</option>
                    <option value="completed" @selected($status === 'completed')>Sudah Dinilai Saja</option>
                </x-select>
            </div>

            <x-primary-button type="submit" class="shrink-0">Filter</x-primary-button>

            @if ($search !== '' || $status !== '')
                <a href="{{ route('guru.assessments.groups') }}" class="inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-500 hover:bg-gray-100">
                    Reset
                </a>
            @endif
        </form>

        @if ($rows->isEmpty())
            <div class="rounded-2xl border border-slate-100 bg-white p-8 text-center text-sm text-slate-400 shadow-sm">
                Belum ada kelompok yang perlu dinilai. Kelompok akan muncul di sini setelah tugas dikumpulkan untuk review.
            </div>
        @else
            <div class="space-y-3">
                @foreach ($rows as $row)
                    @php($memberAssessments = $row->assessments->keyBy('student_id'))
                    <details class="group overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" @if ($row->status === \App\Models\TaskGroup::STATUS_SUBMITTED || $errors->any()) open @endif>
                        <summary class="flex cursor-pointer list-none flex-col gap-2 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $row->group?->name ?? 'Kelompok' }}</p>
                                <p class="text-xs text-slate-400">
                                    {{ $row->group?->schoolClass?->name }} &middot; {{ $row->task->title }}
                                    @if ($row->submitted_at)
                                        &middot; Dikumpulkan {{ $row->submitted_at->diffForHumans() }}
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                @if ($row->status === \App\Models\TaskGroup::STATUS_COMPLETED)
                                    <x-badge color="green">Anggota sudah dinilai</x-badge>
                                @else
                                    <x-badge color="yellow">Menunggu Review</x-badge>
                                @endif
                                <span class="text-xs text-slate-400 group-open:hidden">Klik nama kelompok untuk melihat anggota</span>
                            </div>
                        </summary>

                        <div class="space-y-3 border-t border-gray-100 px-5 py-4">
                            @forelse ($row->group?->members ?? collect() as $member)
                                @php($assessment = $memberAssessments->get($member->id))
                                <form method="POST" action="{{ route('guru.tasks.groups.students.assessment.update', [$row->task, $row->group, $member]) }}" class="rounded-xl border border-slate-200 p-4">
                                    @csrf
                                    @method('PUT')

                                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                                        <div>
                                            <p class="font-semibold text-slate-800">{{ $member->name }}</p>
                                            <p class="text-xs text-slate-400">Nomor absen {{ $member->attendanceLabel() }}</p>
                                        </div>
                                        @if ($assessment)
                                            <x-badge color="green">Nilai: {{ $assessment->score }}</x-badge>
                                        @else
                                            <x-badge color="yellow">Belum dinilai</x-badge>
                                        @endif
                                    </div>

                                    <div class="grid gap-3 sm:grid-cols-4">
                                        <div>
                                            <x-label value="Nilai (0-100)" />
                                            <x-input type="number" name="score" min="0" max="100" value="{{ old('score', $assessment?->score) }}" required />
                                            <x-input-error :messages="$errors->get('score')" class="mt-1" />
                                        </div>
                                        <div class="sm:col-span-3">
                                            <x-label value="Feedback untuk {{ $member->name }}" />
                                            <x-textarea name="feedback" rows="2" placeholder="Tuliskan masukan khusus untuk siswa ini...">{{ old('feedback', $assessment?->feedback) }}</x-textarea>
                                            <x-input-error :messages="$errors->get('feedback')" class="mt-1" />
                                        </div>
                                    </div>

                                    <div class="mt-3 flex items-center justify-between gap-3">
                                        <x-primary-button>{{ $assessment ? 'Perbarui Nilai' : 'Simpan Nilai Siswa' }}</x-primary-button>
                                        <a href="{{ route('guru.tasks.groups.show', [$row->task, $row->group]) }}" class="text-sm font-semibold text-status-primary hover:underline">
                                            Lihat detail pengerjaan
                                        </a>
                                    </div>
                                </form>
                            @empty
                                <p class="text-sm text-slate-400">Kelompok ini belum memiliki anggota.</p>
                            @endforelse
                        </div>
                    </details>
                @endforeach
            </div>

            {{ $rows->links() }}
        @endif
    </div>
</x-layouts.app>
