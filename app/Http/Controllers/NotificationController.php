<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $query = AppNotification::where('company_id', auth()->user()->company_id);

        if ($request->filled('type')) $query->where('type', $request->input('type'));

        return view('admin.notifications.index', [
            'notifications' => $query->latest()->paginate(20)->withQueryString(),
            'unreadCount' => AppNotification::where('company_id', auth()->user()->company_id)->unread()->count(),
        ]);
    }

    public function markRead(AppNotification $notification): RedirectResponse
    {
        $notification->update(['is_read' => true]);
        return back();
    }

    public function markAllRead(): RedirectResponse
    {
        AppNotification::where('company_id', auth()->user()->company_id)->unread()->update(['is_read' => true]);
        return back()->with('status', 'Toutes les notifications marques comme lues.');
    }
}
