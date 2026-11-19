<x-layouts.app title="Penilaian Individu" role="guru">
    <div class="space-y-4">

        <div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm sm:p-6">
            <h3 class="text-xl font-bold text-slate-900">Penilaian Individu</h3>
            <p class="mt-1 text-sm text-slate-500">
                Siswa yang menerima tugas individu dan sudah mengumpulkan tugas untuk ditinjau atau dinilai.
            </p>
        </div>

        <form method="GET" action="{{ route('guru.assessments.individual') }}" class="flex flex-wrap gap-2">
            <div class="w-full max-w-xs">
                <x-input type="text" name="search" value="{{ $search }}" placeholder="Cari judul tugas atau siswa..." />
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
                <a href="{{ route('guru.assessments.individual') }}" class="inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-500 hover:bg-gray-100">
                    Reset
                </a>
            @endif
        </form>

        @if ($rows->isEmpty())
            <div class="rounded-2xl border border-slate-100 bg-white p-8 text-center text-sm text-slate-400 shadow-sm">
                Belum ada siswa yang perlu dinilai. Tugas individu akan muncul di sini setelah dikumpulkan untuk review.
            </div>
        @else
            <div class="space-y-3">
                @foreach ($rows as $row)
                    <details class="group overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" @if ($row->status === \App\Models\TaskGroup::STATUS_SUBMITTED || $errors->any()) open @endif>
                        <summary class="flex cursor-pointer list-none flex-col gap-2 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $row->student?->name ?? 'Penerima tugas' }}</p>
                                <p class="text-xs text-slate-400">
                                    Individu &middot;
                                    {{ $row->task->title }}
                                    @if ($row->submitted_at)
                                        &middot; Dikumpulkan {{ $row->submitted_at->diffForHumans() }}
                                    @endif
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                @if ($row->status === \App\Models\TaskGroup::STATUS_COMPLETED)
                                    <x-badge color="green">Nilai: {{ $row->score }}</x-badge>
                                @else
                                    <x-badge color="yellow">Menunggu Review</x-badge>
                                @endif
                                <span class="text-xs text-slate-400 group-open:hidden">Klik untuk {{ $row->status === \App\Models\TaskGroup::STATUS_COMPLETED ? 'ubah nilai' : 'beri nilai' }}</span>
                            </div>
                        </summary>

                        <div class="border-t border-gray-100 px-5 py-4">
                            <a href="{{ route('guru.tasks.students.show', [$row->task, $row->student]) }}" class="inline-flex text-sm font-semibold text-status-primary hover:underline">
                                Review detail individu &rarr;
                            </a>
                        </div>
                    </details>
                @endforeach
            </div>

            {{ $rows->links() }}
        @endif
    </div>
</x-layouts.app>
