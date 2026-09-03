<?php
// app/Http/Controllers/Admin/PengembalianController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PengembalianController extends Controller
{
    /** Denda per hari keterlambatan (Rp) */
    private const DENDA_PER_HARI = 5000;

    public function index(): View
    {
        // Peminjaman yang sedang berjalan & belum ada record pengembalian
        $sedangDipinjam = Peminjaman::with('user')
            ->where('status', 'dipinjam')
            ->whereDoesntHave('pengembalian')
            ->orderBy('tgl_kembali_plan')
            ->get();

        // Riwayat pengembalian yang sudah diproses
        $riwayat = Pengembalian::with(['peminjaman.user', 'petugas'])
            ->latest('tgl_kembali')
            ->paginate(10);

        return view('admin.pengembalian.index', compact('sedangDipinjam', 'riwayat'));
    }

    public function store(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        if ($peminjaman->pengembalian()->exists()) {
            return back()->with('error', 'Peminjaman ini sudah diproses pengembaliannya.');
        }

        $validated = $request->validate([
            'tgl_kembali'     => ['required', 'date'],
            'kondisi_kembali' => ['required', 'string', 'max:255'],
        ]);

        $terlambatHari = max(
            0,
            now()->parse($validated['tgl_kembali'])
                ->diffInDays($peminjaman->tgl_kembali_plan, false) * -1
        );

        $denda = $terlambatHari * self::DENDA_PER_HARI;

        DB::transaction(function () use ($peminjaman, $validated, $denda) {
            Pengembalian::create([
                'peminjaman_id'   => $peminjaman->id,
                'tgl_kembali'     => $validated['tgl_kembali'],
                'kondisi_kembali' => $validated['kondisi_kembali'],
                'denda'           => $denda,
                'petugas_id'      => Auth::id(),
            ]);

            $peminjaman->update(['status' => 'dikembalikan']);
        });

        return redirect()
            ->route('admin.pengembalian.index')
            ->with('success', 'Pengembalian berhasil diproses.'
                . ($denda > 0 ? " Denda: Rp" . number_format($denda, 0, ',', '.') : ''));
    }

    public function destroy(Pengembalian $pengembalian): RedirectResponse
    {
        DB::transaction(function () use ($pengembalian) {
            $pengembalian->peminjaman()->update(['status' => 'dipinjam']);
            $pengembalian->delete();
        });

        return back()->with('success', 'Data pengembalian dibatalkan.');
    }
}