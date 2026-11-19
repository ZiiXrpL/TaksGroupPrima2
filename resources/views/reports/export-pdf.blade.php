<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>{{ $title }}</title>
<style>
    @page { margin: 14mm 8mm 18mm; }
    body { font-family: Helvetica, Arial, sans-serif; font-size: 8px; color: #1e293b; }
    .report-header { margin-bottom: 16px; text-align: center; }
    .school-logo { display: block; width: 64px; height: 48px; margin: 0 auto 5px; }
    h1 { font-size: 18px; margin: 0 0 3px; color: #312e81; text-align: center; }
    .subtitle { font-size: 10px; color: #64748b; margin: 0; text-align: center; }
    table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    th, td { border: 1px solid #cbd5e1; padding: 4px 3px; text-align: left; vertical-align: top; word-wrap: break-word; }
    th { background: #4f46e5; color: #fff; font-size: 7px; }
    td { font-size: 7px; }
    thead { display: table-header-group; }
    tr { page-break-inside: avoid; }
    .center { text-align: center; }
    .empty { color: #64748b; text-align: center; padding: 12px; }
</style>
</head>
<body>
    <header class="report-header">
        <img class="school-logo" src="{{ $logo }}" alt="Logo sekolah">
        <h1>{{ $title }}</h1>
        <div class="subtitle">{{ $subtitle }} &middot; Diekspor {{ $printedAt->translatedFormat('d F Y, H:i') }}</div>
    </header>

    @if (empty($rows))
        <p class="empty">Tidak ada data untuk filter yang dipilih.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width: 4%">No</th>
                    <th style="width: 8%">No Absen</th>
                    <th style="width: 16%">Nama</th>
                    <th style="width: 11%">Mapel</th>
                    <th style="width: 15%">Tugas</th>
                    <th style="width: 11%">Kelompok</th>
                    <th style="width: 6%">Nilai</th>
                    <th style="width: 7%">Progres</th>
                    <th style="width: 10%">Status</th>
                    <th style="width: 12%">Feedback</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        <td class="center">{{ $loop->iteration }}</td>
                        <td class="center">{{ $row['attendance'] }}</td>
                        <td>{{ $row['name'] }}</td>
                        <td>{{ $row['subject'] }}</td>
                        <td>{{ $row['task'] }}</td>
                        <td>{{ $row['group'] }}</td>
                        <td class="center">{{ $row['score'] }}</td>
                        <td class="center">{{ $row['progress'] }}</td>
                        <td>{{ $row['status'] }}</td>
                        <td>{{ $row['feedback'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
