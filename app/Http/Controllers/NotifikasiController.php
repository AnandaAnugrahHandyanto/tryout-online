<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index(Request $r)
    {
        $user = auth()->user();

        $baseQuery = Notifikasi::where('user_id', $user->id);
        if ($r->input('tipe') && $r->input('tipe') !== 'semua') {
            $baseQuery->where('tipe', $r->input('tipe'));
        }

        // Highlight only the most recent (or selected one) notification
        $notif = $baseQuery->orderBy('created_at', 'desc')->limit(1)->first();

        $counts = [
            'semua' => Notifikasi::where('user_id', $user->id)->count(),
            'nilai' => Notifikasi::where('user_id', $user->id)->where('tipe', 'nilai')->count(),
            'jadwal' => Notifikasi::where('user_id', $user->id)->where('tipe', 'jadwal')->count(),
            'pencapaian' => Notifikasi::where('user_id', $user->id)->where('tipe', 'pencapaian')->count(),
        ];
        $counts['peringatan'] = Notifikasi::where('user_id', $user->id)->where('tipe', 'peringatan')->count();

        return view('notifikasi.index', compact('notif', 'counts'));
    }

    public function markRead(Notifikasi $notifikasi)
    {
        if ($notifikasi->user_id !== auth()->id()) abort(403);
        $notifikasi->update(['sudah_dibaca' => true]);
        return back();
    }

    public function markAllRead()
    {
        Notifikasi::where('user_id', auth()->id())->where('sudah_dibaca', false)->update(['sudah_dibaca' => true]);
        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
