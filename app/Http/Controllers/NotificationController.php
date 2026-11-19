<?php

namespace App\Http\Controllers;

use App\Models\SubtaskCompletion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

/**
 * Endpoint notifikasi bersama untuk ketiga role (spesifikasi bagian 7 &
 * 31), diletakkan di luar prefix admin/guru/siswa - sama seperti
 * FileDownloadController - karena "notifikasi milik siapa" murni
 * ditentukan oleh notifiable_id/notifiable_type pada baris notifikasi itu
 * sendiri (dicek lewat findOrFailForUser()), bukan oleh role route.
 */
class NotificationController extends Controller
{
    /**
     * Daftar lengkap notifikasi milik user yang sedang login, terbaru
     * lebih dulu.
     */
    public function index(): View
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', ['notifications' => $notifications]);
    }

    /**
     * Tandai satu notifikasi sebagai dibaca, lalu arahkan ke URL tujuan
     * notifikasi tersebut (disimpan pada kolom data->url saat notifikasi
     * dibuat, lihat app/Notifications/*).
     */
    public function markRead(DatabaseNotification $notification): RedirectResponse
    {
        $this->authorizeOwnership($notification);

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return redirect($this->notificationUrl($notification));
    }

    /**
     * Tandai seluruh notifikasi milik user sebagai dibaca (dipanggil dari
     * tombol "Tandai semua dibaca" pada dropdown notifikasi).
     */
    public function markAllRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back();
    }

    /**
     * Cegah user membaca/menandai notifikasi milik user lain lewat ID
     * langsung (spesifikasi bagian 25: "Prevent IDOR").
     */
    private function authorizeOwnership(DatabaseNotification $notification): void
    {
        abort_unless($notification->notifiable_id === auth()->id()
            && $notification->notifiable_type === auth()->user()::class, 403);
    }

    private function notificationUrl(DatabaseNotification $notification): string
    {
        if (($notification->data['type'] ?? null) !== 'subtask_proof_submitted') {
            return $notification->data['url'] ?? url()->previous();
        }

        $completion = isset($notification->data['completion_id'])
            ? SubtaskCompletion::query()->with(['subtask.task', 'group', 'student', 'completedBy'])->find($notification->data['completion_id'])
            : $this->legacyProofCompletion($notification);

        if (
            ! $completion
            || $completion->status !== SubtaskCompletion::STATUS_PENDING
            || ! $completion->subtask?->task
            || $completion->subtask->task->teacher_id !== auth()->id()
        ) {
            return $notification->data['url'] ?? url()->previous();
        }

        $task = $completion->subtask->task;
        $student = $completion->student ?? $completion->completedBy;
        $url = $completion->group
            ? route('guru.tasks.groups.show', [$task, $completion->group])
            : ($student
                ? route('guru.tasks.students.show', [$task, $student])
                : route('guru.tasks.show', $task));

        return $url.'#subtask-completion-'.$completion->id;
    }

    private function legacyProofCompletion(DatabaseNotification $notification): ?SubtaskCompletion
    {
        $path = parse_url((string) ($notification->data['url'] ?? ''), PHP_URL_PATH);

        if (! is_string($path) || ! preg_match('~/guru/tugas/(\d+)(?:/kelompok/(\d+))?/?$~', $path, $matches)) {
            return null;
        }

        $taskId = (int) $matches[1];
        $groupId = isset($matches[2]) ? (int) $matches[2] : null;
        $message = (string) ($notification->data['message'] ?? '');

        return SubtaskCompletion::query()
            ->with(['subtask', 'group', 'student', 'completedBy'])
            ->where('status', SubtaskCompletion::STATUS_PENDING)
            ->whereHas('subtask', fn ($query) => $query->where('task_id', $taskId))
            ->when(
                $groupId !== null,
                fn ($query) => $query->where('group_id', $groupId),
                fn ($query) => $query->whereNull('group_id')
            )
            ->where('created_at', '<=', $notification->created_at->copy()->addMinute())
            ->orderByDesc('created_at')
            ->get()
            ->first(function (SubtaskCompletion $completion) use ($message): bool {
                $recipient = $completion->group?->name
                    ?? $completion->student?->name
                    ?? $completion->completedBy?->name;

                return $recipient !== null
                    && str_contains($message, $recipient)
                    && str_contains($message, '"'.$completion->subtask->title.'"');
            });
    }
}
