@props(['comments', 'action'])

@php
    $palette = ['bg-blue-600', 'bg-emerald-600', 'bg-amber-500', 'bg-rose-500', 'bg-cyan-600', 'bg-violet-600'];
@endphp

<div class="rounded-2xl border border-slate-100 bg-white p-4 shadow-sm sm:p-6">
    <h3 class="mb-4 text-base font-bold text-slate-900">Diskusi Kelompok</h3>

    @if ($comments->isEmpty())
        <p class="py-6 text-center text-sm text-slate-400">Belum ada komentar. Jadilah yang pertama menulis.</p>
    @else
        <ul class="max-h-96 space-y-4 overflow-y-auto">
            @foreach ($comments as $comment)
                @php
                    $isGuru = $comment->user?->isGuru();
                    $initials = collect(explode(' ', $comment->user?->name ?? '?'))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('');
                    $avatarColor = $isGuru ? 'bg-indigo-600' : $palette[$comment->user_id % count($palette)];
                @endphp
                <li class="flex items-start gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ $avatarColor }} text-xs font-semibold text-white">
                        {{ $initials }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm">
                            <span class="font-semibold text-slate-800">{{ $comment->user?->name ?? 'Pengguna telah dihapus' }}</span>
                            <span class="ml-1.5 text-xs text-slate-400">{{ $isGuru ? 'Guru' : 'Siswa' }} &middot; {{ $comment->created_at->diffForHumans() }}</span>
                        </p>
                        <p class="mt-0.5 whitespace-pre-line text-sm text-slate-600">{{ $comment->comment }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    <div class="mt-4 border-t border-slate-100 pt-4">
        <form method="POST" action="{{ $action }}" class="flex items-end gap-2">
            @csrf
            <div class="min-w-0 flex-1">
                <textarea
                    name="comment"
                    placeholder="Tulis komentar baru..."
                    rows="2"
                    required
                    class="block w-full resize-y rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-800 shadow-sm transition placeholder:text-slate-400 hover:border-slate-300 hover:bg-white focus:border-status-primary focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100"
                ></textarea>
            </div>
            <button type="submit" aria-label="Kirim komentar" title="Kirim komentar" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-status-primary text-white shadow-sm transition hover:bg-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3.4 20.6l17.6-8.6-17.6-8.6L3.3 10 15 12l-11.7 2z"/>
                </svg>
            </button>
        </form>
        <x-input-error :messages="$errors->get('comment')" class="mt-1" />
    </div>
</div>
