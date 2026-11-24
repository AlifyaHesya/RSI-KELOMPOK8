<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotifikasiController extends Controller
{
    public function index(): View
    {
        $notifikasi = Notifikasi::where('id_penerima', Auth::id())
            ->latest()
            ->paginate(15);

        return view('notifikasi.index', compact('notifikasi'));
    }

    public function tandaiDibaca(Notifikasi $notifikasi): RedirectResponse
    {
        abort_unless((int) $notifikasi->id_penerima === (int) Auth::id(), 403);

        $notifikasi->update(['sudah_dibaca' => true]);

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function tandaiSemuaDibaca(): RedirectResponse
    {
        Notifikasi::where('id_penerima', Auth::id())
            ->where('sudah_dibaca', false)
            ->update(['sudah_dibaca' => true]);

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
