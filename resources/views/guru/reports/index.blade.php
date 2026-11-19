<x-layouts.app :title="$individual ? 'Laporan Individu' : 'Laporan Kelompok'" role="guru">
    @php
        $exportRoute = $individual ? 'guru.reports.individual.export' : 'guru.reports.groups.export';
        $printRoute = $individual ? 'guru.reports.individual.print' : 'guru.reports.groups.print';
        $indexRoute = $individual ? 'guru.reports.individual' : 'guru.reports.groups';
    @endphp

    <div class="space-y-4">
        <div class="panel-card flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-6">
            <div>
                <h3 class="text-lg font-semibold text-slate-900">{{ $individual ? 'Laporan Individu' : 'Laporan Kelompok' }}</h3>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $individual ? 'Rekap progres dan penilaian setiap siswa pada tugas individu.' : 'Rekap progres dan penilaian anggota kelompok pada tugas kelompok.' }}
                </p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route($exportRoute, request()->query()) }}">
                    <x-primary-button type="button" class="!bg-status-success shrink-0">Export Excel (XLSX)</x-primary-button>
                </a>
                <a href="{{ route($printRoute, request()->query()) }}">
                    <x-primary-button type="button" class="shrink-0">Export PDF</x-primary-button>
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route($indexRoute) }}" class="panel-card flex flex-wrap items-end gap-2 p-4">
            <div class="w-full sm:w-52">
                <x-label for="task_id" value="Tugas" class="mb-1 text-xs" />
                <x-select name="task_id" id="task_id">
                    <option value="">Semua Tugas</option>
                    @foreach ($taskOptions as $option)
                        <option value="{{ $option->id }}" @selected($filters['task_id'] == $option->id)>{{ $option->title }}</option>
                    @endforeach
                </x-select>
            </div>

            <div class="w-full sm:w-44">
                <x-label for="class_id" value="Kelas" class="mb-1 text-xs" />
                <x-select name="class_id" id="class_id">
                    <option value="">Semua Kelas</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" @selected($filters['class_id'] == $class->id)>{{ $class->name }}</option>
                    @endforeach
                </x-select>
            </div>

            @unless ($individual)
                <div class="w-full sm:w-44">
                    <x-label for="group_id" value="Kelompok" class="mb-1 text-xs" />
                    <x-select name="group_id" id="group_id">
                        <option value="">Semua Kelompok</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}" @selected($filters['group_id'] == $group->id)>{{ $group->name }}</option>
                        @endforeach
                    </x-select>
                </div>
            @endunless

            <div class="w-full sm:w-40">
                <x-label for="start_date" value="Dari Deadline" class="mb-1 text-xs" />
                <x-input type="date" name="start_date" id="start_date" value="{{ $filters['start_date'] }}" />
            </div>

            <div class="w-full sm:w-40">
                <x-label for="end_date" value="Sampai Deadline" class="mb-1 text-xs" />
                <x-input type="date" name="end_date" id="end_date" value="{{ $filters['end_date'] }}" />
            </div>

            <x-primary-button type="submit" class="shrink-0">Terapkan</x-primary-button>

            @if (collect($filters)->filter()->isNotEmpty())
                <a href="{{ route($indexRoute) }}" class="inline-flex items-center rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-100">
                    Reset
                </a>
            @endif
        </form>

        @if (empty($rows))
            <div class="rounded-xl border border-gray-200 bg-white p-8 text-center text-sm text-gray-400 shadow-sm">
                Tidak ada data yang cocok dengan filter ini.
            </div>
        @else
            <div class="panel-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3">No Absen</th>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Mapel</th>
                                <th class="px-4 py-3">Tugas</th>
                                <th class="px-4 py-3">Kelompok</th>
                                <th class="px-4 py-3">Nilai</th>
                                <th class="px-4 py-3">Progres</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Feedback</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($rows as $row)
                                <tr>
                                    <td class="px-4 py-3 text-center text-slate-500">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 text-center text-slate-600">{{ $row['attendance'] }}</td>
                                    <td class="px-4 py-3 font-medium text-slate-800">{{ $row['name'] }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $row['subject'] }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $row['task'] }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $row['group'] }}</td>
                                    <td class="px-4 py-3 text-center text-slate-600">{{ $row['score'] }}</td>
                                    <td class="px-4 py-3 text-center text-slate-600">{{ $row['progress'] }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $row['status'] }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $row['feedback'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
