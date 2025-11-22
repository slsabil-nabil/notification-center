<?php

namespace Slsabil\NotificationCenter\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Slsabil\NotificationCenter\Models\NotificationRecipient;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = NotificationRecipient::with('notification')
            ->where('user_id', Auth::id())
            ->latest('id')
            ->paginate(20);

        return view('notification-center::index', compact('notifications'));
    }

    public function markAsRead(NotificationRecipient $recipient)
    {
        abort_if($recipient->user_id !== Auth::id(), 403);

        if (is_null($recipient->read_at)) {
            $recipient->update(['read_at' => now()]);
        }

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Auth::user()
            ->notificationRecipients()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function menu(Request $r)
    {
        $page = max(1, (int)$r->integer('page', 1));
        $per  = 10;

        if ($r->boolean('count_only')) {
            $unread = NotificationRecipient::where('user_id', Auth::id())
                ->whereNull('read_at')
                ->count();

            return response()->json(['unread_count' => $unread]);
        }

        $onlyUnread = $r->input('only') === 'unread';

        $q = NotificationRecipient::with('notification')
            ->where('user_id', Auth::id())
            ->when($onlyUnread, fn($qq) => $qq->whereNull('read_at'))
            ->orderByDesc('id');

        $p = $q->paginate($per, ['*'], 'page', $page);

        $items = [];
        foreach ($p->items() as $rec) {
            $n = $rec->notification;
            $data = $n->data ?? [];

            $url = $data['goto']
                ?? $data['target']
                ?? route('notifications.index');

            $items[] = [
                'id'             => $rec->id,
                'title'          => $n->title_localized ?? ($n->title ?? 'Notification'),
                'body'           => $n->body_localized ?? ($n->body ?? ''),
                'url'            => $url,
                'read_at'        => optional($rec->read_at)?->toIso8601String(),
                'created_at'     => optional($n->created_at)?->toIso8601String(),
                'requires_action'=> (bool)($n->requires_action ?? false),
            ];
        }

        $unread = NotificationRecipient::where('user_id', Auth::id())
            ->whereNull('read_at')->count();

        return response()->json([
            'unread_count' => $unread,
            'items'        => $items,
            'next_page'    => $p->hasMorePages() ? $p->currentPage() + 1 : null,
        ]);
    }
}
